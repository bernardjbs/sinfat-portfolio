<?php

namespace Database\Factories;

use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SeriesFactory extends Factory
{
    protected $model = Series::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'slug'   => Str::slug($title),
            'title'  => $title,
            'pitch'  => fake()->sentence(12),
            'status' => 'writing',
        ];
    }

    public function complete(): static
    {
        return $this->state(fn () => ['status' => 'complete']);
    }
}
