<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\PanenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('kebun', KebunController::class)->except('show');
    Route::resource('panen', PanenController::class)->except('show');
});

require __DIR__.'/auth.php';
