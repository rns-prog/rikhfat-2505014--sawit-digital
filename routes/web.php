<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\PanenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('biaya/preview-upah', [BiayaController::class, 'previewUpah'])->name('biaya.preview');
    Route::resource('kebun', KebunController::class)->except('show');
    Route::resource('panen', PanenController::class)->except('show');
    Route::resource('biaya', BiayaController::class)->except('show');
});

require __DIR__.'/auth.php';
