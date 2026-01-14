<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderCreatedMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Services\RajaOngkirService;
use App\Services\VoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CheckoutController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private VoucherService $voucherService,
        private RajaOngkirService $rajaOngkir,
    ) {}

    public function form(): RedirectResponse|View
    {
        $cart = Cart::currentCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        $provinces = Province::query()
            ->orderBy('name')
            ->get(['code', 'name']);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $savedAddresses = $user?->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('checkout.form', [
            'cart' => $cart,
            'shipping_cost' => $cart->getShippingCost(),
            'total' => $cart->getTotal(),
            'provinces' => $provinces,
            'savedAddresses' => $savedAddresses,
        ]);
    }

    public function process(CheckoutRequest $request): RedirectResponse|View
    {
        $cart = Cart::currentCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Issue 3 Fix: Check for existing pending order to prevent double-submit (atomic check with lock)
        $existingPaymentUrl = DB::transaction(function () use ($user): ?string {
            $existingOrder = Order::where('user_id', $user->id)
                ->where('payment_status', 'unpaid')
                ->where('status', 'pending_payment')
                ->where('payment_expired_at', '>', now())
                ->whereNotNull('payment_url')
                ->lockForUpdate()
                ->first();

            return $existingOrder?->payment_url;
        });

        if ($existingPaymentUrl) {
            return redirect()->away($existingPaymentUrl);
        }

        foreach ($cart->items as $item) {
            if (! $item->product || $item->product->status !== 'published') {
                return redirect()->route('cart.index')->with('error', 'Ada produk yang tidak tersedia.');
            }
            if (! $item->product->isInStock($item->quantity)) {
                return redirect()->route('cart.index')->with('error', 'Stok tidak mencukupi untuk '.$item->product->name);
            }
        }

        $province = Province::query()
            ->where('code', (string) $request->string('province_code'))
            ->first();
        $city = City::query()
            ->where('code', (string) $request->string('city_code'))
            ->first();
        $district = District::query()
            ->where('code', (string) $request->string('district_code'))
            ->first();
        $village = Village::query()
            ->where('code', (string) $request->string('village_code'))
            ->first();

        $this->ensureLocationHierarchy($province, $city, $district, $village);

        // Issue 1 Fix: Calculate shipping cost server-side via RajaOngkir
        $defaultWeight = config('rajaongkir.default_weight', 200);
        $totalWeight = $cart->items->sum(fn ($item) => ($item->product?->weight ?? $defaultWeight) * $item->quantity);

        $shippingCost = $this->calculateServerSideShippingCost(
            $request->integer('rajaongkir_destination_id'),
            $totalWeight,
            (string) $request->string('shipping_courier'),
            (string) $request->string('shipping_service'),
        );

        if ($shippingCost === null) {
            return redirect()->route('checkout.form')
                ->withInput()
                ->withErrors(['shipping_service' => 'Layanan pengiriman tidak valid. Silakan pilih ulang.']);
        }

        try {
            $order = DB::transaction(function () use ($cart, $user, $request, $city, $province, $district, $village, $shippingCost, $totalWeight): Order {
                // Lock products to prevent race condition (overselling)
                $productIds = $cart->items->pluck('product_id')->toArray();
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                // Re-validate stock with locked rows
                foreach ($cart->items as $item) {
                    $product = $products->get($item->product_id);
                    if (! $product || $product->status !== 'published') {
                        throw new \RuntimeException('Produk '.$item->product->name.' tidak tersedia.');
                    }
                    if (! $product->isInStock($item->quantity)) {
                        throw new \RuntimeException('Stok tidak mencukupi untuk '.$product->name);
                    }
                }

                $subtotal = $cart->getSubtotal();

                // Handle voucher (with lock to prevent race condition)
                $voucherCode = $request->string('voucher_code');
                $voucherDiscount = 0;
                $voucher = null;

                if ($voucherCode !== '') {
                    $voucherResult = $this->voucherService->validateWithLock((string) $voucherCode, $user, $subtotal, $shippingCost);
                    if ($voucherResult['valid']) {
                        $voucher = $voucherResult['voucher'];
                        $voucherDiscount = $voucherResult['discount'];
                    }
                }

                // Calculate total: for free_shipping, discount is applied to shipping
                // For other types, discount is applied to subtotal
                if ($voucher?->type === 'free_shipping') {
                    $total = $subtotal + max(0, $shippingCost - $voucherDiscount);
                } else {
                    $total = max(0, $subtotal - $voucherDiscount) + $shippingCost;
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending_payment',
                    'payment_status' => 'unpaid',
                    'payment_gateway' => config('cart.default_gateway', 'midtrans'),
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'voucher_code' => $voucher?->code,
                    'voucher_discount' => $voucherDiscount,
                    'shipping_courier' => $request->string('shipping_courier'),
                    'shipping_service' => $request->string('shipping_service'),
                    'shipping_etd' => $request->string('shipping_etd'),
                    'shipping_weight' => $totalWeight,
                    'rajaongkir_destination_id' => $request->integer('rajaongkir_destination_id'),
                    'total' => $total,
                    'shipping_address' => $request->string('shipping_address'),
                    'shipping_city' => $city?->name ?? $request->string('shipping_city'),
                    'shipping_district' => $district?->name ?? $request->string('shipping_district'),
                    'shipping_village' => $village?->name ?? $request->string('shipping_village'),
                    'shipping_province' => $province?->name ?? $request->string('shipping_province'),
                    'shipping_postal_code' => $request->string('shipping_postal_code'),
                    'phone' => $request->string('phone'),
                    'notes' => $request->string('notes'),
                    'province_code' => $province?->code ?? $request->string('province_code'),
                    'city_code' => $city?->code ?? $request->string('city_code'),
                    'district_code' => $district?->code ?? $request->string('district_code'),
                    'village_code' => $village?->code ?? $request->string('village_code'),
                    'payment_expired_at' => now()->addHours((int) config('cart.payment_expiry_hours', 24)),
                ]);

                foreach ($cart->items as $item) {
                    $product = $products->get($item->product_id);

                    $orderItem = new OrderItem([
                        'product_id' => $item->product_id,
                        'product_name' => $product->name,
                        'product_price' => $product->getCurrentPrice(),
                        'quantity' => $item->quantity,
                        'subtotal' => $item->getSubtotal(),
                    ]);

                    $order->items()->save($orderItem);

                    // Decrement from locked product instance
                    $product->decrement('stock', $item->quantity);
                }

                $cart->clear();

                // Record voucher usage
                if ($voucher !== null && $voucherDiscount > 0) {
                    $this->voucherService->apply($voucher, $order, $user, $voucherDiscount);
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // Save address if requested and under limit
        if ($request->boolean('save_new_address') && $user->addresses()->count() < UserAddress::MAX_ADDRESSES_PER_USER) {
            $isFirstAddress = $user->addresses()->count() === 0;
            $user->addresses()->create([
                'recipient_name' => $user->name,
                'phone' => $request->string('phone'),
                'address' => $request->string('shipping_address'),
                'province_code' => $province?->code ?? $request->string('province_code'),
                'province_name' => $province?->name ?? $request->string('shipping_province'),
                'city_code' => $city?->code ?? $request->string('city_code'),
                'city_name' => $city?->name ?? $request->string('shipping_city'),
                'district_code' => $district?->code ?? $request->string('district_code'),
                'district_name' => $district?->name ?? $request->string('shipping_district'),
                'village_code' => $village?->code ?? $request->string('village_code'),
                'village_name' => $village?->name ?? $request->string('shipping_village'),
                'postal_code' => $request->string('shipping_postal_code'),
                'is_default' => $isFirstAddress,
            ]);
        }

        $payment = $this->gateway->createTransaction($order);

        $order->update([
            'snap_token' => $payment['snap_token'] ?? null,
            'payment_url' => $payment['redirect_url'] ?? null,
            'payment_external_id' => $order->order_number,
        ]);

        if (empty($payment['snap_token'])) {
            return redirect()->route('checkout.form')->with('error', 'Gagal memuat pembayaran. Silakan coba lagi.');
        }

        // Send order created email to customer
        try {
            Mail::to($user->email)->send(new OrderCreatedMail($order->load('items', 'user')));
        } catch (\Throwable) {
            // Silently fail - don't block checkout if email fails
        }

        return view('checkout.payment', [
            'order' => $order->fresh(),
            'snapToken' => $payment['snap_token'] ?? null,
            'snapUrl' => config('midtrans.snap_url'),
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function payment(Order $order): View
    {
        $this->authorize('view', $order);

        return view('checkout.payment', [
            'order' => $order,
            'snapToken' => $order->snap_token,
            'snapUrl' => config('midtrans.snap_url'),
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function confirmation(Order $order): View
    {
        $this->authorize('view', $order);

        if ($order->payment_status === 'unpaid') {
            $this->syncPaymentStatus($order);
            $order->refresh();
        }

        return view('checkout.confirmation', ['order' => $order]);
    }

    public function pending(Order $order): View
    {
        $this->authorize('view', $order);

        return view('checkout.pending', ['order' => $order]);
    }

    public function error(): View
    {
        return view('checkout.error');
    }

    private function ensureLocationHierarchy(?Province $province, ?City $city, ?District $district, ?Village $village): void
    {
        $errors = [];

        if ($province && $city && $city->province_code !== $province->code) {
            $errors['city_code'] = 'Kota atau kabupaten tidak sesuai dengan provinsi yang dipilih.';
        }

        if ($city && $district && $district->city_code !== $city->code) {
            $errors['district_code'] = 'Kecamatan tidak sesuai dengan kota atau kabupaten yang dipilih.';
        }

        if ($district && $village && $village->district_code !== $district->code) {
            $errors['village_code'] = 'Kelurahan atau desa tidak sesuai dengan kecamatan yang dipilih.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function syncPaymentStatus(Order $order): void
    {
        $status = $this->gateway->getTransactionStatus($order->order_number);

        if (! $status) {
            return;
        }

        $transactionStatus = $status['transaction_status'] ?? null;
        $fraudStatus = $status['fraud_status'] ?? null;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($fraudStatus === 'challenge') {
                $order->payment_status = 'unpaid';
                $order->status = 'pending_payment';
            } else {
                $order->payment_status = 'paid';
                $order->status = $order->status === 'pending_payment' ? 'confirmed' : $order->status;
                $order->paid_at = now();
            }
        } elseif ($transactionStatus === 'pending') {
            $order->payment_status = 'unpaid';
            $order->status = 'pending_payment';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $order->payment_status = 'failed';
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->restoreStock();
        } elseif ($transactionStatus === 'expire') {
            $order->payment_status = 'expired';
            $order->status = 'expired';
            $order->cancelled_at = now();
            $order->restoreStock();
        }

        $order->payment_type = $status['payment_type'] ?? $order->payment_type;
        $order->save();
    }

    /**
     * Calculate shipping cost server-side via RajaOngkir API.
     * Returns null if the selected courier/service is not found.
     */
    private function calculateServerSideShippingCost(int $destinationId, int $weight, string $courier, string $service): ?int
    {
        if ($destinationId <= 0 || $weight <= 0 || $courier === '' || $service === '') {
            return null;
        }

        $originId = $this->rajaOngkir->getOriginId();
        if (! $originId) {
            return null;
        }

        $shippingOptions = $this->rajaOngkir->calculateCost($originId, $destinationId, $weight, [$courier]);

        if ($shippingOptions->isEmpty()) {
            return null;
        }

        // Find the exact service selected by user
        $selectedOption = $shippingOptions->first(function ($option) use ($courier, $service) {
            return strtolower($option['courier_code']) === strtolower($courier)
                && strtolower($option['service']) === strtolower($service);
        });

        if (! $selectedOption) {
            return null;
        }

        return (int) $selectedOption['cost'];
    }
}
