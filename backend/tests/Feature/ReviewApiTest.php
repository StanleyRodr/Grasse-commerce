<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_buyer_with_delivered_order_can_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $address = Address::create(['user_id' => $user->id, 'label' => 'Casa', 'recipient' => 'Test', 'line1' => 'Calle 1', 'city' => 'CDMX', 'state' => 'CDMX', 'postal_code' => '06600', 'phone' => '5555555555']);

        $this->actingAs($user, 'sanctum')->postJson("/api/products/{$product->id}/reviews", ['rating' => 5, 'comment' => 'Excelente fragancia y duración.'])->assertForbidden();

        $order = $user->orders()->create(['address_id' => $address->id, 'status' => 'delivered', 'subtotal' => 100, 'shipping' => 150, 'total' => 250]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => $product->price, 'quantity' => 1]);
        $this->actingAs($user, 'sanctum')->postJson("/api/products/{$product->id}/reviews", ['rating' => 5, 'comment' => 'Excelente fragancia y duración.'])->assertCreated();
        $this->getJson("/api/products/{$product->id}/reviews")->assertOk()->assertJsonPath('data.data.0.verified_purchase', true);
    }
}
