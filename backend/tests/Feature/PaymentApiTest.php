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

    public function test_signed_checkout_webhook_moves_order_to_processing(): void
    {
        $secret = 'whsec_test_secret';
        config(['services.stripe.webhook_secret' => $secret]);
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['user_id' => $user->id, 'address_id' => $address->id, 'status' => 'pending']);
        $payload = json_encode([
            'id' => 'evt_test', 'object' => 'event', 'api_version' => '2025-03-31', 'created' => time(),
            'data' => ['object' => ['id' => 'cs_test', 'object' => 'checkout.session', 'metadata' => ['order_id' => (string) $order->id]]],
            'livemode' => false, 'pending_webhooks' => 1, 'type' => 'checkout.session.completed',
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $this->call('POST', '/api/payments/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk()->assertJson(['received' => true]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_secret']);
        $this->call('POST', '/api/payments/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => 't=1,v1=invalid', 'CONTENT_TYPE' => 'application/json',
        ], '{}')->assertStatus(400);
    }
}
