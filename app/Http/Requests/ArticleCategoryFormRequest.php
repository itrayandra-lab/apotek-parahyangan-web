<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleCategoryFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('article_category')?->id;

        return [
            'name' => 'required|string|max:255|unique:article_categories,name,'.$categoryId,
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:article_categories,slug,'.$categoryId,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name must not exceed 255 characters.',
            'name.unique' => 'This category name already exists.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'icon.max' => 'Icon must not exceed 255 characters.',
        ];
    }
}
