<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    // Helper method to format success responses
    private function successResponse($data, $message = null, $statusCode = 200)
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

    // List all products
    public function index()
    {
        $products = Product::all();
        return $this->successResponse(['products' => $products],'Products Fetching successfully', 201);
    }

    // Create a new product
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|string',
                'product_code' => 'required|string|unique:products',
                'description' => 'required|string',
            ]);

            $user = Auth::user();

            $product = Product::create([
                'title' => $request->title,
                'price' => $request->price,
                'product_code' => $request->product_code,
                'description' => $request->description,
                'user_id' => $user->id,
            ]);

            return $this->successResponse(['product' => $product], 'Product created successfully', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Show a single product
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return $this->successResponse(['product' => $product]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    // Update a product
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|string',
                'product_code' => 'required|string|unique:products,product_code,' . $id,
                'description' => 'required|string',
            ]);

            $product = Product::findOrFail($id);
            $product->update([
                'title' => $request->title,
                'price' => $request->price,
                'product_code' => $request->product_code,
                'description' => $request->description,
            ]);

            return $this->successResponse(['product' => $product], 'Product updated successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Delete a product
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return $this->successResponse([], 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
