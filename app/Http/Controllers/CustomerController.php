<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Show customer dashboard.
     */
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        return view('customer.dashboard', compact('user'));
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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password']; // Auto-hashed via cast
        }

        $user->save();

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
