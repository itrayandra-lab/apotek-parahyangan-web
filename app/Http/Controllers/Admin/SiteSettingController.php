<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSettingFormRequest;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class SiteSettingController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::getAllGrouped();

        return view('admin.site-settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(SiteSettingFormRequest $request): RedirectResponse
    {
        $flattened = Arr::dot($request->validated());

        foreach ($flattened as $key => $value) {
            $segments = explode('.', $key);
            $group = $segments[0] ?? 'general';

            $existing = SiteSetting::where('key', $key)->first();
            $description = $existing?->description ?? ucwords(str_replace('_', ' ', $segments[1] ?? $key));

            SiteSetting::setValue($key, $value ?? '', $description, $group);
        }

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'Site settings updated successfully.');
    }

    public function testEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'type' => ['required', 'in:support,newsletter'],
        ]);

        try {
            Mail::raw('This is a test email from Beautylatory Site Settings.', function ($message) use ($validated): void {
                $message->to($validated['email'])
                    ->subject('Test Email - Beautylatory');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: '.$exception->getMessage(),
            ], 500);
        }
    }
}
