<?php

use App\Http\Controllers\SkripsiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\MahasiswaAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('mahasiswa.login');
});

Route::get('/login', [MahasiswaAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [MahasiswaAuthController::class, 'login']);
Route::post('/logout', [MahasiswaAuthController::class, 'logout'])->name('logout');




//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/skripsi', [SkripsiController::class, 'index'])->name('skripsi.index');
Route::get('/skripsi/{id}', [SkripsiController::class, 'show'])->name('skripsi.show');
Route::post('/skripsi/search', [SkripsiController::class, 'search'])->name('skripsi.search');
Route::get('/skripsi/bookmark/{id}', [SkripsiController::class, 'bookmark'])->name('skripsi.bookmark');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});