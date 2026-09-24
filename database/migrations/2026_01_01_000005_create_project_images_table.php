<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->json('alt')->nullable();
            $table->unsignedInteger('order_column')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'order_column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
