<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    // Helper method to format success responses
    private function successResponse($data = [], $message = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    // Helper method to format error responses
    private function errorResponse($message, $statusCode)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    // Create a new order
    public function store(Request $request)
    {
        try {
            $request->validate([
                // Add any validation rules here as needed
            ]);

            // Get the authenticated user
            $user = Auth::user();

            // Create the order with user_id
            $order = Order::create([
                'user_id' => $user->id,
            ]);

            return $this->successResponse(['order' => $order], 'Order created successfully', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // View orders for the authenticated user
    public function index()
    {
        try {
            // Get the authenticated user's orders
            $orders = Auth::user()->orders()->orderByDesc('created_at')->get();

            return $this->successResponse(['orders' => $orders], 'Orders fetched successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
