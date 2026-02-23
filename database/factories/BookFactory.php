<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Publisher;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'isbn' => fake()->isbn13(),
            'published_at' => fake()->date(),
            'google_books_id' => fake()->uuid(),
            'google_books_synced_at' => now(),
            'language' => 'pt',
            'name' => fake()->sentence(3),
            'publisher_id' => Publisher::factory(),

            'bibliography' => fake()->paragraph(),
            'cover_image' => fake()->imageUrl(),
            'price' => fake()->randomFloat(2, 5, 100),
            'stock' => fake()->numberBetween(1, 10),
        ];
    }
}
