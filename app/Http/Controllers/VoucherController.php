<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyVoucherRequest;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    public function __construct(private VoucherService $voucherService) {}

    public function apply(ApplyVoucherRequest $request): JsonResponse
    {
        $user = $request->user();
        $subtotal = $request->integer('subtotal');
        $shippingCost = $request->integer('shipping_cost', 0);

        $result = $this->voucherService->validate(
            $request->string('code'),
            $user,
            $subtotal,
            $shippingCost
        );

        if (! $result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        $voucher = $result['voucher'];

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'discount' => $result['discount'],
                'discount_formatted' => 'Rp '.number_format($result['discount'], 0, ',', '.'),
            ],
        ]);
    }

    public function remove(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
        ]);
    }
}
