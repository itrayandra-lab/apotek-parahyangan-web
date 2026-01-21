<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private ActivityService $activityService)
    {
    }

    /**
     * Show customer dashboard.
     */
    public function index(): View
    {
        $user = Auth::guard('web')->user();
        $activities = $this->activityService->getRecentActivities($user, 10);

        return view('customer.dashboard', compact('user', 'activities'));
    }

    /**
     * Show customer profile.
     */
    public function showProfile(): View
    {
        $user = Auth::guard('web')->user();

        return view('customer.profile.show', compact('user'));
    }

    /**
     * Show edit profile form.
     */
    public function editProfile(): View
    {
        $user = Auth::guard('web')->user();

        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'whatsapp' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->whatsapp = $validated['whatsapp'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password']; // Auto-hashed via cast
        }

        $user->save();

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
