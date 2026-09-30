<?php

use App\Http\Controllers\AuthController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    $events = Event::query()
        ->where('is_published', true)
        ->where('starts_at', '>=', now())
        ->with('ticketTypes')
        ->orderBy('starts_at')
        ->get();

    $featuredEvents = $events->values();

    return view('home', compact('events', 'featuredEvents'));
})->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

Route::middleware('guest')->group(function () {
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
