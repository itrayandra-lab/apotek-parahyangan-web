<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // Short summary for listing
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('display_author_name')->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable(); // For scheduled publication
            $table->timestamp('published_at')->nullable(); // When actually published
            $table->unsignedBigInteger('views_count')->default(0); // Track views
            $table->timestamps();

            // Indexes for scheduling queries performance
            $table->index('status'); // For published(), scheduled(), draft() scopes
            $table->index(['status', 'scheduled_at']); // For readyToPublish() scope and admin index ordering
            $table->index('published_at'); // For published() scope ordering
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
