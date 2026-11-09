<?php

use App\Http\Controllers\KebunController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'jumlahKebun' => request()->user()->kebuns()->count(),
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('kebun', KebunController::class)->except('show');
});

require __DIR__.'/auth.php';
