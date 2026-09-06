<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAddressApiTest extends TestCase
{
    use RefreshDatabase;

    private function addressPayload(): array
    {
        return ['label' => 'Casa', 'recipient' => 'Lucía Méndez', 'line1' => 'Av. Reforma 100', 'city' => 'CDMX', 'state' => 'CDMX', 'postal_code' => '06600', 'phone' => '5555555555', 'is_default' => true];
    }

    public function test_user_can_manage_default_address(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/addresses', $this->addressPayload())->assertCreated();
        $id = $response->json('data.id');
        $this->actingAs($user, 'sanctum')->postJson('/api/addresses', [...$this->addressPayload(), 'label' => 'Trabajo', 'is_default' => false]);
        $this->actingAs($user, 'sanctum')->postJson("/api/addresses/{$id}/default")->assertOk();
        $this->assertTrue(Address::findOrFail($id)->is_default);
        $this->assertFalse(Address::where('user_id', $user->id)->where('id', '!=', $id)->firstOrFail()->is_default);
    }

    public function test_user_can_create_order_from_cart_and_cart_is_cleared(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);
        $address = $user->addresses()->create($this->addressPayload());
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')->postJson('/api/orders', ['address_id' => $address->id])
            ->assertCreated()->assertJsonPath('data.status', 'pending')->assertJsonPath('data.total', 4000);

        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_empty_cart_cannot_create_order(): void
    {
        $user = User::factory()->create();
        $address = $user->addresses()->create($this->addressPayload());
        $this->actingAs($user, 'sanctum')->postJson('/api/orders', ['address_id' => $address->id])->assertUnprocessable();
    }

    public function test_creating_order_decrements_product_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);
        $address = $user->addresses()->create($this->addressPayload());
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')->postJson('/api/orders', ['address_id' => $address->id])->assertCreated();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_order_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);
        $address = $user->addresses()->create($this->addressPayload());
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')->postJson('/api/orders', ['address_id' => $address->id])
            ->assertUnprocessable();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('orders', 0);
    }
}
