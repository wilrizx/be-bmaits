<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::orderBy('nama_kendaraan', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'total' => $vehicles->count(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kendaraan' => 'required|string|max:255',
            'jenis_kendaraan' => 'required|string|max:100',
            'warna_kendaraan' => 'required|string|max:50',
            'nomor_polisi' => 'required|string|max:20|unique:vehicles,nomor_polisi',
            'bahan_bakar' => 'required|string|max:50',
            'kapasitas_penumpang' => 'required|integer|min:1',
            'status_ketersediaan' => 'required|in:tersedia,dipinjam,maintenance',
        ]);

        $vehicle = Vehicle::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $vehicle,
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // ✅ FIX: Gunakan nama field yang sesuai database
            $request->validate([
                'nama_kendaraan' => 'sometimes|string|max:255',
                'jenis_kendaraan' => 'sometimes|string|max:100',
                'warna_kendaraan' => 'sometimes|string|max:50',
                'nomor_polisi' => 'sometimes|string|max:20|unique:vehicles,nomor_polisi,' . $id,
                'bahan_bakar' => 'sometimes|string|max:50',
                'kapasitas_penumpang' => 'sometimes|integer|min:1',
                'status_ketersediaan' => 'sometimes|in:tersedia,dipinjam,maintenance',
            ]);

            $vehicle->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Vehicle updated successfully',
                'data' => $vehicle,
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            
            // ✅ Optional: Cek apakah ada booking aktif untuk kendaraan ini
            $activeBookings = $vehicle->bookings()
                ->whereIn('status_peminjaman', ['menunggu', 'disetujui'])
                ->exists();
            
            if ($activeBookings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete vehicle with active bookings',
                ], 400);
            }

            $vehicle->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle deleted successfully',
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }
    }
}
