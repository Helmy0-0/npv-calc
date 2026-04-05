<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NpvController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman NPV
Route::get('/', fn() => redirect()->route('npv.index'));

// ── NPV Calculator Routes ─────────────────────────────────────────────
Route::prefix('npv')->name('npv.')->group(function () {

    // Halaman form input
    Route::get('/', [NpvController::class, 'index'])->name('index');

    // Proses perhitungan
    Route::post('/calculate', [NpvController::class, 'calculate'])->name('calculate');

});
