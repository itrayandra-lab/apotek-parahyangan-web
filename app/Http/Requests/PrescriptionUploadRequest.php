<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionUploadRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'prescription_image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5120', // 5MB in kilobytes
            ],
            'user_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'prescription_image.required' => 'Foto resep wajib diunggah.',
            'prescription_image.image' => 'File harus berupa gambar.',
            'prescription_image.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'prescription_image.max' => 'Ukuran file maksimal 5MB.',
            'user_notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
