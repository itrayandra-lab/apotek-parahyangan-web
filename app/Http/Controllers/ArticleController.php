<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $categoryFilter = $request->filled('category');
        $searchQuery = $request->string('search')->trim();
        $hasSearch = $searchQuery->isNotEmpty();

        // Get featured article (only if no category filter and no search)
        $featuredArticle = null;
        if (! $categoryFilter && ! $hasSearch) {
            $featuredArticle = Article::published()
                ->with(['author', 'categories', 'tags'])
                ->latest('published_at')
                ->first();
        }

        $query = Article::published()
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at');

        // Exclude featured article from regular listing if exists
        if ($featuredArticle) {
            $query->where('id', '!=', $featuredArticle->id);
        }

        // Search filter (title, excerpt, tags, categories)
        if ($hasSearch) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%'.$searchQuery.'%')
                    ->orWhere('excerpt', 'like', '%'.$searchQuery.'%')
                    ->orWhereHas('tags', function ($tagQuery) use ($searchQuery) {
                        $tagQuery->where('name', 'like', '%'.$searchQuery.'%');
                    })
                    ->orWhereHas('categories', function ($catQuery) use ($searchQuery) {
                        $catQuery->where('name', 'like', '%'.$searchQuery.'%');
                    });
            });
        }

        // Category filter
        if ($categoryFilter) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('article_categories.slug', $request->category);
            });
        }

        $articles = $query->paginate(8)->appends($request->query());
        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->orderBy('name')->get();

        return view('articles.index', compact('articles', 'categories', 'featuredArticle', 'searchQuery'));
    }

    public function loadMore(Request $request): JsonResponse
    {
        $page = $request->input('page', 2);
        $categorySlug = $request->input('category');
        $excludeId = $request->input('exclude_id');
        $searchQuery = $request->string('search')->trim();

        $query = Article::published()
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at');

        // Exclude featured article if provided
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Search filter
        if ($searchQuery->isNotEmpty()) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%'.$searchQuery.'%')
                    ->orWhere('excerpt', 'like', '%'.$searchQuery.'%')
                    ->orWhereHas('tags', function ($tagQuery) use ($searchQuery) {
                        $tagQuery->where('name', 'like', '%'.$searchQuery.'%');
                    })
                    ->orWhereHas('categories', function ($catQuery) use ($searchQuery) {
                        $catQuery->where('name', 'like', '%'.$searchQuery.'%');
                    });
            });
        }

        if ($categorySlug) {
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('article_categories.slug', $categorySlug);
            });
        }

        $articles = $query->paginate(8, ['*'], 'page', $page);

        $articlesHtml = view('articles.partials.article-list', [
            'articles' => $articles,
        ])->render();

        return response()->json([
            'html' => $articlesHtml,
            'has_more' => $articles->hasMorePages(),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::published()
            ->with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views only for real users (not bots) and once per session
        $viewedKey = 'article_viewed_'.$article->id;
        if (! session()->has($viewedKey) && ! $this->isBot()) {
            $article->incrementViews();
            session()->put($viewedKey, true);
        }

        // Get related articles
        $relatedArticles = $article->getRelatedArticles(4);

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Check if the current request is from a bot/crawler.
     */
    private function isBot(): bool
    {
        $userAgent = strtolower(request()->userAgent() ?? '');

        if (empty($userAgent)) {
            return true; // No user agent = likely a bot
        }

        $botPatterns = [
            'bot',
            'crawl',
            'spider',
            'slurp',
            'scraper',
            'googlebot',
            'bingbot',
            'yandex',
            'baidu',
            'duckduck',
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'whatsapp',
            'telegram',
            'discord',
            'curl',
            'wget',
            'python',
            'java',
            'php',
            'ruby',
            'headless',
            'phantom',
            'selenium',
            'puppeteer',
            'lighthouse',
            'pagespeed',
            'gtmetrix',
        ];

        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function showByCategory(string $slug): View
    {
        $category = ArticleCategory::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('article_categories.id', $category->id);
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(9);

        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->orderBy('name')->get();

        return view('articles.category', compact('articles', 'category', 'categories'));
    }
}
