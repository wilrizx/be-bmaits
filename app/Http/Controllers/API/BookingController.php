<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // 1. Submit form peminjaman (public)
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

    // 2. Cek status peminjaman berdasarkan NRP (public)
    public function checkByNrp($nrp)
    {
        $bookings = Booking::where('nrp', $nrp)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    // 3. List semua bookings (untuk testing/admin)
    public function index()
    {
        $bookings = Booking::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    // 4. Get jadwal kendaraan yang sudah disetujui (untuk kalender)
    public function getApprovedBookings()
    {
        $bookings = Booking::where('status_peminjaman', 'disetujui')
            ->orderBy('tanggal_peminjaman', 'asc')
            ->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    // 5. Get jadwal berdasarkan vehicle_id (untuk kalender per kendaraan)
    public function getBookingsByVehicle($vehicleId)
    {
        $bookings = Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status_peminjaman', ['disetujui', 'menunggu'])
            ->orderBy('tanggal_peminjaman', 'asc')
            ->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    // 6. Get jadwal berdasarkan tanggal range (untuk kalender)
    public function getBookingsByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'nullable|integer',
        ]);

        $query = Booking::where('status_peminjaman', 'disetujui')
            ->where(function($q) use ($request) {
                $q->whereBetween('tanggal_peminjaman', [$request->start_date, $request->end_date])
                  ->orWhereBetween('tanggal_kembali', [$request->start_date, $request->end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('tanggal_peminjaman', '<=', $request->start_date)
                         ->where('tanggal_kembali', '>=', $request->end_date);
                  });
            });

        if ($request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $bookings = $query->orderBy('tanggal_peminjaman', 'asc')->get();

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    // 7. Approve booking (untuk admin - nanti)
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status_peminjaman = 'disetujui';
        $booking->save();

        return response()->json([
            'message' => 'Peminjaman berhasil disetujui',
            'data' => $booking,
        ], 200);
    }

    // 8. Reject booking (untuk admin - nanti)
    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status_peminjaman = 'ditolak';
        $booking->save();

        return response()->json([
            'message' => 'Peminjaman ditolak',
            'data' => $booking,
        ], 200);
    }
}
