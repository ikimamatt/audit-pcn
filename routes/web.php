<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;

require __DIR__ . '/auth.php';
require __DIR__ . '/master-data.php';
require __DIR__ . '/audit.php';

// Route untuk check session (tidak perlu auth)
Route::get('/check-session', function () {
    if (auth()->check()) {
        return response()->json(['status' => 'active']);
    }
    return response()->json(['status' => 'expired'], 401);
})->name('check-session');

Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
    Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('/home', [RoutingController::class, 'index'])->name('home');

    // Specific routes for master data and audit - MUST BE BEFORE CATCH-ALL ROUTES
    Route::get('tables/master_kode_aoi', [RoutingController::class, 'masterKodeAoi']);
    Route::get('tables/master_kode_risk', [RoutingController::class, 'masterKodeRisk']);
    Route::get('tables/master_auditee', [RoutingController::class, 'masterAuditee']);
    Route::get('tables/master_user', [RoutingController::class, 'masterUser']);
    Route::get('tables/master_akses_user', [RoutingController::class, 'masterAksesUser']);
    Route::get('forms/tabel_perencanaan_audit', [RoutingController::class, 'tabelPerencanaanAudit']);
    Route::get('forms/perencanaan_audit', [RoutingController::class, 'perencanaanAuditForm']);

    Route::post('profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
});
