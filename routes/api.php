<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\BookingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// admin auth routes
Route::prefix('v1')->group(function () {
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
        Route::delete('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
    });
});

Route::prefix('booking')->group(function () {
        Route::post('/', [BookingController::class, 'store'])
            ->middleware('throttle:10,1'); // Max 10 submissions per minute
        Route::get('/check/{nrp}', [BookingController::class, 'checkByNrp']);
        Route::get('/', [BookingController::class, 'index']); // For testing
    });
