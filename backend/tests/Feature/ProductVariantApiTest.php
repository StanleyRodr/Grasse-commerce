<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_exposes_variants_and_admin_can_manage_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $created = $this->actingAs($admin, 'sanctum')->postJson("/api/admin/products/{$product->id}/variants", ['label' => '50 ml', 'volume_ml' => 50, 'price' => 1500, 'stock' => 20])->assertCreated();
        $variantId = $created->json('data.id');
        $this->getJson("/api/products/{$product->id}")->assertOk()->assertJsonPath('data.variants.0.volumeMl', 50);
        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/variants/{$variantId}", ['stock' => 5])->assertOk()->assertJsonPath('data.stock', 5);
        $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/variants/{$variantId}")->assertOk();
        $this->assertDatabaseMissing('product_variants', ['id' => $variantId]);
    }
}
