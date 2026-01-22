<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Medicine;
use App\Services\VoucherService;
use App\Services\InvoiceService;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private VoucherService $voucherService,
        private InvoiceService $invoiceService,
        private ActivityService $activityService,
    ) {}

    public function form(Request $request): RedirectResponse|View
    {
        $cart = Cart::currentCart()->load('items.product', 'items.medicine');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        // Handle selected items
        $selectedItems = $cart->items;
        $selectedItemIds = [];
        
        // Prioritize old input (from failed validation)
        if ($request->hasSession() && $request->session()->hasOldInput('selected_items')) {
            $rawOld = $request->session()->getOldInput('selected_items');
            $selectedItemIds = json_decode($rawOld, true) ?? [];
        } 
        // Then check query parameter (normal flow)
        elseif ($request->has('selected_items')) {
            $selectedItemIds = json_decode($request->input('selected_items'), true) ?? [];
        } 
        // Default: Select all items if nothing specified
        else {
            $selectedItemIds = $selectedItems->pluck('id')->toArray();
        }

        // Filter items based on IDs
        if (!empty($selectedItemIds)) {
            $selectedItems = $cart->items->whereIn('id', $selectedItemIds);
        }

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        // Calculate subtotal for selected items
        $subtotal = $selectedItems->sum(function ($item) {
            $isMedicine = (bool) $item->medicine_id;
            $price = $isMedicine ? $item->medicine?->price : ($item->product?->discount_price ?? $item->product?->price);
            return $price * $item->quantity;
        });

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber();

        // Use 'checkout.form_new' as per implementation plan, or maybe we should renamed it later.
        // For now sticking to what worked in CheckoutControllerNew
        return view('checkout.form_new', [
            'selectedItems' => $selectedItems,
            'subtotal' => $subtotal,
            'selectedItemIds' => $selectedItemIds,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }

    public function process(Request $request): RedirectResponse|View
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'selected_items' => 'required',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'voucher_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cart = Cart::currentCart()->load('items.product', 'items.medicine');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Get selected items
        $selectedItemIds = json_decode($request->input('selected_items'), true) ?? [];
       
        if (empty($selectedItemIds)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        $selectedItems = $cart->items->whereIn('id', $selectedItemIds);
      
        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Produk yang dipilih tidak valid.');
        }

        try {
            $order = DB::transaction(function () use ($selectedItems, $user, $request): Order {
                // Lock items to prevent race condition (overselling)
                $productIds = $selectedItems->whereNotNull('product_id')->pluck('product_id')->toArray();
                $medicineIds = $selectedItems->whereNotNull('medicine_id')->pluck('medicine_id')->toArray();

                $products = !empty($productIds) ? Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id') : collect();
                $medicines = !empty($medicineIds) ? Medicine::whereIn('id', $medicineIds)->lockForUpdate()->get()->keyBy('id') : collect();

                // Re-validate stock with locked rows
                foreach ($selectedItems as $item) {
                    if ($item->product_id) {
                        $product = $products->get($item->product_id);
                        if (! $product || $product->status !== 'published') {
                            throw new \RuntimeException('Produk '.($product->name ?? 'Tidak Dikenal').' tidak tersedia.');
                        }
                        if (! $product->isInStock($item->quantity)) {
                            throw new \RuntimeException('Stok tidak mencukupi untuk '.$product->name);
                        }
                    } elseif ($item->medicine_id) {
                        $medicine = $medicines->get($item->medicine_id);
                        if (! $medicine) {
                            throw new \RuntimeException('Obat tidak ditemukan.');
                        }
                        // Check medicine stock if available
                        if (method_exists($medicine, 'isInStock') && !$medicine->isInStock($item->quantity)) {
                            throw new \RuntimeException('Stok tidak mencukupi untuk '.$medicine->name);
                        }
                    }
                }
                
                // Calculate subtotal for selected items
                $subtotal = $selectedItems->sum(function ($item) use ($products, $medicines) {
                    if ($item->product_id) {
                        $product = $products->get($item->product_id);
                        $price = $product->discount_price ?? $product->price;
                        return $price * $item->quantity;
                    } elseif ($item->medicine_id) {
                        $medicine = $medicines->get($item->medicine_id);
                        return $medicine->price * $item->quantity;
                    }
                    return 0;
                });

                // Handle voucher
                $voucherCode = $request->string('voucher_code');
                $voucherDiscount = 0;
                $voucher = null;

                if ($voucherCode !== '') {
                    $voucherResult = $this->voucherService->validateWithLock((string) $voucherCode, $user, $subtotal, 0);
                    if ($voucherResult['valid']) {
                        $voucher = $voucherResult['voucher'];
                        $voucherDiscount = $voucherResult['discount'];
                    }
                }
                
                // Calculate total (no shipping cost for pickup)
                $total = max(0, $subtotal - $voucherDiscount);

                // Set payment status based on method
                $paymentStatus = 'unpaid';
                $orderStatus = 'pending_payment';
                
                // Generate invoice number
                $invoiceNumber = $this->invoiceService->generateInvoiceNumber();
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'invoice_number' => $invoiceNumber,
                    'order_number' => $invoiceNumber, // Explicitly set order_number same as invoice
                    'status' => $orderStatus,
                    'payment_status' => $paymentStatus,
                    'payment_gateway' => null, // Defer selection to payment page
                    'subtotal' => $subtotal,
                    'tax' => 0,
                    'shipping_cost' => 0,
                    'shipping_weight' => 0,
                    'voucher_code' => $voucher?->code,
                    'voucher_discount' => $voucherDiscount,
                    'total' => $total,
                    'shipping_address' => 'Pickup at Apotek Parahyangan PVJ',
                    'shipping_city' => 'Bandung',
                    'shipping_province' => 'Jawa Barat',
                    'shipping_postal_code' => '40162',
                    'phone' => $request->string('phone'),
                    'notes' => $request->string('notes'),
                    'payment_expired_at' => now()->addHours(24),
                    'metadata' => ['selected_cart_items' => $selectedItems], // Store selected items logic
                ]);

                // Create order items for selected items only
                foreach ($selectedItems as $item) {
                    if ($item->product_id) {
                        $product = $products->get($item->product_id);
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'product_price' => $product->discount_price ?? $product->price,
                            'quantity' => $item->quantity,
                            'subtotal' => ($product->discount_price ?? $product->price) * $item->quantity,
                        ]);

                        // Decrement stock
                        $product->decrement('stock', $item->quantity);
                    } elseif ($item->medicine_id) {
                        $medicine = $medicines->get($item->medicine_id);
                        OrderItem::create([
                            'order_id' => $order->id,
                            'medicine_id' => $medicine->id,
                            'product_name' => $medicine->name,
                            'product_price' => $medicine->price,
                            'quantity' => $item->quantity,
                            'subtotal' => $medicine->price * $item->quantity,
                        ]);

                        if (method_exists($medicine, 'decrementStock')) {
                            $medicine->decrementStock($item->quantity);
                        }
                    }
                }

                if ($voucher) {
                    $this->voucherService->recordUsage($voucher, $user, $order);
                }
                
                $this->activityService->addOrderActivity($user, $order, 'created');

                return $order;
            });
            
            return redirect()->route('checkout.payment', $order);

        } catch (\Exception $e) {
            return redirect()->route('checkout.form')
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function payOffline(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        // Update order to manual payment
        $order->update([
            'payment_gateway' => 'manual',
            // We don't mark as paid yet, just that they chose manual payment at checkout/pharmacy
        ]);

        return redirect()->route('orders.index')->with('success', 'Metode pembayaran apotek dipilih. Silakan lakukan pembayaran di kasir.');
    }

    public function payment(Order $order): View|RedirectResponse
    {
        $this->authorize('view', $order);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order);
        }

        // Ensure gateway is set to midtrans if it was null or manual
        if ($order->payment_gateway !== 'midtrans') {
            $order->update(['payment_gateway' => 'midtrans']);
        }

        if (!$order->snap_token) {
            try {
                $paymentData = $this->gateway->createTransaction($order);
                
                $order->update([
                    'snap_token' => $paymentData['snap_token'],
                    'payment_url' => $paymentData['redirect_url'] ?? null,
                    'payment_gateway' => 'midtrans', // Ensure gateway is set to midtrans when paying online
                ]);
                
                $order->refresh();
                
            } catch (\Exception $e) {
                return redirect()->route('checkout.form')
                    ->with('error', 'Gagal menghubungkan ke layanan pembayaran: ' . $e->getMessage());
            }
        }

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

        return view('checkout.confirmation', [
            'order' => $order->load('items'),
        ]);
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
}
