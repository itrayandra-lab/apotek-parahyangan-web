<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled
                            {--dry-run : Preview articles without actually publishing}';

    protected $description = 'Publish articles that are scheduled and ready to be published';

    private int $successCount = 0;

    private int $failureCount = 0;

    public function handle(): int
    {
        $this->info('Starting scheduled article publication process...');

        $totalArticles = Article::readyToPublish()->count();

        if ($totalArticles === 0) {
            $this->info('No articles ready to publish.');

            return self::SUCCESS;
        }

        $this->info("Found {$totalArticles} article(s) ready to publish.");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No articles will be published');
            $this->previewArticles();

            return self::SUCCESS;
        }

        // Process articles in chunks to avoid memory issues
        Article::readyToPublish()
            ->select('id', 'title', 'scheduled_at')
            ->chunkById(50, function ($articles) {
                $this->processChunk($articles);
            });

        $this->displaySummary();

        return $this->failureCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processChunk($articles): void
    {
        foreach ($articles as $article) {
            try {
                DB::transaction(function () use ($article) {
                    // Reload full article model for publishing
                    $fullArticle = Article::lockForUpdate()->find($article->id);

                    // Double-check still ready to publish (prevent race conditions)
                    if (! $fullArticle || $fullArticle->status !== 'scheduled') {
                        $this->warn("Skipped: {$article->title} (already published or status changed)");

                        return;
                    }

                    $fullArticle->publish();

                    $this->successCount++;
                    $this->info("✓ Published: {$fullArticle->title}");

                    Log::info('Article published by scheduler', [
                        'article_id' => $fullArticle->id,
                        'title' => $fullArticle->title,
                        'scheduled_at' => $fullArticle->scheduled_at,
                        'published_at' => $fullArticle->published_at,
                    ]);
                });
            } catch (\Exception $e) {
                $this->failureCount++;
                $this->error("✗ Failed to publish: {$article->title}");
                $this->error("  Error: {$e->getMessage()}");

                Log::error('Failed to publish scheduled article', [
                    'article_id' => $article->id,
                    'title' => $article->title,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    private function previewArticles(): void
    {
        $this->table(
            ['ID', 'Title', 'Scheduled At'],
            Article::readyToPublish()
                ->select('id', 'title', 'scheduled_at')
                ->get()
                ->map(fn ($article) => [
                    $article->id,
                    $article->title,
                    $article->scheduled_at->format('Y-m-d H:i:s'),
                ])
                ->toArray()
        );
    }

    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('=== Publication Summary ===');
        $this->info("Successfully published: {$this->successCount}");

        if ($this->failureCount > 0) {
            $this->error("Failed to publish: {$this->failureCount}");
            $this->warn('Check logs for detailed error information.');
        }

        Log::info('Scheduled article publication completed', [
            'success_count' => $this->successCount,
            'failure_count' => $this->failureCount,
        ]);
    }
}
