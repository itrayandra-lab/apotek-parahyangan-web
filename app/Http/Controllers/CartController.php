<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(): View
    {
        $cart = $this->getCart()->load('items.product');

        return view('cart.index', [
            'cart' => $cart,
        ]);
    }

    /**
     * Get current cart item count for badge.
     */
    public function count(): JsonResponse
    {
        $cart = $this->getCart();

        return response()->json([
            'count' => $cart->getItemsCount(),
        ]);
    }

    /**
     * Add a product to cart.
     */
    public function add(AddToCartRequest $request): JsonResponse
    {
        $cart = $this->getCart();
        $product = Product::with('media')->findOrFail($request->integer('product_id'));
        $quantity = $request->integer('quantity', 1);

        if ($product->status !== 'published') {
            return response()->json([
                'message' => 'Produk tidak tersedia.',
            ], 422);
        }

        if (! $product->isInStock($quantity)) {
            return response()->json([
                'message' => 'Stok produk tidak mencukupi.',
            ], 422);
        }

        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        if (! $product->isInStock($newQuantity)) {
            return response()->json([
                'message' => 'Jumlah melebihi stok tersedia.',
            ], 422);
        }

        $cart->addProduct($product, $quantity);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'count' => $cart->getItemsCount(),
            'totals' => $this->cartTotals($cart),
        ]);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(CartItem $item, UpdateCartItemRequest $request): JsonResponse|RedirectResponse
    {
        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            return $this->respondNotFound($request, 'Item tidak ditemukan di keranjang.');
        }

        $quantity = $request->integer('quantity');
        $product = $item->product()->first();

        if (! $product || $product->status !== 'published') {
            return $this->respondError($request, 'Produk tidak tersedia.', 422);
        }

        if (! $product->isInStock($quantity)) {
            return $this->respondError($request, 'Stok produk tidak mencukupi.', 422);
        }

        $item->setQuantity($quantity);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Kuantitas diperbarui.',
                'count' => $cart->getItemsCount(),
                'totals' => $this->cartTotals($cart->fresh('items.product')),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Kuantitas diperbarui.');
    }

    /**
     * Remove a cart item.
     */
    public function remove(Request $request, CartItem $item): JsonResponse|RedirectResponse
    {
        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            return $this->respondNotFound($request, 'Item tidak ditemukan di keranjang.');
        }

        $item->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Item dihapus.',
                'count' => $cart->getItemsCount(),
                'totals' => $this->cartTotals($cart->fresh('items.product')),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Clear the cart.
     */
    public function clear(): RedirectResponse
    {
        $cart = $this->getCart();
        $cart->clear();

        return redirect()->route('cart.index')->with('success', 'Keranjang dikosongkan.');
    }

    private function getCart(): Cart
    {
        return Cart::currentCart();
    }

    /**
     * @return array<string, int>
     */
    private function cartTotals(Cart $cart): array
    {
        return [
            'subtotal' => $cart->getSubtotal(),
            'shipping' => $cart->getShippingCost(),
            'total' => $cart->getTotal(),
        ];
    }

    private function respondNotFound(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 404);
        }

        return redirect()->route('cart.index')->with('error', $message);
    }

    private function respondError(Request $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return redirect()->route('cart.index')->with('error', $message);
    }
}
