<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartWishlistApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_and_update_cart_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1200]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertCreated()
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.subtotal', 2400);

        $item = CartItem::query()->firstOrFail();
        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/cart/items/{$item->id}", ['quantity' => 3])
            ->assertOk()
            ->assertJsonPath('data.total', 3600);
    }

    public function test_cart_items_cannot_be_modified_by_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($owner, 'sanctum')->postJson('/api/cart/items', ['product_id' => $product->id]);
        $item = CartItem::query()->firstOrFail();

        $this->actingAs($otherUser, 'sanctum')
            ->patchJson("/api/cart/items/{$item->id}", ['quantity' => 4])
            ->assertNotFound();
    }

    public function test_user_can_add_and_remove_wishlist_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/wishlist', ['product_id' => $product->id])
            ->assertCreated()
            ->assertJsonPath('data.product.id', $product->id);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/wishlist')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/wishlist/{$product->id}")
            ->assertOk();

        $this->assertDatabaseCount('wishlists', 0);
    }
}
