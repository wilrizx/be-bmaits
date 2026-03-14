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
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $tanggalPinjam = $request->tanggal_pinjam;
        $tanggalKembali = $request->tanggal_kembali;

        // Cari ID kendaraan yang TIDAK tersedia (sudah ada booking yang beririsan)
        $bookedVehicleIds = Booking::whereIn('status_booking', ['menunggu', 'disetujui'])
            ->where(function ($query) use ($tanggalPinjam, $tanggalKembali) {
                /* Logika Irisan:
                Booking lama menabrak jadwal baru jika:
                Tanggal Pinjam Lama <= Tanggal Kembali Baru 
                AND 
                Tanggal Kembali Lama >= Tanggal Pinjam Baru
                */
                $query->where('tanggal_pinjam', '<=', $tanggalKembali)
                    ->where('tanggal_kembali', '>=', $tanggalPinjam);
            })
            ->pluck('vehicle_id')
            ->unique()
            ->toArray();

        // Ambil kendaraan yang ID-nya tidak ada dalam daftar bookedVehicleIds
        $availableVehicles = Vehicle::whereNotIn('id', $bookedVehicleIds)->get();

        return response()->json([
            'success' => true, 
            'message' => 'Kendaraan tersedia berhasil dimuat',
            'data' => $availableVehicles,
            'total' => $availableVehicles->count(),
        ], 200);
    }

    // 1. Submit form pinjam (public)
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nrp' => 'required|integer',
            'unit_kerja' => 'required|string|max:255',
            'vehicle_id' => 'required|integer|exists:vehicle,id', // Pastikan nama tabel benar (biasanya jamak 'vehicles')
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam', 
            'keperluan' => 'required|string|max:1000',
        ]);

        // Cek apakah kendaraan masih dipinjam/sudah dibooking pada tanggal tersebut
        $conflictBooking = Booking::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status_booking', ['menunggu', 'disetujui'])
            ->where(function ($query) use ($request) {
                $query->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
                    ->where('tanggal_kembali', '>=', $request->tanggal_pinjam);
            })
            ->exists();

        if ($conflictBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan sudah dipinjam atau dalam status menunggu persetujuan pada tanggal tersebut.',
            ], 409);
        }

        $booking = Booking::create([
            'nama' => $request->nama,
            'nrp' => $request->nrp,
            'unit_kerja' => $request->unit_kerja,
            'vehicle_id' => $request->vehicle_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'keperluan' => $request->keperluan,
            'status_booking' => 'menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan',
            'data' => $booking->load('vehicle'),
        ], 201);
    }

    // 2. Cek status pinjam berdasarkan NRP (public)
    public function checkByNrp($nrp)
    {
        $bookings = Booking::where('nrp', $nrp)
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false, // ✅ Tambahkan
                'message' => 'Tidak ada pinjam ditemukan untuk NRP ini',
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
            $query->where('status_booking', $request->status);
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
        $bookings = Booking::where('status_booking', 'menunggu')
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
        $bookings = Booking::where('status_booking', 'disetujui') // ✅ Gunakan 'status_pengajuan'
            ->with('vehicle')
            ->orderBy('tanggal_pinjam', 'asc')
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
            ->whereIn('status_booking', ['disetujui', 'menunggu'])
            ->with('vehicle')
            ->orderBy('tanggal_pinjam', 'asc')
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

        $query = Booking::where('status_booking', 'disetujui')
            ->where(function($q) use ($request) {
                $q->whereBetween('tanggal_pinjam', [$request->start_date, $request->end_date])
                ->orWhereBetween('tanggal_kembali', [$request->start_date, $request->end_date])
                ->orWhere(function($q2) use ($request) {
                    $q2->where('tanggal_pinjam', '<=', $request->start_date)
                        ->where('tanggal_kembali', '>=', $request->end_date);
                });
            })
            ->with('vehicle');

        if ($request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $bookings = $query->orderBy('tanggal_pinjam', 'asc')->get();

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
            $booking = Booking::with('vehicle')->findOrFail($id);
            
            if ($booking->status_booking !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking dengan status menunggu yang bisa disetujui',
                ], 400);
            }

            // 1. Update Status
            $booking->update(['status_booking' => 'disetujui']);

            // 2. Logic Generate File (Contoh: Menggunakan library DomPDF)
            // Di sini kita memanggil fungsi internal untuk membuat PDF
            $fileUrl = $this->generateBookingPdf($booking);

            return response()->json([
                'success' => true,
                'message' => 'Pinjam disetujui dan dokumen berhasil dibuat',
                'data' => $booking,
                'download_url' => $fileUrl // Client (Next.js) bisa lanjukan ke download
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 8. Reject booking (untuk admin - nanti)
    public function reject($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status_booking !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya booking dengan status menunggu yang bisa ditolak',
                ], 400);
            }

            $booking->update(['status_booking' => 'ditolak']);

            return response()->json([
                'success' => true,
                'message' => 'pinjam ditolak',
                'data' => $booking->load('vehicle'),
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }
    }
    
    // public function show($id)
    // {
    //     $booking = Booking::with('vehicle')->findOrFail($id);
    //     return response()->json($booking);
    // }

    public function show($id)
    {
        try {
            // Mengambil data booking beserta detail kendaraannya
            $booking = Booking::with('vehicle')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Detail peminjaman berhasil diambil',
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    private function generateBookingPdf($booking)
    {
        // Data ini diambil langsung dari hasil form yang disimpan di database
        $data = [
            'nomor_surat' => 'SRT/' . $booking->id . '/' . date('Y'),
            'nama'        => $booking->nama,
            'nrp'         => $booking->nrp,
            'unit'        => $booking->unit_kerja,
            'kendaraan'   => $booking->vehicle->nama_kendaraan, // Asumsi kolom di tabel vehicle
            'plat'        => $booking->vehicle->no_plat,
            'tgl_pinjam'  => $booking->tanggal_pinjam,
            'tgl_kembali' => $booking->tanggal_kembali,
            'keperluan'   => $booking->keperluan,
            'tgl_cetak'   => now()->format('d F Y'),
        ];

        // Logika pembuatan file (Contoh simpan di storage)
        // $pdf = Pdf::loadView('pdf.surat_peminjaman', $data);
        // $fileName = 'Surat_Pinjam_' . $booking->id . '.pdf';
        // Storage::put('public/documents/' . $fileName, $pdf->output());

        // return asset('storage/documents/' . $fileName);
        
        return "URL_FILE_HASIL_GENERATE"; 
    }

    public function getCalendarEvents()
    {
        $bookings = Booking::where('status_booking', 'disetujui')
            ->with('vehicle')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->vehicle->nama_kendaraan . 
                            ' - ' . $booking->unit_kerja,
                    'start' => $booking->tanggal_pinjam,
                    'end'   => date('Y-m-d', strtotime($booking->tanggal_kembali . ' +1 day')),
                    'vehicle_id' => $booking->vehicle_id,
                ];
            });

        return response()->json($bookings);
    }
}
