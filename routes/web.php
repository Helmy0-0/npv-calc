<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NpvController;

/*
|--------------------------------------------------------------------------
| Web Routes  (v2 — dengan DB)
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('npv.index'));

Route::prefix('npv')->name('npv.')->group(function () {

    // Form input
    Route::get('/',           [NpvController::class, 'index'])->name('index');

    // Proses hitung + simpan ke DB
    Route::post('/calculate', [NpvController::class, 'calculate'])->name('calculate');

    // Riwayat semua proyek  ← ROUTE HARUS SEBELUM {id} agar tidak konflik
    Route::get('/history',    [NpvController::class, 'history'])->name('history');

    // Detail satu proyek (load dari DB berdasar ID)
    Route::get('/{id}',       [NpvController::class, 'show'])->name('show')
         ->where('id', '[0-9]+');

    // Hapus proyek
    Route::delete('/{id}',    [NpvController::class, 'destroy'])->name('destroy')
         ->where('id', '[0-9]+');
});