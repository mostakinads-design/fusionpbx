<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CdrController;
use App\Http\Controllers\AiAgentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.liveData');

// CDR Routes
Route::prefix('cdr')->name('cdr.')->group(function () {
    Route::get('/', [CdrController::class, 'index'])->name('index');
    Route::get('/data', [CdrController::class, 'getData'])->name('data');
    Route::get('/statistics', [CdrController::class, 'statistics'])->name('statistics');
    Route::get('/export', [CdrController::class, 'export'])->name('export');
    Route::get('/{id}', [CdrController::class, 'show'])->name('show');
    Route::get('/{id}/analyze', [CdrController::class, 'analyze'])->name('analyze');
});

// AI Agent Routes
Route::prefix('ai')->name('ai.')->group(function () {
    Route::get('/', [AiAgentController::class, 'index'])->name('index');
    Route::post('/chat', [AiAgentController::class, 'chat'])->name('chat');
    Route::post('/routing-decision', [AiAgentController::class, 'routingDecision'])->name('routing');
    Route::post('/update-mode', [AiAgentController::class, 'updateMode'])->name('updateMode');
});

