<?php

namespace Tests\Feature\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiControllerFeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_method_returns_orders_for_authenticated_user()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'orders' => [
                        '*' => [
                            'id',
                            'user_id',
                            'created_at',
                            'updated_at',
                            'user',
                            'orderItems',
                        ],
                    ],
                ],
            ]);
    }
}
