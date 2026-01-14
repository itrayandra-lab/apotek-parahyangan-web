<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_province' => ['required', 'string', 'max:100'],
            'shipping_district' => ['required', 'string', 'max:100'],
            'shipping_village' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'province_code' => ['required', 'string', 'size:2', 'exists:indonesia_provinces,code'],
            'city_code' => ['required', 'string', 'size:4', 'exists:indonesia_cities,code'],
            'district_code' => ['required', 'string', 'size:6', 'exists:indonesia_districts,code'],
            'village_code' => ['required', 'string', 'size:10', 'exists:indonesia_villages,code'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'shipping_courier' => ['required', 'string', 'max:50'],
            'shipping_service' => ['required', 'string', 'max:50'],
            'shipping_cost' => ['required', 'integer', 'min:0'],
            'shipping_etd' => ['nullable', 'string', 'max:50'],
            'rajaongkir_destination_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_courier.required' => 'Silakan pilih kurir pengiriman.',
            'shipping_service.required' => 'Silakan pilih layanan pengiriman.',
            'shipping_cost.required' => 'Ongkos kirim harus dipilih.',
        ];
    }
}
