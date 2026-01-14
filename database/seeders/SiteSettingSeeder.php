<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'social_media.instagram_url',
                'value' => 'https://instagram.com/beautylatory',
                'group' => 'social_media',
                'description' => 'Instagram Profile URL',
            ],
            [
                'key' => 'social_media.facebook_url',
                'value' => 'https://facebook.com/beautylatory',
                'group' => 'social_media',
                'description' => 'Facebook Page URL',
            ],
            [
                'key' => 'social_media.youtube_url',
                'value' => 'https://youtube.com/@beautylatory',
                'group' => 'social_media',
                'description' => 'YouTube Channel URL',
            ],
            [
                'key' => 'contact.support_email',
                'value' => 'support@beautylatory.com',
                'group' => 'contact',
                'description' => 'Customer Support Email',
            ],
            [
                'key' => 'contact.newsletter_email',
                'value' => 'newsletter@beautylatory.com',
                'group' => 'contact',
                'description' => 'Newsletter Subscription Email',
            ],
            [
                'key' => 'contact.address',
                'value' => '',
                'group' => 'contact',
                'description' => 'Business Address',
            ],
            [
                'key' => 'contact.phone',
                'value' => '',
                'group' => 'contact',
                'description' => 'Contact Phone/WhatsApp',
            ],
            [
                'key' => 'contact.google_maps_embed',
                'value' => '',
                'group' => 'contact',
                'description' => 'Google Maps Embed URL',
            ],
            [
                'key' => 'contact.business_hours',
                'value' => 'Senin - Jumat: 09:00 - 17:00 WIB',
                'group' => 'contact',
                'description' => 'Business Hours',
            ],
            [
                'key' => 'general.site_name',
                'value' => 'Beautylatory',
                'group' => 'general',
                'description' => 'Site Name',
            ],
            [
                'key' => 'general.tagline',
                'value' => 'Great skin starts with innovation.',
                'group' => 'general',
                'description' => 'Site Tagline',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'description' => $setting['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
