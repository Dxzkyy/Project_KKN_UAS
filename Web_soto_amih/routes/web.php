<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BahanJadiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match ($role) {
            'owner' => redirect()->route('owner.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            'chef' => redirect()->route('chef.dashboard'),
            default => redirect('/'),
        };
    }
    return redirect()->to('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Owner routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');

    // Menu routes
    Route::resource('menu', MenuController::class);

    // Bahan Jadi routes
    Route::resource('bahan_jadi', BahanJadiController::class);
    Route::patch('/bahan_jadi/{bahanJadi}/update-stok', [BahanJadiController::class, 'updateStok'])->name('bahan_jadi.update_stok');
});



//----------------------------------------------------------------------------------------------

// Kasir routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', function () {
        return view('kasir.dashboard');
    })->name('dashboard');
});

//----------------------------------------------------------------------------------------------

// Chef routes
Route::middleware(['auth', 'role:chef'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('/dashboard', function () {
        return view('chef.dashboard');
    })->name('dashboard');
});

//----------------------------------------------------------------------------------------------

require __DIR__ . '/auth.php';
