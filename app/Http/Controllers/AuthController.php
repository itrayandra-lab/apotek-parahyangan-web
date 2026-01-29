<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        Log::channel('auth')->info('Login attempt started', [
            'email' => $request->input('email_or_username'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        $request->validate([
            'email_or_username' => ['required', 'string', 'regex:/^[^;&|`$(){}<>\\\]+$/'],
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email_or_username', 'password');

        $guestSessionId = $request->session()->getId();

        $user = User::where('email', $credentials['email_or_username'])
            ->orWhere('username', $credentials['email_or_username'])
            ->orWhere('whatsapp', $credentials['email_or_username'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::channel('auth')->warning('Login failed: Invalid credentials', [
                'email' => $credentials['email_or_username'],
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput($request->only('email_or_username'))
                ->withErrors(['email_or_username' => 'Identitas atau password salah. Silakan coba kembali.']);
        }

        Log::channel('auth')->info('Login successful', [
            'user_id' => $user->id,
            'role' => $user->role,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

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
        Log::channel('auth')->info('Registration attempt started', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^;&|`$(){}<>\\\]+$/'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username', 'regex:/^[^;&|`$(){}<>\\\]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'regex:/^[^;&|`$(){}<>\\\]+$/'],
            'whatsapp' => ['required', 'string', 'max:20', 'regex:/^[^;&|`$(){}<>\\\]+$/'],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $guestSessionId = $request->session()->getId();

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        Log::channel('auth')->info('Registration successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        // Auto-login after registration
        $request->session()->put('guest_cart_session_id', $guestSessionId);
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')
            ->with('success', 'Account created successfully! Welcome to Apotek Parahyangan Suite.');
    }

    /**
     * Handle a logout request (unified for admin & customer).
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        Log::channel('auth')->info('Logout event', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }
}
