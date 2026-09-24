<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
final class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = rtrim($this->faker->unique()->sentence(3), '.');

        return [
            'title' => ['en' => $title, 'ar' => 'مشروع '.$this->faker->numberBetween(1, 999)],
            'summary' => ['en' => $this->faker->sentence(12)],
            'description' => ['en' => $this->faker->paragraphs(3, true)],
            'client_name' => $this->faker->company(),
            'project_url' => $this->faker->url(),
            'started_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
            'completed_at' => $this->faker->dateTimeBetween('-5 months', 'now'),
            'status' => ProjectStatus::Published,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => ProjectStatus::Draft]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
