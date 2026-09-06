<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_fails_safely_when_stripe_is_not_configured(): void
    {
        config(['services.stripe.secret' => null]);
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $address->id, 'status' => 'pending']);

        $this->actingAs($user, 'sanctum')->postJson("/api/orders/{$order->id}/checkout")
            ->assertStatus(503);
    }

    public function test_webhook_fails_safely_when_secret_is_not_configured(): void
    {
        config(['services.stripe.webhook_secret' => null]);
        $this->postJson('/api/payments/stripe/webhook')->assertStatus(503);
    }
}
