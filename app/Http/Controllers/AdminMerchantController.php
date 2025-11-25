<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminMerchantController extends Controller
{
    // Tampilkan daftar semua merchant.
    public function index()
    {
        // Ambil semua user dengan role 'merchant'
        $merchants = User::where('role', 'merchant')->latest()->get();
        return view('admin.merchants.index', compact('merchants'));
    }

    // Tampilkan form edit lokasi untuk merchant tertentu.
    public function edit(User $merchant)
    {
        if ($merchant->role !== 'merchant') {
            abort(404);
        }
        
        return view('admin.merchants.edit', compact('merchant'));
    }

    // Simpan perubahan lokasi merchant.
    public function update(Request $request, User $merchant)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $merchant->update([
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.merchants.index')
            ->with('success', 'Lokasi merchant berhasil diperbarui.');
    }
}