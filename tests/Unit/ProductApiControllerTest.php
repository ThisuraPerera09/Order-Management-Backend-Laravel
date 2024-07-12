<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProductApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_products()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'products' => [],
                     ],
                 ]);
    }

    public function test_store_creates_new_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $productData = [
            'title' => 'Test Product',
            'price' => '50.00',
            'product_code' => 'TEST001',
            'description' => 'This is a test product.',
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'product' => [],
                     ],
                 ]);

        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'product_code' => 'TEST001',
        ]);
    }

    public function test_update_updates_existing_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create([
            'title' => 'Original Product',
            'product_code' => 'ORIG001',
        ]);

        $updateData = [
            'title' => 'Updated Product',
            'price' => '60.00',
            'product_code' => 'UPD001',
            'description' => 'This is an updated product.',
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'product' => [],
                     ],
                 ]);

        $this->assertDatabaseHas('products', [
            'title' => 'Updated Product',
            'product_code' => 'UPD001',
        ]);
    }

    public function test_show_returns_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->get("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'product' => [],
                     ],
                 ]);
    }

    public function test_destroy_deletes_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create();

        $response = $this->delete("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Product deleted successfully',
                 ]);

        $this->assertDeleted($product);
    }

    public function test_search_returns_matching_products()
    {
        Product::factory()->create([
            'title' => 'Test Product 1',
            'description' => 'This is a test product.',
        ]);
        Product::factory()->create([
            'title' => 'Test Product 2',
            'description' => 'Another test product.',
        ]);

        $response = $this->postJson('/api/products/search', ['search' => 'Test']);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'products' => [],
                     ],
                 ])
                 ->assertJsonCount(2, 'data.products');
    }
}
