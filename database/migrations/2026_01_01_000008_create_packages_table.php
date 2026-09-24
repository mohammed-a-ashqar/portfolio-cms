<?php

declare(strict_types=1);

use App\Enums\BillingPeriod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            // Minor units: never store money as a float.
            $table->unsignedBigInteger('price_minor')->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_period', 24)->default(BillingPeriod::OneTime->value);
            $table->json('features')->nullable();      // ["...", "..."] per locale
            $table->unsignedInteger('delivery_days')->nullable();
            $table->unsignedInteger('revisions')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order_column')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'is_active', 'order_column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
