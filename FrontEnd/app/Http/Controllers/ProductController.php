<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
 
class ProductController extends Controller
{
  
    public function index()
    {
        
        $userId = Auth::id();
    
        // Retrieve products where user_id is not equal to current user's ID
        $product = Product::where('user_id', '!=', $userId)
                            ->orderBy('created_at', 'DESC')
                            ->get();
    
        return view('products.index', compact('product'));
    }
 
    public function create()
    {
        return view('products.create');
    }
  
 
    public function store(Request $request)
    {
      
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'product_code' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        $user_id = auth()->user()->id;
    
        Product::create(array_merge($request->all(), ['user_id' => $user_id]));
    
        return redirect()->route('dashboard')->with('success', 'Product added successfully');
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
  
        return view('products.show', compact('product'));
    }

    

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
  
        return view('products.edit', compact('product'));
    }
  
 
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
  
        $product->update($request->all());
  
        return redirect()->route('dashboard')->with('success', 'product updated successfully');
    }
  
 
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
  
        $product->delete();
  
        return redirect()->route('products')->with('success', 'product deleted successfully');
    }

    public function order(string $id)
    {
        $product = Product::findOrFail($id);

       
        $order = new Order();
        $order->user_id = auth()->user()->id; 
        $order->save();

        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $product->id;
        $orderItem->quantity = 1;
        $orderItem->save();

        return redirect()->route('orders.index')->with('success', 'Order placed successfully');
    }

    public function userProducts()
    {
        $user = Auth::user();

     
        if (!$user) {
            return redirect()->back()->with('error', 'User not authenticated');
        }

        
        $products = Product::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        return view('products.user_products', compact('products'));
    }

    public function dashboard()
{

    $products = Product::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();

    return view('dashboard', compact('products'));
}
}