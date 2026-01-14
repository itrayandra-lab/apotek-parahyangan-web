<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('products');

        $search = $request->string('search')->trim();
        $status = $request->string('status')->trim();
        $hasProducts = $request->string('has_products')->trim();

        if ($search->isNotEmpty()) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        if ($status->isNotEmpty() && in_array($status->toString(), ['active', 'hidden'], true)) {
            $query->where('status', $status->toString());
        }

        if ($hasProducts->isNotEmpty() && in_array($hasProducts->toString(), ['with', 'without'], true)) {
            if ($hasProducts->toString() === 'with') {
                $query->whereHas('products');
            } else {
                $query->whereDoesntHave('products');
            }
        }

        $categories = $query
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.categories.index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search->toString(),
                'status' => $status->toString(),
                'has_products' => $hasProducts->toString(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,hidden',
        ]);

        Category::query()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.form', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,hidden',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        // Check if category has associated products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
