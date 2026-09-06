<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['name' => 'Test Perfume', 'house' => 'Grasse', 'category' => 'Floral', 'scent_family' => 'Floral', 'occasion' => 'Diario', 'price' => 1500, 'image' => 'https://example.com/perfume.jpg', 'stock' => 40];

        $created = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/products', $payload)->assertCreated();
        $id = $created->json('data.id');
        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/products/{$id}", ['stock' => 12])->assertOk()->assertJsonPath('data.stock', 12);
        $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/products/{$id}")->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $id]);
    }

    public function test_customer_cannot_manage_products(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum')->postJson('/api/admin/products', [])->assertForbidden();
    }
}
