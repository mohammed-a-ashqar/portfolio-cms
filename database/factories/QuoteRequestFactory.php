<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QuoteStatus;
use App\Models\QuoteRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteRequest>
 */
final class QuoteRequestFactory extends Factory
{
    protected $model = QuoteRequest::class;

    public function definition(): array
    {
        return [
            'reference' => 'QR-'.now()->year.'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'company' => $this->faker->company(),
            'budget_min_minor' => 100000,
            'budget_max_minor' => 500000,
            'currency' => 'USD',
            'message' => $this->faker->paragraph(4),
            'status' => QuoteStatus::New,
        ];
    }

    public function withStatus(QuoteStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
