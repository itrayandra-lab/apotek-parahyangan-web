<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $voucherId = $this->route('voucher')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code')->ignore($voucherId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'free_shipping'])],
            'value' => ['required', 'integer', 'min:0'],
            'min_purchase' => ['nullable', 'integer', 'min:0'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.unique' => 'Kode voucher sudah digunakan.',
            'name.required' => 'Nama voucher wajib diisi.',
            'type.required' => 'Tipe voucher wajib dipilih.',
            'value.required' => 'Nilai voucher wajib diisi.',
            'valid_until.after_or_equal' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }
}
