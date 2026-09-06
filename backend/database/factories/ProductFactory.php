<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'house' => fake()->company(),
            'category' => 'Floral',
            'scent_family' => 'Floral',
            'occasion' => 'Diario',
            'price' => fake()->randomFloat(2, 1000, 6000),
            'rating' => fake()->randomFloat(1, 3, 5),
            'reviews_count' => 0,
            'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601',
            'badge' => null,
            'stock' => 25,
            'description' => 'Una composición elegante para todos los días.',
            'notes' => ['Notas frescas', 'Maderas suaves'],
        ];
    }
}
