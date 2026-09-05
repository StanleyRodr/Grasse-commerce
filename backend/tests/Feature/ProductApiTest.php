<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_are_paginated_with_frontend_contract(): void
    {
        Product::factory()->createMany([
            ['name' => 'Santal 33', 'house' => 'Le Labo', 'occasion' => 'Diario', 'scent_family' => 'Amaderada', 'price' => 4250],
            ['name' => 'Gris Charnel', 'house' => 'BDK Parfums', 'occasion' => 'Noche', 'scent_family' => 'Ambarada', 'price' => 3120],
        ]);

        $this->getJson('/api/products?occasion=Diario&per_page=1')
            ->assertOk()
            ->assertJsonPath('meta.currentPage', 1)
            ->assertJsonPath('meta.lastPage', 1)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.scentFamily', 'Amaderada')
            ->assertJsonPath('data.0.reviews', 0);
    }

    public function test_products_can_be_searched_and_sorted(): void
    {
        Product::factory()->createMany([
            ['name' => 'Santal 33', 'house' => 'Le Labo', 'price' => 4250],
            ['name' => 'Another 13', 'house' => 'Le Labo', 'price' => 3980],
        ]);

        $this->getJson('/api/products?search=Another&sort=price-high')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Another 13');
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->getJson('/api/products/999')->assertNotFound();
    }
}
