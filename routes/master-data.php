<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\MasterKodeAoiController;
use App\Http\Controllers\MasterData\MasterKodeRiskController;
use App\Http\Controllers\MasterData\MasterAuditeeController;
use App\Http\Controllers\MasterData\MasterUserController;
use App\Http\Controllers\MasterData\MasterAksesUserController;

// Master Data Routes
Route::prefix('master')->name('master.')->group(function () {
    // Kode AOI
    Route::resource('kode-aoi', MasterKodeAoiController::class)
        ->names('kode-aoi')
        ->parameters(['kode-aoi' => 'masterKodeAoi']);
    
    // Kode Risk
    Route::resource('kode-risk', MasterKodeRiskController::class)
        ->names('kode-risk')
        ->parameters(['kode-risk' => 'masterKodeRisk']);
    
    // Auditee
    Route::resource('auditee', MasterAuditeeController::class)
        ->names('auditee')
        ->parameters(['auditee' => 'masterAuditee']);
    
    // User
    Route::resource('user', MasterUserController::class)
        ->names('user')
        ->parameters(['user' => 'masterUser']);
    
    // Akses User
    Route::get('akses-user', [MasterAksesUserController::class, 'index'])->name('akses-user.index');
}); 