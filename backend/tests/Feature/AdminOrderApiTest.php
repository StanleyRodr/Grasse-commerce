<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_update_order_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $customer->id]);
        $order = Order::factory()->create(['user_id' => $customer->id, 'address_id' => $address->id]);

        $this->actingAs($admin, 'sanctum')->getJson('/api/admin/orders')
            ->assertOk()->assertJsonPath('data.data.0.id', $order->id);

        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'shipped'])
            ->assertOk()->assertJsonPath('data.status', 'shipped');
    }
}
