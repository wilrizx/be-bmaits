<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\UnitKerjaController;

// Grouping utama V1
Route::prefix('v1')->group(function () {

    // --- AUTH ADMIN ---
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
        
        // Route yang butuh Login
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [LoginController::class, 'me']);
            Route::delete('/logout', [LoginController::class, 'destroy']);
        });
    });

    // --- BOOKING ---
    Route::prefix('booking')->group(function () {
        // Public Access
        Route::get('/available-vehicles', [BookingController::class, 'getAvailableVehicles']);
        Route::post('/', [BookingController::class, 'store'])->middleware('throttle:10,1');
        Route::get('/check/{nrp}', [BookingController::class, 'checkByNrp']);
        Route::get('/approved', [BookingController::class, 'getApprovedBookings']);
        Route::get('/vehicle/{vehicleId}', [BookingController::class, 'getBookingsByVehicle']);
        Route::get('/schedule', [BookingController::class, 'getBookingsByDateRange']);
        
        // Admin Protected Access
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [BookingController::class, 'index']);
            Route::get('/pending', [BookingController::class, 'getPendingBookings']);
            Route::patch('/{id}/approve', [BookingController::class, 'approve']);
            Route::patch('/{id}/reject', [BookingController::class, 'reject']);
        });
    });

    // --- VEHICLES ---
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        
        // Admin Protected Access
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [VehicleController::class, 'store']);
            Route::put('/{id}', [VehicleController::class, 'update']);
            Route::delete('/{id}', [VehicleController::class, 'destroy']);
        });
    });

    // --- UNIT KERJA ---
    Route::get('/unit-kerja', [UnitKerjaController::class, 'index']);
});