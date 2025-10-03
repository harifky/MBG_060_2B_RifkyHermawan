<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\DapurController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route default redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes untuk Role Gudang (Admin)
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [GudangController::class, 'dashboard'])->name('dashboard');

    Route::prefix('bahan-baku')->name('bahan-baku.')->group(function () {
        Route::get('/', [GudangController::class, 'bahanBaku'])->name('index');
        Route::get('/create', [GudangController::class, 'createBahan'])->name('create');
        Route::post('/store', [GudangController::class, 'storeBahan'])->name('store');
        Route::get('/{id}/edit', [GudangController::class, 'editBahan'])->name('edit');
        Route::put('/{id}', [GudangController::class, 'updateBahan'])->name('update');
        Route::delete('/{id}', [GudangController::class, 'deleteBahan'])->name('delete');
    });

    Route::prefix('permintaan')->name('permintaan.')->group(function () {
        Route::get('/status', [GudangController::class, 'statusPermintaan'])->name('status');
        Route::get('/detail/{id}', [GudangController::class, 'detailPermintaan'])->name('detail');
        Route::post('/approve/{id}', [GudangController::class, 'approvePermintaan'])->name('approve');
        Route::post('/reject/{id}', [GudangController::class, 'rejectPermintaan'])->name('reject');
    });
});

// Routes untuk Role Dapur
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DapurController::class, 'dashboard'])->name('dashboard');

    Route::prefix('bahan-baku')->name('bahan-baku.')->group(function () {
        Route::get('/', [DapurController::class, 'bahanBaku'])->name('index');
    });

    Route::prefix('permintaan')->name('permintaan.')->group(function () {
        Route::get('/', [DapurController::class, 'permintaan'])->name('index');
        Route::get('/create', [DapurController::class, 'createPermintaan'])->name('create');
        Route::post('/store', [DapurController::class, 'storePermintaan'])->name('store');
        Route::get('/{id}', [DapurController::class, 'showPermintaan'])->name('show');
        Route::get('/{id}/edit', [DapurController::class, 'editPermintaan'])->name('edit');
        Route::put('/{id}', [DapurController::class, 'updatePermintaan'])->name('update');
        Route::delete('/{id}', [DapurController::class, 'destroyPermintaan'])->name('destroy');
    });
});

// Route untuk handle yang tidak punya akses
Route::fallback(function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role === 'gudang') {
            return redirect()->route('gudang.dashboard');
        } elseif ($user->role === 'dapur') {
            return redirect()->route('dapur.dashboard');
        }
    }
    return redirect()->route('login');
});
