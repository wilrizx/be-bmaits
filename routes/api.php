<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth;
use App\Http\Controllers\API\Auth\LoginController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// admin auth routes
Route::prefix('v1')->group(function () {
    Route::prefix('adminbma')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::delete('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
        Route::get('/me', [LoginController::class, 'me'])->middleware('auth:sanctum');
    });
});

