<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExtensionController;
use App\Http\Controllers\Api\CallLogController;
use App\Http\Controllers\Api\SettingController;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Users
    Route::apiResource('users', UserController::class);
    
    // Extensions
    Route::apiResource('extensions', ExtensionController::class);
    
    // Call Logs
    Route::apiResource('call-logs', CallLogController::class);
    Route::get('/call-logs/filter/status/{status}', [CallLogController::class, 'filterByStatus']);
    Route::get('/call-logs/filter/caller/{caller}', [CallLogController::class, 'filterByCaller']);
    
    // Settings
    Route::apiResource('settings', SettingController::class);
    Route::get('/settings/key/{key}', [SettingController::class, 'getByKey']);
});
