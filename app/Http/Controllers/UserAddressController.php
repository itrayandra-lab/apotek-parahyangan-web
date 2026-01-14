<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserAddressRequest;
use App\Http\Requests\UpdateUserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;

class UserAddressController extends Controller
{
    public function index(Request $request): View
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        $provinces = Province::query()->orderBy('name')->get(['code', 'name']);

        return view('account.addresses.index', [
            'addresses' => $addresses,
            'provinces' => $provinces,
            'maxAddresses' => UserAddress::MAX_ADDRESSES_PER_USER,
        ]);
    }

    public function store(StoreUserAddressRequest $request): JsonResponse
    {
        $user = $request->user();
        $isFirstAddress = $user->addresses()->count() === 0;

        $address = $user->addresses()->create([
            ...$request->validated(),
            'is_default' => $isFirstAddress || $request->boolean('is_default'),
        ]);

        if ($request->boolean('is_default') && ! $isFirstAddress) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil ditambahkan.',
            'data' => $address,
        ]);
    }

    public function update(UpdateUserAddressRequest $request, UserAddress $address): JsonResponse
    {
        $address->update($request->validated());

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui.',
            'data' => $address->fresh(),
        ]);
    }

    public function destroy(Request $request, UserAddress $address): JsonResponse|RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $newDefault = $request->user()->addresses()->first();
            $newDefault?->update(['is_default' => true]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Request $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Alamat utama berhasil diubah.',
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }
}
