<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ArticleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:articles,slug,'.$articleId,
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author_id' => 'required|exists:users,id',
            'display_author_name' => 'nullable|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:article_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'nullable|date',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // Require scheduled_at when status is scheduled
        $validator->sometimes('scheduled_at', 'required|after_or_equal:now', function ($input) {
            // Only validate "after_or_equal:now" for NEW articles or CHANGED scheduled_at
            if ($input->status !== 'scheduled') {
                return false;
            }

            // For new articles, always validate
            if (! $this->isMethod('PUT') && ! $this->isMethod('PATCH')) {
                return true;
            }

            // For updates, only validate if scheduled_at actually changed
            $article = $this->route('article');
            if (! $article || ! $this->filled('scheduled_at')) {
                return false;
            }

            $originalScheduledAt = $article->scheduled_at ? $article->scheduled_at->format('Y-m-d H:i') : null;
            $requestScheduledAt = $this->input('scheduled_at') ? Carbon::parse($this->input('scheduled_at'))->format('Y-m-d H:i') : null;

            return $requestScheduledAt !== $originalScheduledAt;
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Article title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'body.required' => 'Article content is required.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.mimes' => 'Thumbnail must be a file of type: jpeg, png, jpg, gif, webp.',
            'thumbnail.max' => 'Thumbnail size must not exceed 2MB.',
            'author_id.required' => 'Author is required.',
            'author_id.exists' => 'Selected author does not exist.',
            'display_author_name.max' => 'Display author name must not exceed 255 characters.',
            'categories.required' => 'At least one category is required.',
            'categories.min' => 'At least one category must be selected.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
            'tags.*.max' => 'Each tag must not exceed 255 characters.',
            'status.required' => 'Publication status is required.',
            'status.in' => 'Status must be draft, published, or scheduled.',
            'scheduled_at.required' => 'Scheduled date is required when status is scheduled.',
            'scheduled_at.date' => 'Scheduled date must be a valid date.',
            'scheduled_at.after_or_equal' => 'Scheduled date must be in the future.',
        ];
    }
}
