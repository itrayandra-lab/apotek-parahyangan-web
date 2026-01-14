<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndonesiaVillagesRequest extends FormRequest
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
            'district_code' => ['required', 'string', 'size:6', 'exists:indonesia_districts,code'],
        ];
    }
}
