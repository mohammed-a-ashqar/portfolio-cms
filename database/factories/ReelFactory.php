<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReelProvider;
use App\Models\Reel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reel>
 */
final class ReelFactory extends Factory
{
    protected $model = Reel::class;

    public function definition(): array
    {
        $code = Str::random(11);

        return [
            'provider' => ReelProvider::Instagram,
            'external_id' => $code,
            'url' => "https://www.instagram.com/reel/{$code}/",
            'embed_url' => "https://www.instagram.com/reel/{$code}/embed/",
            'caption' => ['en' => $this->faker->sentence(6)],
            'is_active' => true,
        ];
    }

    public function youtube(): static
    {
        return $this->state(function (): array {
            $id = Str::random(11);

            return [
                'provider' => ReelProvider::YouTube,
                'external_id' => $id,
                'url' => "https://www.youtube.com/shorts/{$id}",
                'embed_url' => "https://www.youtube-nocookie.com/embed/{$id}",
                'thumbnail_url' => "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg",
            ];
        });
    }

    public function hidden(): static
    {
        return $this->state(['is_active' => false]);
    }
}
