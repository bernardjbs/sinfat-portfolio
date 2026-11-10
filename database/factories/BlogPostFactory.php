<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'excerpt'      => fake()->paragraph(),
            'content'      => fake()->paragraphs(3, true),
            'category'     => 'development',
            'status'       => 'draft',
            'published_at' => null,
            'ai_generated' => false,
            'ai_model'     => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status'       => 'published',
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status'       => 'draft',
            'published_at' => null,
        ]);
    }

    public function aiGenerated(): static
    {
        return $this->state(fn () => [
            'ai_generated' => true,
            'ai_model'     => 'claude-sonnet-4-5',
        ]);
    }

    public function inSeries(Series $series, int $part): static
    {
        return $this->state(fn () => [
            'series_id'   => $series->id,
            'series_part' => $part,
        ]);
    }
}
