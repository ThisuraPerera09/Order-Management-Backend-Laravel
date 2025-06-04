<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderApiController extends Controller
{


    //Create a Order
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1|max:10',
            ]);
    
      
            $order = Order::create([
                'user_id' => Auth::id(),
            ]);
    
          
            $hasInvalidProduct = false;
    
       
            foreach ($request->products as $product) {
             
                $existingProduct = Product::findOrFail($product['product_id']);
                
                if ($existingProduct->user_id == Auth::id()) {
                    $hasInvalidProduct = true;
                    continue;
                }
    
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                ]);
            }
    
            if ($hasInvalidProduct) {
                $order->delete();
                throw ValidationException::withMessages([
                    'products.*.product_id' => 'You cannot order your own product.',
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => ['order' => $order],
            ], 201);
            Log::info('Execution time for fetching orders: ' . $executionTime . ' seconds');
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    


    

//My orders
    public function index()
{
    try {
        $userId = auth()->id(); 

        $orders = Order::where('user_id', $userId)
            ->with('user', 'orderItems')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['orders' => $orders],
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}




//Orders that other users make on my products

public function ordersWithMyProducts()
{
    $userId = Auth::id();

    // Get the orders where the products belong to the authenticated user but the orders are placed by other users
    $orders = Order::whereHas('orderItems.product', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })->where('user_id', '!=', $userId)->with('orderItems.product')->get();

    return response()->json($orders);
}
}
