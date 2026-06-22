<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\Api\OrderApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Untuk Aplikasi Android Pelanggan (Guest)
|--------------------------------------------------------------------------
| File ini ditambahkan ke: routes/api.php
| Tidak memerlukan autentikasi karena pelanggan adalah guest
*/

// Menu
Route::prefix('v1')->group(function () {

    // ── Menu ──────────────────────────────────────────────────────────────
    Route::get('/menus', [MenuApiController::class, 'index']);          // Semua menu aktif
    Route::get('/menus/{id}', [MenuApiController::class, 'show']);      // Detail menu

    // ── Order (Pelanggan Guest) ───────────────────────────────────────────
    Route::post('/orders', [OrderApiController::class, 'store']);                    // Buat pesanan baru
    Route::get('/orders/{kode_order}', [OrderApiController::class, 'show']);        // Cek status pesanan
    Route::get('/orders/{kode_order}/status', [OrderApiController::class, 'status']); // Polling status

    Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('app/public/menus/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('filename', '.*');

});