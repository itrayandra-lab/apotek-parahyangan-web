<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCategoryFormRequest;
use App\Models\ArticleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ArticleCategory::withCount('articles');

        $search = $request->string('search')->trim();

        if ($search->isNotEmpty()) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $categories = $query
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.article-categories.index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.article-categories.form', [
            'category' => new ArticleCategory,
            'isEdit' => false,
        ]);
    }

    public function store(ArticleCategoryFormRequest $request): RedirectResponse
    {
        ArticleCategory::create($request->validated());

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ArticleCategory $articleCategory): View
    {
        return view('admin.article-categories.form', [
            'category' => $articleCategory,
            'isEdit' => true,
        ]);
    }

    public function update(ArticleCategoryFormRequest $request, ArticleCategory $articleCategory): RedirectResponse
    {
        $articleCategory->update($request->validated());

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ArticleCategory $articleCategory): RedirectResponse
    {
        $articleCategory->delete();

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
