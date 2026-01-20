<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function getAvailableVehicles(Request $request)
    {
        $request->validate([
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_peminjaman',
        ]);

        $tanggalPinjam = $request->tanggal_peminjaman;
        $tanggalKembali = $request->tanggal_kembali;

        $allVehicles = Vehicle::all();

        $bookedVehicleIds = Booking::where(function ($query) use ($tanggalPinjam, $tanggalKembali) {
            $query->where('tanggal_peminjaman', '<=', $tanggalKembali)
                ->where('tanggal_kembali', '>=', $tanggalPinjam);
        })
        ->whereIn('status_peminjaman', ['menunggu', 'disetujui'])
        ->pluck('vehicle_id')
        ->toArray();

        $availableVehicles = $allVehicles->whereNotIn('id', $bookedVehicleIds)->values();

        return response()->json([
            'success' => true, 
            'message' => 'Available vehicles retrieved successfully',
            'data' => $availableVehicles,
            'total' => $availableVehicles->count(),
        ], 200);
    }

    // 1. Submit form peminjaman (public)
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nrp' => 'required|integer',
            'unit_kerja' => 'required|string|max:255',
            'vehicle_id' => 'required|integer|exists:vehicles,id', 
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_peminjaman', 
            'detail_keperluan' => 'required|string|max:1000',
        ]);

        $conflictBooking = Booking::where('vehicle_id', $request->vehicle_id)
            ->where(function ($query) use ($request) {
                $query->where('tanggal_peminjaman', '<=', $request->tanggal_kembali)
                    ->where('tanggal_kembali', '>=', $request->tanggal_peminjaman);
            })
            ->whereIn('status_peminjaman', ['menunggu', 'disetujui'])
            ->exists();

        if ($conflictBooking) {
            return response()->json([
                'success' => false, // ✅ Tambahkan
                'message' => 'Kendaraan sudah dipinjam pada tanggal tersebut. Pilih kendaraan atau tanggal lain.',
            ], 409);
        }

        $booking = Booking::create([
            'nama' => $request->nama,
            'nrp' => $request->nrp,
            'unit_kerja' => $request->unit_kerja,
            'vehicle_id' => $request->vehicle_id,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_kembali' => $request->tanggal_kembali,
            'detail_keperluan' => $request->detail_keperluan,
            'status_peminjaman' => 'menunggu',
        ]);

        return response()->json([
            'success' => true, // ✅ Tambahkan
            'message' => 'Peminjaman berhasil diajukan',
            'data' => $booking->load('vehicle'),
        ], 201);
    }

    // 2. Cek status peminjaman berdasarkan NRP (public)
    public function checkByNrp($nrp)
    {
        $bookings = Booking::where('nrp', $nrp)
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false, // ✅ Tambahkan
                'message' => 'Tidak ada peminjaman ditemukan untuk NRP ini',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true, // ✅ Tambahkan
            'data' => $bookings,
            'total' => $bookings->count(),
        ], 200);
    }


    // 3. List semua bookings (untuk testing/admin)
    public function index(Request $request)
    {
        $query = Booking::with('vehicle');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status_peminjaman', $request->status);
        }

        // Search by nama, unit_kerja, or nrp
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%")
                  ->orWhere('nrp', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
            'total' => $bookings->count(),
        ], 200);
    }

    public function getPendingBookings()
    {
        $bookings = Booking::where('status_peminjaman', 'menunggu')
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pending bookings retrieved successfully',
            'data' => $bookings,
            'total' => $bookings->count(),
        ], 200);
    }

    // 4. Get jadwal kendaraan yang sudah disetujui (untuk kalender)
    public function getApprovedBookings()
    {
        $bookings = Booking::where('status_peminjaman', 'disetujui')
            ->with('vehicle')
            ->orderBy('tanggal_peminjaman', 'asc')
            ->get();

        return response()->json([
            'success' => true, // ✅ Tambahkan
            'data' => $bookings,
            'total' => $bookings->count(),
        ], 200);
    }


    // 5. Get jadwal berdasarkan vehicle_id (untuk kalender per kendaraan)
    public function getBookingsByVehicle($vehicleId)
    {
        $bookings = Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status_peminjaman', ['disetujui', 'menunggu'])
            ->with('vehicle')
            ->orderBy('tanggal_peminjaman', 'asc')
            ->get();

        return response()->json([
            'success' => true, // ✅ Tambahkan
            'data' => $bookings,
            'total' => $bookings->count(),
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
            })
            ->with('vehicle');

        if ($request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $bookings = $query->orderBy('tanggal_peminjaman', 'asc')->get();

        return response()->json([
            'success' => true, // ✅ Tambahkan
            'data' => $bookings,
            'total' => $bookings->count(),
        ], 200);
    }

    // 7. Approve booking (untuk admin - nanti)
    public function approve($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status_peminjaman !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking dengan status menunggu yang bisa disetujui',
                ], 400);
            }

            $booking->update(['status_peminjaman' => 'disetujui']);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disetujui',
                'data' => $booking->load('vehicle'),
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }
    }

    // 8. Reject booking (untuk admin - nanti)
    public function reject($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status_peminjaman !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking dengan status menunggu yang bisa ditolak',
                ], 400);
            }

            $booking->update(['status_peminjaman' => 'ditolak']);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman ditolak',
                'data' => $booking->load('vehicle'),
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }
    }
}
