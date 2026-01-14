<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $user = auth()->guard('admin')->user();
        $isAdmin = $user?->role === 'admin';

        $productCount = $isAdmin ? Product::count() : null;
        $categoryCount = $isAdmin ? Category::count() : null;
        $sliderCount = $isAdmin ? Slider::count() : null;
        $recentProducts = $isAdmin ? Product::with('category')->latest()->take(5)->get() : collect();

        return view('admin.dashboard', compact('productCount', 'categoryCount', 'sliderCount', 'recentProducts'));
    }

    /**
     * Show the admin profile page.
     */
    public function showProfile(): View
    {
        $user = auth()->guard('admin')->user();

        return view('admin.profile.show', compact('user'));
    }

    /**
     * Show the profile edit form.
     */
    public function editProfile(): View
    {
        $user = auth()->guard('admin')->user();

        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->guard('admin')->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->updateProfile([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully!');
    }
}
