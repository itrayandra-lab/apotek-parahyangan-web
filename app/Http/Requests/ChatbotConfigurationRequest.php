<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chatbot_active' => ['required', 'boolean'],
            'webhook_enabled' => ['required', 'boolean'],
            'webhook_url' => ['required', 'url', 'max:500'],
            'webhook_timeout' => ['required', 'integer', 'min:5', 'max:120'],
            'webhook_retry_attempts' => ['required', 'integer', 'min:1', 'max:5'],
            'guest_session_expiry_days' => ['required', 'integer', 'min:1', 'max:90'],
            'rate_limit_per_minute' => ['required', 'integer', 'min:10', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'chatbot_active.required' => 'Status chatbot wajib diisi.',
            'chatbot_active.boolean' => 'Status chatbot harus berupa nilai aktif/nonaktif.',

            'webhook_enabled.required' => 'Status webhook wajib diisi.',
            'webhook_enabled.boolean' => 'Status webhook harus berupa nilai aktif/nonaktif.',

            'webhook_url.required' => 'Webhook URL wajib diisi.',
            'webhook_url.url' => 'Webhook URL harus berupa URL yang valid.',
            'webhook_url.max' => 'Webhook URL maksimal 500 karakter.',

            'webhook_timeout.required' => 'Timeout webhook wajib diisi.',
            'webhook_timeout.integer' => 'Timeout webhook harus berupa angka.',
            'webhook_timeout.min' => 'Webhook timeout harus antara 5-120 detik.',
            'webhook_timeout.max' => 'Webhook timeout harus antara 5-120 detik.',

            'webhook_retry_attempts.required' => 'Jumlah retry webhook wajib diisi.',
            'webhook_retry_attempts.integer' => 'Jumlah retry webhook harus berupa angka.',
            'webhook_retry_attempts.min' => 'Retry webhook minimal 1 kali.',
            'webhook_retry_attempts.max' => 'Retry webhook maksimal 5 kali.',

            'guest_session_expiry_days.required' => 'Masa berlaku sesi tamu wajib diisi.',
            'guest_session_expiry_days.integer' => 'Masa berlaku sesi tamu harus berupa angka.',
            'guest_session_expiry_days.min' => 'Masa berlaku sesi tamu minimal 1 hari.',
            'guest_session_expiry_days.max' => 'Masa berlaku sesi tamu maksimal 90 hari.',

            'rate_limit_per_minute.required' => 'Batas rate per menit wajib diisi.',
            'rate_limit_per_minute.integer' => 'Batas rate per menit harus berupa angka.',
            'rate_limit_per_minute.min' => 'Batas rate per menit minimal 10.',
            'rate_limit_per_minute.max' => 'Batas rate per menit maksimal 100.',
        ];
    }
}
