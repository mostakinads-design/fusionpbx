<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\CallCenterQueueController;
use App\Http\Controllers\CallCenterAgentController;
use App\Http\Controllers\CallCenterTierController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignContactController;
use App\Http\Controllers\CdrController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Users
Route::resource('users', UserController::class);

// Extensions
Route::resource('extensions', ExtensionController::class);

// Domains
Route::resource('domains', DomainController::class);

// Call Center Queues
Route::resource('call-center-queues', CallCenterQueueController::class);

// Call Center Agents
Route::resource('call-center-agents', CallCenterAgentController::class);
Route::post('call-center-agents/{agent}/status', [CallCenterAgentController::class, 'updateStatus'])
    ->name('call-center-agents.update-status');

// Call Center Tiers
Route::resource('call-center-tiers', CallCenterTierController::class)
    ->only(['index', 'store', 'update', 'destroy']);

// Campaigns
Route::resource('campaigns', CampaignController::class);
Route::post('campaigns/{campaign}/start', [CampaignController::class, 'start'])
    ->name('campaigns.start');
Route::post('campaigns/{campaign}/pause', [CampaignController::class, 'pause'])
    ->name('campaigns.pause');
Route::post('campaigns/{campaign}/stop', [CampaignController::class, 'stop'])
    ->name('campaigns.stop');

// Campaign Contacts
Route::get('campaigns/{campaign}/contacts', [CampaignContactController::class, 'index'])
    ->name('campaign-contacts.index');
Route::get('campaigns/{campaign}/contacts/import', [CampaignContactController::class, 'import'])
    ->name('campaign-contacts.import');
Route::post('campaigns/{campaign}/contacts/import', [CampaignContactController::class, 'store'])
    ->name('campaign-contacts.store');
Route::delete('campaigns/{campaign}/contacts/{contact}', [CampaignContactController::class, 'destroy'])
    ->name('campaign-contacts.destroy');

// CDR
Route::get('cdr', [CdrController::class, 'index'])->name('cdr.index');
Route::get('cdr/{cdr}', [CdrController::class, 'show'])->name('cdr.show');
Route::get('cdr-export', [CdrController::class, 'export'])->name('cdr.export');
