<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleFormRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::with(['author', 'categories']);

        $search = $request->string('search')->trim();
        $status = $request->string('status')->trim();
        $onlyMine = $request->boolean('mine');

        if ($search->isNotEmpty()) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        if ($status->isNotEmpty() && in_array($status->toString(), ['draft', 'published', 'scheduled'], true)) {
            $query->where('status', $status->toString());
        }

        if ($onlyMine && auth()->check()) {
            $query->where('author_id', auth()->id());
        }

        $articles = $query
            ->orderByRaw("FIELD(status, 'scheduled', 'published', 'draft')")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.articles.index', [
            'articles' => $articles,
            'filters' => [
                'search' => $search->toString(),
                'status' => $status->toString(),
                'mine' => $onlyMine,
            ],
        ]);
    }

    public function create(): View
    {
        $categories = ArticleCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.form', [
            'article' => new Article,
            'categories' => $categories,
            'tags' => $tags,
            'isEdit' => false,
        ]);
    }

    public function store(ArticleFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Extract body content for separate handling
        $bodyContent = $validated['body'] ?? null;
        unset($validated['body']);

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        // Set rich text content after model creation
        if ($bodyContent) {
            $article->body = $bodyContent;
            $article->save();
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $article->addMediaFromRequest('thumbnail')
                ->toMediaCollection('article_images');
        }

        // Sync categories
        $article->categories()->sync($validated['categories']);

        // Handle tags
        if (! empty($validated['tags'])) {
            $tagIds = $this->handleTags($validated['tags']);
            $article->tags()->sync($tagIds);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function show(Article $article): View
    {
        $article->load(['author', 'categories', 'tags']);

        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        $article->load(['categories', 'tags']);
        $categories = ArticleCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.form', [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'isEdit' => true,
        ]);
    }

    public function update(ArticleFormRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();

        // Extract body content for separate handling
        $bodyContent = $validated['body'] ?? null;
        unset($validated['body']);

        // Handle status changes
        if ($validated['status'] === 'published' && $article->status !== 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== 'published') {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        // Set rich text content after model update
        if ($bodyContent !== null) {
            $article->body = $bodyContent;
            $article->save();
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $article->clearMediaCollection('article_images');
            $article->addMediaFromRequest('thumbnail')
                ->toMediaCollection('article_images');
        }

        // Sync categories
        $article->categories()->sync($validated['categories']);

        // Handle tags
        if (! empty($validated['tags'])) {
            $tagIds = $this->handleTags($validated['tags']);
            $article->tags()->sync($tagIds);
        } else {
            $article->tags()->sync([]);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    public function publish(Article $article): RedirectResponse
    {
        $article->publish();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article published successfully.');
    }

    public function unschedule(Article $article): RedirectResponse
    {
        $article->unschedule();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article unscheduled and moved to draft.');
    }

    private function handleTags(array $tagInputs): array
    {
        $tagIds = [];

        foreach ($tagInputs as $tagInput) {
            $tagInput = trim($tagInput);

            if (empty($tagInput)) {
                continue;
            }

            // Check if tag exists by name
            $tag = Tag::where('name', $tagInput)->first();

            if (! $tag) {
                // Create new tag
                $tag = Tag::create(['name' => $tagInput]);
            }

            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }
}
