<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::view('/dashboard', 'home')->middleware('auth')->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register/send-code', [AuthController::class, 'sendRegistrationCode'])
        ->middleware('throttle:5,1')
        ->name('register.send-code');
    Route::post('/register/verify', [AuthController::class, 'verifyRegistrationCode'])
        ->middleware('throttle:10,1')
        ->name('register.verify');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password/send-code', [AuthController::class, 'sendPasswordResetCode'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:10,1')
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
