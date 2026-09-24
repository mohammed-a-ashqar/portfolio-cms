<?php

declare(strict_types=1);

use App\Enums\ReelProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 24)->default(ReelProvider::Instagram->value);
            $table->string('external_id');
            $table->string('url', 512);
            $table->string('embed_url', 512);
            $table->json('caption')->nullable();
            // Locally stored poster wins; remote URL is the fallback.
            $table->string('thumbnail_path')->nullable();
            $table->string('thumbnail_url', 512)->nullable();
            $table->string('author_name')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order_column')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // One record per video per platform — re-importing updates instead of duplicating.
            $table->unique(['provider', 'external_id']);
            $table->index(['is_active', 'order_column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
