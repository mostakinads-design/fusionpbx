<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\FusionUserController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\CallRecordController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CampaignContactController;
use App\Http\Controllers\DialerController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\DIDController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Domains
Route::resource('domains', DomainController::class);

// Users
Route::resource('users', FusionUserController::class);

// Extensions
Route::resource('extensions', ExtensionController::class);

// DIDs
Route::resource('dids', DIDController::class);
Route::put('dids/{did}/toggle-status', [DIDController::class, 'toggleStatus'])->name('dids.toggleStatus');

// Call Records
Route::resource('call-records', CallRecordController::class)->only(['index', 'show']);

// Campaigns
Route::resource('campaigns', CampaignController::class);
Route::post('campaigns/{campaign}/start', [CampaignController::class, 'start'])->name('campaigns.start');
Route::post('campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
Route::post('campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');

// Campaign Contacts
Route::prefix('campaigns/{campaign}/contacts')->name('campaigns.contacts.')->group(function () {
    Route::get('/', [CampaignContactController::class, 'index'])->name('index');
    Route::get('/create', [CampaignContactController::class, 'create'])->name('create');
    Route::post('/', [CampaignContactController::class, 'store'])->name('store');
    Route::post('/import', [CampaignContactController::class, 'import'])->name('import');
    Route::delete('/{contact}', [CampaignContactController::class, 'destroy'])->name('destroy');
});

// Agents
Route::resource('agents', AgentController::class);
Route::post('agents/{agent}/status', [AgentController::class, 'updateStatus'])->name('agents.updateStatus');

// Dialer
Route::prefix('dialer')->name('dialer.')->group(function () {
    Route::get('/', [DialerController::class, 'index'])->name('index');
    Route::post('/dial', [DialerController::class, 'dial'])->name('dial');
    Route::post('/calls/{call}/status', [DialerController::class, 'updateCallStatus'])->name('updateCallStatus');
    Route::get('/agent/{agent}', [DialerController::class, 'agentDashboard'])->name('agentDashboard');
    Route::post('/next-contact', [DialerController::class, 'getNextContact'])->name('getNextContact');
});

// Billing & Balance Management
Route::prefix('billing')->name('billing.')->group(function () {
    // User Balances
    Route::get('/balances', [BalanceController::class, 'index'])->name('balances.index');
    Route::get('/balances/create', [BalanceController::class, 'create'])->name('balances.create');
    Route::post('/balances', [BalanceController::class, 'store'])->name('balances.store');
    Route::get('/balances/{balance}', [BalanceController::class, 'show'])->name('balances.show');
    Route::put('/balances/{balance}', [BalanceController::class, 'update'])->name('balances.update');
    
    // Transactions
    Route::get('/transactions', [BalanceController::class, 'transactions'])->name('transactions');
    
    // Top-up
    Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
    Route::post('/topup/process', [TopUpController::class, 'process'])->name('topup.process');
    Route::get('/topup/success/{transaction}', [TopUpController::class, 'success'])->name('topup.success');
    
    // Top-up Packages Management
    Route::get('/packages', [TopUpController::class, 'managePackages'])->name('topup.manage');
    Route::post('/packages', [TopUpController::class, 'createPackage'])->name('topup.createPackage');
    Route::put('/packages/{package}', [TopUpController::class, 'updatePackage'])->name('topup.updatePackage');
    Route::delete('/packages/{package}', [TopUpController::class, 'deletePackage'])->name('topup.deletePackage');
});
