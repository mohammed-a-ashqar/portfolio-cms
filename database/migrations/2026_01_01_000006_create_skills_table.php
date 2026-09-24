<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->string('group', 64)->nullable();   // Backend / Frontend / DevOps
            $table->unsignedTinyInteger('proficiency')->default(0); // 0-100
            $table->string('icon', 64)->nullable();
            $table->json('description')->nullable();
            $table->unsignedInteger('order_column')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group', 'order_column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
