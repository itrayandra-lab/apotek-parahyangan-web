<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'social_media.instagram_url' => ['nullable', 'url', 'max:255'],
            'social_media.facebook_url' => ['nullable', 'url', 'max:255'],
            'social_media.youtube_url' => ['nullable', 'url', 'max:255'],
            'contact.support_email' => ['required', 'email', 'max:255'],
            'contact.newsletter_email' => ['nullable', 'email', 'max:255'],
            'contact.address' => ['nullable', 'string', 'max:500'],
            'contact.phone' => ['nullable', 'string', 'max:20'],
            'contact.business_hours' => ['nullable', 'string', 'max:255'],
            'contact.google_maps_embed' => ['nullable', 'url', 'max:2000'],
            'general.site_name' => ['required', 'string', 'max:255'],
            'general.tagline' => ['nullable', 'string', 'max:500'],
            'shipping.origin_city' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'social_media.instagram_url.url' => 'Instagram URL must be a valid URL.',
            'social_media.facebook_url.url' => 'Facebook URL must be a valid URL.',
            'social_media.youtube_url.url' => 'YouTube URL must be a valid URL.',
            'contact.support_email.required' => 'Support email is required.',
            'contact.support_email.email' => 'Support email must be a valid email address.',
            'contact.newsletter_email.email' => 'Newsletter email must be a valid email address.',
            'contact.google_maps_embed.url' => 'Google Maps embed must be a valid URL.',
            'contact.google_maps_embed.max' => 'Google Maps embed URL is too long (max 2000 characters).',
            'contact.business_hours.max' => 'Business hours text is too long (max 255 characters).',
            'general.site_name.required' => 'Site name is required.',
        ];
    }
}
