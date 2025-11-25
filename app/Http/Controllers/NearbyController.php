<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NearbyController extends Controller
{
    public function index()
    {
        return view('nearby.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'numeric|max:50' // Default max 50km
        ]);

        $radius = $request->radius ?? 10;

        $merchants = User::nearby($request->lat, $request->lng, $radius)
            ->with('foodMenus')
            ->get();

        return response()->json([
            'success' => true,
            'merchants' => $merchants
        ]);
    }
}