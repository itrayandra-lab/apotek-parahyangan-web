<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir) {}

    /**
     * Search destination by keyword
     */
    public function searchDestination(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:3|max:100',
        ]);

        if (! $this->rajaOngkir->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping service not configured',
                'data' => [],
            ], 503);
        }

        $destinations = $this->rajaOngkir->searchDestination($request->string('keyword'));

        return response()->json([
            'success' => true,
            'data' => $destinations->values(),
        ]);
    }

    /**
     * Calculate shipping cost
     */
    public function calculateCost(Request $request): JsonResponse
    {
        $request->validate([
            'destination_id' => 'required|integer',
        ]);

        if (! $this->rajaOngkir->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping service not configured',
                'data' => [],
            ], 503);
        }

        $originId = $this->rajaOngkir->getOriginId();

        if (! $originId) {
            return response()->json([
                'success' => false,
                'message' => 'Origin city not configured',
                'data' => [],
            ], 503);
        }

        $cart = Cart::currentCart()->load('items.product');
        $totalWeight = $this->calculateTotalWeight($cart);

        $costs = $this->rajaOngkir->calculateCost(
            $originId,
            (int) $request->integer('destination_id'),
            $totalWeight
        );

        return response()->json([
            'success' => true,
            'data' => [
                'weight' => $totalWeight,
                'origin_id' => $originId,
                'destination_id' => $request->integer('destination_id'),
                'costs' => $costs->values(),
            ],
        ]);
    }

    /**
     * Calculate total weight from cart items
     */
    private function calculateTotalWeight(Cart $cart): int
    {
        $defaultWeight = config('rajaongkir.default_weight', 200);

        $totalWeight = $cart->items->sum(function ($item) use ($defaultWeight) {
            $productWeight = $item->product?->weight ?? $defaultWeight;

            return $productWeight * $item->quantity;
        });

        return max($totalWeight, 1);
    }
}
