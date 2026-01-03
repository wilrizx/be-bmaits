<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nrp' => 'required|integer',
            'unit_kerja' => 'required|string|max:255',
            'vehicle_id' => 'required|integer',
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_peminjaman',
            'detail_keperluan' => 'required|string',
        ]);

        $booking = Booking::create($request->all());

        return response()->json([
            'message' => 'Peminjaman berhasil diajukan',
            'data' => $booking,
        ], 201);
    }

    public function checkByNrp($nrp){
        $bookings = Booking::where('nrp', $nrp)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }
        //
    
    public function index()
    {
        $bookings = Booking::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }
    //
}
