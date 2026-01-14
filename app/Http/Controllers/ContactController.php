<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactMessageNotification;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     */
    public function store(ContactFormRequest $request): RedirectResponse
    {
        // Create the contact message
        $message = ContactMessage::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send email notification (graceful failure)
        $this->sendEmailNotification($message);

        return redirect()
            ->route('contact')
            ->with('success', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
    }

    /**
     * Send email notification to support.
     */
    private function sendEmailNotification(ContactMessage $message): void
    {
        try {
            $supportEmail = SiteSetting::getValue('contact.support_email', config('mail.from.address'));

            if ($supportEmail) {
                Mail::to($supportEmail)->send(new ContactMessageNotification($message));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            Log::error('Failed to send contact message notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
