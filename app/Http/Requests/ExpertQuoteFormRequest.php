<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpertQuoteFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('POST');

        return [
            'quote' => ['required', 'string', 'min:10'],
            'author_name' => ['required', 'string', 'max:255'],
            'author_title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [
                $isCreate ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'quote.required' => 'Expert quote is required.',
            'quote.min' => 'Quote must be at least 10 characters.',
            'author_name.required' => 'Expert name is required.',
            'author_name.max' => 'Expert name must not exceed 255 characters.',
            'author_title.required' => 'Role is required.',
            'author_title.max' => 'Role must not exceed 255 characters.',
            'image.required' => 'Expert photo is required.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }
}
