<?php

declare(strict_types=1);

namespace Tests\Feature;

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
            ->assertJsonPath('data.0.id', $products->last()->id);
    }

    public function test_it_creates_a_product(): void
    {
        $payload = [
            'name' => 'Gaming Keyboard',
            'description' => 'Mechanical keyboard with RGB lighting.',
            'price' => 1250000,
            'stock' => 15,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Gaming Keyboard')
            ->assertJsonPath('data.stock', 15);

        $this->assertDatabaseHas('products', [
            'name' => 'Gaming Keyboard',
            'price' => '1250000.00',
            'stock' => 15,
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
            'name' => 'Updated Product',
            'description' => null,
            'price' => 99999.99,
            'stock' => 10,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Product')
            ->assertJsonPath('data.description', null)
            ->assertJsonPath('data.stock', 10);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => '99999.99',
            'stock' => 10,
        ]);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_it_validates_product_payload(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'price' => -1,
            'stock' => -5,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }
}
