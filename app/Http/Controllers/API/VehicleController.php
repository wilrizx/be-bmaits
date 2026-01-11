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

        // Transform data agar sesuai dengan format frontend
        $vehicles = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'nama' => $vehicle->nama_kendaraan,
                'jenis' => $vehicle->jenis_kendaraan,
                'warna' => $vehicle->warna_kendaraan,
                'plate' => $vehicle->nomor_polisi,
                'bbm' => $vehicle->bahan_bakar,
                'kapasitas' => $vehicle->kapasitas_penumpang . ' Orang',
                'status' => $vehicle->status_ketersediaan,
            ];
        });

        return response()->json([
            'data' => $vehicles,
        ], 200);

        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'warna' => 'required|string|max:50',
            'plate' => 'required|string|max:20|unique:vehicles,plate',
            'bbm' => 'required|string|max:50',
            'kapasitas' => 'required|string|max:50',
            'status' => 'required|in:available,borrowed,maintenance',
        ]);

        $vehicle = Booking::create($request->all());

        return response()->json([
            'message' => 'Vehicle created successfully',
            'data' => $vehicle,
        ], 201);

        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        return response()->json([
            'data' => $vehicle,
        ], 200);
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => 'sometimes|string|max:100',
            'warna' => 'sometimes|string|max:50',
            'plate' => 'sometimes|string|max:20|unique:vehicles,plate,' . $id,
            'bbm' => 'sometimes|string|max:50',
            'kapasitas' => 'sometimes|string|max:50',
            'status' => 'sometimes|in:available,borrowed,maintenance',
        ]);

        $vehicle->update($request->all());

        return response()->json([
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle,
        ], 200);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehicle deleted successfully',
        ], 200);
        //
    }
}
