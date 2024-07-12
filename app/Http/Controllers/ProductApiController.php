<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $products = Product::all();
            Log::info('All products fetched successfully.');
            return $this->successResponse(['products' => $products], 'Products fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error fetching all products.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    //Get my product list

    public function getMyProducts()
    {
        try {
            $userId = Auth::id();
            $products = Product::where('user_id', $userId)->get();
            Log::info('User products fetched successfully.', ['user_id' => $userId]);
            return $this->successResponse(['products' => $products], 'Products fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user products.', ['user_id' => $userId, 'exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
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

            Log::info('Product created successfully.', ['product_id' => $product->id, 'user_id' => $user->id]);
            return $this->successResponse(['product' => $product], 'Product created successfully', 201);
        } catch (ValidationException $e) {
            Log::error('Validation error during product creation.', ['errors' => $e->errors()]);
            return $this->errorResponse($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            Log::error('Error during product creation.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Show a single product
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            Log::info('Product fetched successfully.', ['product_id' => $id]);
            return $this->successResponse(['product' => $product], 'Product fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error fetching product.', ['product_id' => $id, 'exception' => $e->getMessage()]);
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

            Log::info('Product updated successfully.', ['product_id' => $id]);
            return $this->successResponse(['product' => $product], 'Product updated successfully');
        } catch (ValidationException $e) {
            Log::error('Validation error during product update.', ['errors' => $e->errors()]);
            return $this->errorResponse($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            Log::error('Error during product update.', ['product_id' => $id, 'exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Delete a product
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            Log::info('Product deleted successfully.', ['product_id' => $id]);
            return $this->successResponse([], 'Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error during product deletion.', ['product_id' => $id, 'exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

   //Get ordered Product list

    public function getOrderedProducts()
    {
        try {
            $products = Product::has('orderItems')
                               ->distinct()
                               ->get();
            Log::info('Ordered products fetched successfully.');
            return $this->successResponse(['products' => $products], 'Ordered products fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error fetching ordered products.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    //Product search API

    public function search(Request $request)
    {
        try {
            $request->validate([
                'search' => 'required|string',
            ]);

            $search = $request->input('search');
            $products = Product::where('title', 'like', "%$search%")
                               ->orWhere('description', 'like', "%$search%")
                               ->get();

            Log::info('Products search completed successfully.', ['search_term' => $search]);
            return $this->successResponse(['products' => $products], 'Search results fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error during product search.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
