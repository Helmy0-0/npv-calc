<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NpvController;

// Web Routes  (v2)

Route::get('/', fn() => redirect()->route('npv.index'));

Route::prefix('npv')->name('npv.')->group(function () {


    Route::get('/',           [NpvController::class, 'index'])->name('index');
    Route::post('/calculate', [NpvController::class, 'calculate'])->name('calculate');
    Route::get('/history',    [NpvController::class, 'history'])->name('history');
    Route::get('/{id}',       [NpvController::class, 'show'])->name('show')
         ->where('id', '[0-9]+');
    Route::delete('/{id}',    [NpvController::class, 'destroy'])->name('destroy')
         ->where('id', '[0-9]+');
});