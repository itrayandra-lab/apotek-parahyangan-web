<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndonesiaDistrictsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city_code' => ['required', 'string', 'size:4', 'exists:indonesia_cities,code'],
        ];
    }
}
