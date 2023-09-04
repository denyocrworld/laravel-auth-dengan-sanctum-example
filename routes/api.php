<?php

use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\CustomerApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

//api/login
//api/logout
//api/refresh
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);

Route::prefix('customers')->middleware('auth:sanctum')->group(function () {
    Route::get('', [CustomerApiController::class, 'index']);
    Route::post('', [CustomerApiController::class, 'store']);
    Route::get('{id}', [CustomerApiController::class, 'show']);
    Route::put('{id}', [CustomerApiController::class, 'update']);
    Route::delete('{id}', [CustomerApiController::class, 'destroy']);
});

Route::prefix('products')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ProductApiController::class, 'index']);
    Route::post('', [ProductApiController::class, 'store']);
    Route::get('{id}', [ProductApiController::class, 'show']);
    Route::put('{id}', [ProductApiController::class, 'update']);
    Route::delete('{id}', [ProductApiController::class, 'destroy']);
});
