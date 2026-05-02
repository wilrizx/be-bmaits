<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\UnitKerjaController;

Route::prefix('v1')->group(function () {

    // --- AUTH ADMIN ---
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [LoginController::class, 'me']);
            Route::delete('/logout', [LoginController::class, 'destroy']);
        });
    });

    // --- BOOKING ---
    Route::prefix('booking')->group(function () {
        // --- Public Access (Tanpa Login) ---
        Route::get('/available-vehicles', [BookingController::class, 'getAvailableVehicles']);
        Route::post('/', [BookingController::class, 'store'])->middleware('throttle:10,1');
        Route::get('/check/{nrp}', [BookingController::class, 'checkByNrp']);

        // Route Kunci untuk E-Surat: FE butuh ambil detail data untuk di-render ke PDF
        Route::get('/detail/{id}', [BookingController::class, 'show']);

        Route::get('/approved-list', [BookingController::class, 'getApprovedBookings']);
        Route::get('/schedule/vehicle/{vehicleId}', [BookingController::class, 'getBookingsByVehicle']);
        Route::get('/schedule/range', [BookingController::class, 'getBookingsByDateRange']);

        // --- Admin Protected Access (Butuh Login) ---
        // Route::middleware('auth:sanctum')->group(function () {

        // });

        Route::get('/', [BookingController::class, 'index']);
        Route::get('/pending', [BookingController::class, 'getPendingBookings']);
        Route::patch('/{id}/approve', [BookingController::class, 'approve']);
        Route::patch('/{id}/reject', [BookingController::class, 'reject']);
    });

    // --- VEHICLES & UNIT KERJA ---
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
    Route::get('/unit-kerja', [UnitKerjaController::class, 'index']); // Diperlukan Form Publik

    // Route kalender
    Route::get('/calendar', [BookingController::class, 'getCalendarEvents']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/vehicles', [VehicleController::class, 'store']);
        Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);
    });
});
