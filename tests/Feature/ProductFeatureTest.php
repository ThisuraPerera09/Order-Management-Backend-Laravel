<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProductApiControllerFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_products()
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'products' => [],
                     ],
                 ])
                 ->assertJsonCount(3, 'data.products');
    }

    public function test_can_create_product()
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

    // Add more feature tests for other endpoints as needed...
}
