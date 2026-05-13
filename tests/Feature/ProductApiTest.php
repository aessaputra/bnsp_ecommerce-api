<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products(): void
    {
        $products = Product::factory()->count(2)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $products->last()->id)
            ->assertJsonPath('meta.per_page', 15);
    }

    public function test_it_limits_product_pagination_size(): void
    {
        Product::factory()->count(105)->create();

        $response = $this->getJson('/api/products?per_page=200');

        $response->assertOk()
            ->assertJsonCount(100, 'data')
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_it_creates_a_product(): void
    {
        $payload = [
            'sku' => 'KB-RGB-001',
            'name' => 'Gaming Keyboard',
            'description' => 'Mechanical keyboard with RGB lighting.',
            'price' => 1250000,
            'stock' => 15,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.sku', 'KB-RGB-001')
            ->assertJsonPath('data.name', 'Gaming Keyboard')
            ->assertJsonPath('data.slug', 'gaming-keyboard')
            ->assertJsonPath('data.stock', 15)
            ->assertJsonPath('data.status', ProductStatus::Active->value);

        $this->assertDatabaseHas('products', [
            'sku' => 'KB-RGB-001',
            'name' => 'Gaming Keyboard',
            'slug' => 'gaming-keyboard',
            'price' => '1250000.00',
            'stock' => 15,
            'status' => ProductStatus::Active->value,
        ]);
    }

    public function test_it_shows_a_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Wireless Mouse',
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Wireless Mouse');
    }

    public function test_it_updates_a_product(): void
    {
        $product = Product::factory()->create([
            'stock' => 5,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'sku' => 'UPDATED-001',
            'name' => 'Updated Product',
            'slug' => 'Updated Product',
            'description' => null,
            'price' => 99999.99,
            'stock' => 10,
            'status' => ProductStatus::Draft->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.sku', 'UPDATED-001')
            ->assertJsonPath('data.name', 'Updated Product')
            ->assertJsonPath('data.slug', 'updated-product')
            ->assertJsonPath('data.description', null)
            ->assertJsonPath('data.stock', 10)
            ->assertJsonPath('data.status', ProductStatus::Draft->value);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => 'UPDATED-001',
            'name' => 'Updated Product',
            'slug' => 'updated-product',
            'price' => '99999.99',
            'stock' => 10,
            'status' => ProductStatus::Draft->value,
        ]);
    }

    public function test_it_patches_product_stock(): void
    {
        $product = Product::factory()->create([
            'stock' => 5,
        ]);

        $response = $this->patchJson("/api/products/{$product->id}", [
            'stock' => 25,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.stock', 25);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 25,
        ]);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_it_validates_product_payload(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'sku' => '',
            'price' => -1,
            'stock' => -5,
            'status' => 'unknown',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'sku', 'price', 'stock', 'status']);
    }

    public function test_it_validates_unique_product_identifiers(): void
    {
        Product::factory()->create([
            'sku' => 'DUPLICATE-SKU',
            'slug' => 'duplicate-slug',
        ]);

        $response = $this->postJson('/api/products', [
            'sku' => 'DUPLICATE-SKU',
            'name' => 'Duplicate Product',
            'slug' => 'duplicate-slug',
            'price' => 10000,
            'stock' => 1,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['sku', 'slug']);
    }
}
