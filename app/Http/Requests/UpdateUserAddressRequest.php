<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        $address = $this->route('address');

        return $address !== null && $address->user_id === $this->user()?->id;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'province_code' => ['required', 'string', 'exists:indonesia_provinces,code'],
            'province_name' => ['required', 'string', 'max:100'],
            'city_code' => ['required', 'string', 'exists:indonesia_cities,code'],
            'city_name' => ['required', 'string', 'max:100'],
            'district_code' => ['required', 'string', 'exists:indonesia_districts,code'],
            'district_name' => ['required', 'string', 'max:100'],
            'village_code' => ['required', 'string', 'exists:indonesia_villages,code'],
            'village_name' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'province_code.required' => 'Provinsi wajib dipilih.',
            'city_code.required' => 'Kota/Kabupaten wajib dipilih.',
            'district_code.required' => 'Kecamatan wajib dipilih.',
            'village_code.required' => 'Kelurahan/Desa wajib dipilih.',
            'postal_code.required' => 'Kode pos wajib diisi.',
        ];
    }
}
