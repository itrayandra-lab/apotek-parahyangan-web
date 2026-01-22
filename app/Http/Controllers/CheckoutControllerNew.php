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

class CheckoutControllerNew extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private VoucherService $voucherService,
        private InvoiceService $invoiceService,
        private ActivityService $activityService,
    ) {}

    public function form(Request $request): RedirectResponse|View
    {
        \Log::info('CheckoutControllerNew::form called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);

        $cart = Cart::currentCart()->load('items.product', 'items.medicine');

        \Log::info('Cart loaded', [
            'cart_id' => $cart->id,
            'items_count' => $cart->items->count()
        ]);

        if ($cart->items->isEmpty()) {
            \Log::warning('Cart is empty, redirecting to cart.index');
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        // Create invoice number if not passed (view handles it, but good to have)
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber();

        // Handle selected items
        $selectedItems = $cart->items;
        $selectedItemIds = [];
        
        // Prioritize old input (from failed validation)
        if ($request->hasSession() && $request->session()->hasOldInput('selected_items')) {
            $rawOld = $request->session()->getOldInput('selected_items');
            $selectedItemIds = json_decode($rawOld, true) ?? [];
            \Log::info('Selected items restored from session (validation error)', ['selected_ids' => $selectedItemIds]);
        } 
        // Then check query parameter (normal flow)
        elseif ($request->has('selected_items')) {
            $selectedItemIds = json_decode($request->input('selected_items'), true) ?? [];
            \Log::info('Selected items from request query', ['selected_ids' => $selectedItemIds]);
        } 
        // Default: Select all items if nothing specified
        else {
            $selectedItemIds = $selectedItems->pluck('id')->toArray();
        }

        // Filter items based on IDs
        if (!empty($selectedItemIds)) {
            $selectedItems = $cart->items->whereIn('id', $selectedItemIds);
            \Log::info('Filtered selected items', ['count' => $selectedItems->count()]);
        }

        if ($selectedItems->isEmpty()) {
            \Log::warning('No items selected, redirecting to cart.index');
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        // Calculate subtotal for selected items
        $subtotal = $selectedItems->sum(function ($item) {
            $isMedicine = (bool) $item->medicine_id;
            $price = $isMedicine ? $item->medicine?->price : ($item->product?->discount_price ?? $item->product?->price);
            return $price * $item->quantity;
        });

        \Log::info('Rendering checkout form', [
            'selected_items_count' => $selectedItems->count(),
            'subtotal' => $subtotal,
            'items_detail' => $selectedItems->map(fn($item) => [
                'cart_item_id' => $item->id,
                'product_id' => $item->product_id,
                'medicine_id' => $item->medicine_id,
                'name' => $item->product?->name ?? $item->medicine?->name,
                'quantity' => $item->quantity,
            ])
        ]);

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber();

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
            // 'payment_method' => 'required|in:online,counter', // Removed
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

        // Check for existing pending order to prevent double-submit
        /* 
        // Logic ini didisable karena memblokir user membuat order baru jika ada order lama yang belum dibayar.
        // Double-submit sebaiknya dicegah di frontend.
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
        */

        try {
            $order = DB::transaction(function () use ($selectedItems, $user, $request): Order {
                \Log::info('Starting order creation transaction', [
                    'user_id' => $user->id,
                    'payment_method' => $request->input('payment_method'),
                    'selected_items_count' => $selectedItems->count()
                ]);
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
                $paymentStatus = 'unpaid'; // valid values: unpaid, paid, failed, expired, refunded
                $orderStatus = 'pending_payment';
                
                // Generate invoice number
                $invoiceNumber = $this->invoiceService->generateInvoiceNumber();
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'invoice_number' => $invoiceNumber,
                    'order_number' => $invoiceNumber, // Explicitly set order_number same as invoice
                    'status' => $orderStatus,
                    'payment_status' => $paymentStatus,
                    'payment_gateway' => 'midtrans',
                    'subtotal' => $subtotal,
                    'tax' => 0, // Required by schema
                    'shipping_cost' => 0,
                    'shipping_weight' => 0, // Fill with 0 if column exists in DB but not migration file
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

                        // Decrement medicine stock if method exists
                        if (method_exists($medicine, 'decrementStock')) {
                            $medicine->decrementStock($item->quantity);
                        }
                    }
                }

                // Record voucher usage
                if ($voucher) {
                    $this->voucherService->recordUsage($voucher, $user, $order);
                }
                

                // Always keep items in cart until confirmed payment or manual removal? 
                // PREVIOUS LOGIC: if online, keep in cart. Since now default is simplified, we treat it as online pending.
                // Store selected item IDs in order metadata for later cleanup
                $order->update([
                    'metadata' => ['selected_cart_items' => $selectedItems]
                ]);
                
                // Add activity to dashboard
                $this->activityService->addOrderActivity($user, $order, 'created');

                \Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'payment_method' => 'midtrans',
                    'total' => $order->total
                ]);

                return $order;
            });
            
            // Redirect to payment page
            \Log::info('Redirecting to payment page', [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number
            ]);
            
            return redirect()->route('checkout.payment', $order);

        } catch (\Exception $e) {
            \Log::error('Checkout process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('checkout.form')
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function payment(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order);
        }

        if (!$order->snap_token) {
            try {
                $paymentData = $this->gateway->createTransaction($order);
                
                $order->update([
                    'snap_token' => $paymentData['snap_token'],
                    'payment_url' => $paymentData['redirect_url'] ?? null,
                ]);
                
                // Refresh order to get updated attributes
                $order->refresh();
                
            } catch (\Exception $e) {
                \Log::error('Payment gateway error in payment view', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->route('checkout.form')
                    ->with('error', 'Gagal menghubungkan ke layanan pembayaran. Silakan coba lagi.');
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
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.confirmation', [
            'order' => $order->load('items'),
        ]);
    }
}