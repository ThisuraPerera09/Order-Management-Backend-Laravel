<?php

namespace Tests\Unit\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_method_creates_order_with_valid_products()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(2)->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders', [
                'products' => [
                    ['product_id' => $products[0]->id, 'quantity' => 2],
                    ['product_id' => $products[1]->id, 'quantity' => 1],
                ]
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order created successfully',
            ]);

        // Assert the order and order items were created
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);

        $orderItems = OrderItem::where('order_id', $order->id)->get();
        $this->assertCount(2, $orderItems);
        $this->assertEquals($products[0]->id, $orderItems[0]->product_id);
        $this->assertEquals(2, $orderItems[0]->quantity);
        $this->assertEquals($products[1]->id, $orderItems[1]->product_id);
        $this->assertEquals(1, $orderItems[1]->quantity);
    }

    public function test_store_method_handles_invalid_products()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/orders', [
                'products' => [
                    ['product_id' => $products[0]->id, 'quantity' => 2],
                    ['product_id' => $products[1]->id, 'quantity' => 1],
                ]
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
            ]);
    }
}
