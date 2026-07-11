<?php

use App\Http\Controllers\SkripsiController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\Reports\VisitorReportController;
use App\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/nojs', 'errors.nojs')->name('nojs');
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('logout.others:member')->group(function () {
    Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('member.login');
});

//Auth::routes();

/* Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); */

Route::middleware('auth:member')->group(function () {
    Route::get('/skripsi', [SkripsiController::class, 'index'])->name('skripsi.index');
    Route::get('/skripsi/{nim}', [SkripsiController::class, 'show'])->name('skripsi.show');
    Route::get('/skripsi/link/{nim}', [SkripsiController::class, 'getByLink'])->name('skripsi.link');
    Route::post('/skripsi/search', [SkripsiController::class, 'search'])->name('skripsi.search');
    Route::get('/skripsi/bookmark/{nim}', [SkripsiController::class, 'toggleBookmark'])->name('skripsi.bookmark');
    Route::get('/skripsi/stream/{nim}', [SkripsiController::class, 'streamPdf'])->name('skripsi.stream');

    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');

    Route::post('violation-log', [ViolationController::class, 'log'])->name('violation-log');
    Route::get('violation-logout', [ViolationController::class, 'logout'])->name('violation-logout');

    Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');
});

Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('logout.others:web')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login');
    });

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Documents module
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/export', [DocumentController::class, 'export'])->name('documents.export');
        Route::get('/documents/export-format', [DocumentController::class, 'exportFormat'])->name('documents.export-format');
        Route::post('/documents/lookup', [DocumentController::class, 'lookupByNim'])->name('documents.lookup');
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    
        // Laporan module
        Route::get('/reports/visitor', [VisitorReportController::class, 'index'])->name('reports.visitor');
        Route::get('/reports/visitor/export', [VisitorReportController::class, 'export'])->name('reports.visitor.export');
    });
});

/* 
 POST       logout ............................................................. logout › Auth\LoginController@logout
  GET|HEAD   member/dashboard ..................................... member.dashboard › MemberDashboardController@index
  GET|HEAD   password/confirm ...................... password.confirm › Auth\ConfirmPasswordController@showConfirmForm
  POST       password/confirm ................................................. Auth\ConfirmPasswordController@confirm
  POST       password/email ........................ password.email › Auth\ForgotPasswordController@sendResetLinkEmail
  GET|HEAD   password/reset ..................... password.request › Auth\ForgotPasswordController@showLinkRequestForm
  POST       password/reset ..................................... password.update › Auth\ResetPasswordController@reset
  GET|HEAD   password/reset/{token} ...................... password.reset › Auth\ResetPasswordController@showResetForm
*/