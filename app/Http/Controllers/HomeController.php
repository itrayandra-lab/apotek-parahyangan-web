<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ExpertQuote;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Slider;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        $sliders = Slider::with('media')
            ->active()
            ->orderBy('position', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        // Featured medicines for the new homepage
        $featuredMedicines = Medicine::with(['medicineUnits', 'category'])
            ->whereHas('medicineUnits')
            ->orderBy('name', 'asc')
            ->limit(8)
            ->get();

        // Get 3 categories for shortcuts or highlights
        $topCategories = MedicineCategory::whereHas('medicines')
            ->orderBy('name', 'asc')
            ->take(6)
            ->get();

        // Get latest articles for health info
        $articles = Article::published()
            ->with(['author', 'categories', 'media'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $expertQuote = ExpertQuote::query()
            ->with('media')
            ->active()
            ->first();

        return view('home.index', compact('sliders', 'featuredMedicines', 'topCategories', 'articles', 'expertQuote'));
    }

    /**
     * Display the terms and conditions page.
     */
    public function terms(): View
    {
        return view('home.terms');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        return view('home.privacy');
    }

    /**
     * Display the refund policy page.
     */
    public function refund(): View
    {
        return view('home.refund');
    }

    /**
     * Display the delivery policy page.
     */
    public function delivery(): View
    {
        return view('home.delivery');
    }

    /**
     * Display the contact page.
     */
    public function contact(): View
    {
        return view('home.contact');
    }
}
