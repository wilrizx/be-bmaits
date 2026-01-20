<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\VehicleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ✅ Admin Auth Routes
Route::prefix('v1')->group(function () {
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
        Route::get('/me', [LoginController::class, 'me'])->middleware('auth:sanctum');
        Route::delete('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
    });
});

// ✅ Booking Routes
Route::prefix('v1')->group(function () {
    Route::prefix('booking')->group(function () {
        // Public routes
        Route::get('/available-vehicles', [BookingController::class, 'getAvailableVehicles']);
        Route::post('/', [BookingController::class, 'store'])->middleware('throttle:10,1');
        Route::get('/check/{nrp}', [BookingController::class, 'checkByNrp']);
        Route::get('/approved', [BookingController::class, 'getApprovedBookings']);
        Route::get('/vehicle/{vehicleId}', [BookingController::class, 'getBookingsByVehicle']);
        Route::get('/schedule', [BookingController::class, 'getBookingsByDateRange']);
        
        // ✅ Admin only routes (protected)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [BookingController::class, 'index']); // Get all bookings
            Route::get('/pending', [BookingController::class, 'getPendingBookings']); // Get pending only
            Route::patch('/{id}/approve', [BookingController::class, 'approve']); // Approve
            Route::patch('/{id}/reject', [BookingController::class, 'reject']); // Reject
        });
    });
});

// ✅ Vehicle Routes
Route::prefix('v1')->group(function () {
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']); // Public
        Route::get('/{id}', [VehicleController::class, 'show']); // Public
        
        // Admin only routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [VehicleController::class, 'store']);
            Route::put('/{id}', [VehicleController::class, 'update']);
            Route::delete('/{id}', [VehicleController::class, 'destroy']);
        });
    });
});
