<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodMenu;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
            $orders = $ordersQuery->whereHas('items.foodMenu', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        } else if ($user->isAdmin()) {
            $orders = $ordersQuery->get();
        } else {
            $orders = $ordersQuery->where('user_id', $user->id)->get();
        }

        return view('orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,onprogress,done,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function create(Request $request)
    {
        if ($request->has('merchant_id')) {
            $merchantId = $request->merchant_id;
            $foodItems = FoodMenu::where('user_id', $merchantId)->get();
            $merchant = User::find($merchantId);
        } else {
            $foodItems = collect();
            $merchant = null;
        }

        return view('orders.menu', compact('foodItems', 'merchant'));
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
            'payment_status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $foodMenu = FoodMenu::find($item['food_menu_id']);

            $order->items()->create([
                'food_menu_id' => $item['food_menu_id'],
                'quantity' => $item['quantity'],
                'price' => $foodMenu->price,
            ]);
        }

        return redirect()->route('orders.show', $order);
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
