<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada

class FoodMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foodMenus = FoodMenu::all();
        return view('foodmenu.index', compact('foodMenus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('foodmenu.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|unique:food_menus,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food_images', 'public');
            
            $validatedData['image'] = $imagePath;
        }

        FoodMenu::create($validatedData);

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FoodMenu $foodMenu)
    {
        return view('foodmenu.show', compact('foodMenu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodMenu $foodMenu)
    {
        return view('foodmenu.edit', compact('foodMenu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodMenu $foodMenu)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|unique:food_menus,name,' . $foodMenu->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($foodMenu->image) {
                Storage::disk('public')->delete($foodMenu->image);
            }
            
            $imagePath = $request->file('image')->store('food_images', 'public');
            
            $validatedData['image'] = $imagePath;
        }

        $foodMenu->update($validatedData);

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodMenu $foodMenu)
    {
        if ($foodMenu->image) {
            Storage::disk('public')->delete($foodMenu->image);
        }

        $foodMenu->delete();

        return redirect()->route('foodmenu.index')->with('success', 'Menu makanan berhasil dihapus.');
    }
}