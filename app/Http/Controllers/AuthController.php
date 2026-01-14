<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the unified login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Redirect if already logged in via any guard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle a unified login request (admin & customer).
     * Supports login via email OR username.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email_or_username' => 'required|string',
            'password' => 'required|string',
        ]);

        $guestSessionId = $request->session()->getId();

        // Find user by email or username
        $user = User::where('email', $credentials['email_or_username'])
            ->orWhere('username', $credentials['email_or_username'])
            ->first();

        // Validate user exists and password matches
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email_or_username'))
                ->withErrors(['email_or_username' => 'Invalid credentials provided.']);
        }

        // Determine guard and redirect based on role
        if ($user->role === 'admin') {
            $request->session()->put('guest_cart_session_id', $guestSessionId);
            Auth::guard('admin')->login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role === 'content_manager') {
            $request->session()->put('guest_cart_session_id', $guestSessionId);
            Auth::guard('admin')->login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.articles.index'));
        }

        if ($user->role === 'user') {
            $request->session()->put('guest_cart_session_id', $guestSessionId);
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        // Fallback for unknown roles
        return back()
            ->withInput($request->only('email_or_username'))
            ->withErrors(['email_or_username' => 'Unauthorized access.']);
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        // Redirect if already logged in via any guard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $guestSessionId = $request->session()->getId();

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Auto-hashed via cast
            'role' => 'user', // Default role for customers
        ]);

        // Auto-login after registration
        $request->session()->put('guest_cart_session_id', $guestSessionId);
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')
            ->with('success', 'Account created successfully! Welcome to Beautylatory.');
    }

    /**
     * Handle a logout request (unified for admin & customer).
     */
    public function logout(Request $request): RedirectResponse
    {
        // Logout from both guards to ensure complete logout
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }
}
