<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware('role:merchant')->only(['updateStatus']);
    }

    public function index()
    {
        $user = Auth::user();
        $ordersQuery = Order::with(['user', 'items.foodMenu'])->latest();

        if ($user->isMerchant()) {
            $orders = $ordersQuery->get();
        } else {
            $orders = $ordersQuery->where('user_id', $user->id)->get();
        }

        return view('orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:onprogress,done',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function create()
    {
        $foodItems = FoodMenu::all();
        return view('orders.menu', compact('foodItems')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.food_menu_id' => 'required|exists:food_menus,id',
            'items.*.quantity' => 'required|integer|min:1', 
            'total_price' => 'required|numeric|min:0', 
        ]);
        
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $request->total_price,
            'status' => 'pending',
            'address' => $request->address,
        ]);

        foreach ($request->items as $item) {
            $foodMenu = FoodMenu::find($item['food_menu_id']);
            
            $order->items()->create([ 
                'food_menu_id' => $item['food_menu_id'],
                'quantity' => $item['quantity'],
                'price' => $foodMenu->price, 
            ]);
        }

        return redirect()->route('orders.show', $order); // [cite: 137]
    }

    public function show(Order $order)
    {
        if (Auth::user()->isCustomer() && $order->user_id !== Auth::id()) {
             abort(403, 'AKSES DITOLAK');
        }
        
        $order->load(['user', 'items.foodMenu']);

        return view('orders.show', compact('order'));
    }
}