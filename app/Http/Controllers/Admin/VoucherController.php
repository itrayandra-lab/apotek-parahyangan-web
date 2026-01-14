<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherFormRequest;
use App\Models\Voucher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Voucher::query()->latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        $vouchers = $query->paginate(15)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('admin.vouchers.form', ['voucher' => null]);
    }

    public function store(VoucherFormRequest $request): RedirectResponse
    {
        Voucher::create([
            'code' => $request->string('code'),
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'type' => $request->string('type'),
            'value' => $request->integer('value'),
            'min_purchase' => $request->integer('min_purchase', 0),
            'max_discount' => $request->filled('max_discount') ? $request->integer('max_discount') : null,
            'usage_limit' => $request->filled('usage_limit') ? $request->integer('usage_limit') : null,
            'usage_limit_per_user' => $request->integer('usage_limit_per_user', 1),
            'is_active' => $request->boolean('is_active'),
            'valid_from' => $request->date('valid_from'),
            'valid_until' => $request->date('valid_until'),
        ]);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.form', compact('voucher'));
    }

    public function update(VoucherFormRequest $request, Voucher $voucher): RedirectResponse
    {
        $voucher->update([
            'code' => $request->string('code'),
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'type' => $request->string('type'),
            'value' => $request->integer('value'),
            'min_purchase' => $request->integer('min_purchase', 0),
            'max_discount' => $request->filled('max_discount') ? $request->integer('max_discount') : null,
            'usage_limit' => $request->filled('usage_limit') ? $request->integer('usage_limit') : null,
            'usage_limit_per_user' => $request->integer('usage_limit_per_user', 1),
            'is_active' => $request->boolean('is_active'),
            'valid_from' => $request->date('valid_from'),
            'valid_until' => $request->date('valid_until'),
        ]);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
