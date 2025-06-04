<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();
    
        // Retrieve orders where user_id equals the current user's ID
        $orders = Order::where('user_id', $userId)
                        ->orderBy('created_at', 'DESC')
                        ->get();
    
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $order = Order::create([
            'user_id' => $request->user_id, 
        ]);

        return redirect()->route('orders.index')->with('success', 'Order added successfully');
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('orders.show', compact('order'));
    }

}
 
