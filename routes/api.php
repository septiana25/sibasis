<?php

use App\Http\Controllers\AiChatSsoController;
use Illuminate\Support\Facades\Route;

Route::get('/sso/verify', [AiChatSsoController::class, 'verify'])->name('api.sso.verify');
