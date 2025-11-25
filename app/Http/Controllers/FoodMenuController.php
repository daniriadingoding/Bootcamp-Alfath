<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use App\Models\User; // Penting: Import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FoodMenuController extends Controller
{
    public function __construct()
    {
        // Hanya user login (Admin & Merchant) yang bisa akses
        $this->middleware('auth'); 
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin melihat semua menu beserta info pemiliknya (merchant)
            $foodMenus = FoodMenu::with('user')->latest()->get();
        } else {
            // Merchant HANYA melihat menu miliknya sendiri
            $foodMenus = FoodMenu::where('user_id', $user->id)->latest()->get();
        }

        return view('foodmenu.index', compact('foodMenus'));
    }

    public function create()
    {
        // Variabel merchants hanya diisi jika user adalah Admin
        $merchants = [];
        if (Auth::user()->isAdmin()) {
            $merchants = User::where('role', 'merchant')->get();
        }

        return view('foodmenu.create', compact('merchants'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:3',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];

        if (Auth::user()->isAdmin()) {
            $rules['merchant_id'] = 'required|exists:users,id';
        }

        $validatedData = $request->validate($rules);

        if (Auth::user()->isAdmin()) {
            $userId = $request->merchant_id;
        } else {
            $userId = Auth::id();
        }

        $dataToSave = [
            'user_id' => $userId,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
        ];

        if ($request->hasFile('image')) {
            $dataToSave['image'] = $request->file('image')->store('food_images', 'public');
        }

        FoodMenu::create($dataToSave);

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil ditambahkan.');
    }

    public function edit(FoodMenu $foodMenu)
    {
        // Merchant ga boleh  mengedit menu orang lain
        if (!Auth::user()->isAdmin() && $foodMenu->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $merchants = [];
        if (Auth::user()->isAdmin()) {
            $merchants = User::where('role', 'merchant')->get();
        }

        return view('foodmenu.edit', compact('foodMenu', 'merchants'));
    }

    public function update(Request $request, FoodMenu $foodMenu)
    {
        // Merchant ga boleh mengupdate menu orang lain
        if (!Auth::user()->isAdmin() && $foodMenu->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'name' => 'required|string|min:3',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];

        if (Auth::user()->isAdmin()) {
            $rules['merchant_id'] = 'required|exists:users,id';
        }

        $validatedData = $request->validate($rules);

        // Update Data
        $foodMenu->name = $validatedData['name'];
        $foodMenu->description = $validatedData['description'];
        $foodMenu->price = $validatedData['price'];

        // Jika Admin, update pemilik menu (siapa tahu mau dipindahkan)
        if (Auth::user()->isAdmin()) {
            $foodMenu->user_id = $request->merchant_id;
        }

        if ($request->hasFile('image')) {
            if ($foodMenu->image) {
                Storage::disk('public')->delete($foodMenu->image);
            }
            $foodMenu->image = $request->file('image')->store('food_images', 'public');
        }

        $foodMenu->save();

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil diperbarui.');
    }

    public function destroy(FoodMenu $foodMenu)
    {
        if (!Auth::user()->isAdmin() && $foodMenu->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($foodMenu->image) {
            Storage::disk('public')->delete($foodMenu->image);
        }

        $foodMenu->delete();

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil dihapus.');
    }
}