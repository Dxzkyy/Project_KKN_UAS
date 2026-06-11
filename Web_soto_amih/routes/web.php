<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BahanJadiController;
use App\Http\Controllers\Kasir\PesananController;
use App\Http\Controllers\Kasir\LaporanController;
use App\Http\Controllers\ChefController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match ($role) {
            'owner' => redirect()->route('owner.laporan.index'),
            'kasir' => redirect()->route('kasir.pesanan.index'),
            'chef' => redirect()->route('chef.pesanan.index'),
            default => redirect('/'),
        };
    }
    return redirect()->to('/login');
});

Route::get('/dashboard', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match ($role) {
            'owner' => redirect()->route('owner.laporan.index'),
            'kasir' => redirect()->route('kasir.pesanan.index'),
            'chef' => redirect()->route('chef.pesanan.index'),
            default => redirect('/login'),
        };
    }
    return redirect()->to('/login');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Owner routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('owner.laporan.index');
    })->name('dashboard');

    Route::resource('menu', MenuController::class);
    Route::resource('bahan_jadi', BahanJadiController::class);
    Route::patch('/bahan_jadi/{bahanJadi}/update-stok', [BahanJadiController::class, 'updateStok'])->name('bahan_jadi.update_stok');

    // Laporan Penjualan
    Route::get('/laporan', [App\Http\Controllers\Owner\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/set-modal', [App\Http\Controllers\Owner\LaporanController::class, 'setModal'])->name('laporan.set_modal');
    Route::get('/laporan/ekspor-pdf', [App\Http\Controllers\Owner\LaporanController::class, 'eksporPdf'])->name('laporan.ekspor_pdf');

    // Arsip Laporan
    Route::get('/arsip', [App\Http\Controllers\Owner\ArsipController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/{id}', [App\Http\Controllers\Owner\ArsipController::class, 'show'])->name('arsip.show');
    Route::get('/arsip/{id}/pdf', [App\Http\Controllers\Owner\ArsipController::class, 'cetakPdf'])->name('arsip.pdf');

    // Penjualan
    Route::get('/penjualan', [App\Http\Controllers\Owner\PenjualanController::class, 'index'])->name('penjualan.index');
});

//----------------------------------------------------------------------------------------------

// Kasir routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('kasir.pesanan.index');
    })->name('dashboard');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan/store', [PesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/riwayat', [PesananController::class, 'riwayat'])->name('pesanan.riwayat');
    Route::get('/pesanan/struk/{id}', [PesananController::class, 'cetakStruk'])->name('pesanan.struk');
    Route::patch('/pesanan/{id}/cancel', [PesananController::class, 'cancel'])->name('pesanan.cancel');

    // Laporan harian
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/kirim', [LaporanController::class, 'kirim'])->name('laporan.kirim');
});

//----------------------------------------------------------------------------------------------

// Chef routes
Route::middleware(['auth', 'role:chef'])->prefix('chef')->name('chef.')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('chef.pesanan.index');
    })->name('dashboard');

    Route::get('/pesanan', [ChefController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan/update-status/{id}', [ChefController::class, 'updateStatus'])->name('pesanan.update_status');
});

//----------------------------------------------------------------------------------------------

require __DIR__ . '/auth.php';