<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\VehicleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// admin auth routes
Route::prefix('v1')->group(function () {
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
        Route::get('/me', [LoginController::class, 'me'])->middleware('auth:sanctum');
        Route::delete('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
    });
});

Route::prefix('v1')->group(function () {
    Route::prefix('booking')->group(function () {
            Route::get('/available-vehicles', [BookingController::class, 'getAvailableVehicles']);
            Route::post('/', [BookingController::class, 'store'])
                ->middleware('throttle:10,1');
            Route::get('/check/{nrp}', [BookingController::class, 'checkByNrp']);
            Route::get('/', [BookingController::class, 'index']);
            
            // Routes untuk jadwal kendaraan
            Route::get('/approved', [BookingController::class, 'getApprovedBookings']);
            Route::get('/vehicle/{vehicleId}', [BookingController::class, 'getBookingsByVehicle']);
            Route::get('/schedule', [BookingController::class, 'getBookingsByDateRange']);
    });
});

Route::prefix('v1')->group(function () {
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']); // Public
        Route::get('/{id}', [VehicleController::class, 'show']); // Public
        
        // Admin only routes
        Route::post('/', [VehicleController::class, 'store'])
            ->middleware('auth:sanctum');
        Route::put('/{id}', [VehicleController::class, 'update'])
            ->middleware('auth:sanctum');
        Route::delete('/{id}', [VehicleController::class, 'destroy'])
            ->middleware('auth:sanctum');
    });
});

Route::prefix('admin/booking')->middleware('auth:sanctum')->group(function () {
        Route::put('/{id}/approve', [BookingController::class, 'approve']);
        Route::put('/{id}/reject', [BookingController::class, 'reject']);
    });

    