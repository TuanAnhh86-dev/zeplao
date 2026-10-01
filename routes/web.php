<?php

use App\Http\Controllers\AuthController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

Route::get('/select-ticket/{event:slug}', function (Event $event) {
    abort_unless($event->is_published, 404);

    $event->load('ticketTypes');
    $introductionImages = collect(File::files(public_path('images/events')))
        ->filter(function ($file) use ($event) {
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $extension = strtolower($file->getExtension());

            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)
                || ! preg_match('/^sd-(.+?)(?:-\d+)?$/i', $filename, $matches)) {
                return false;
            }

            return str_contains($event->slug, strtolower($matches[1]));
        })
        ->sortBy(fn ($file) => $file->getFilename())
        ->map(fn ($file) => 'images/events/'.$file->getFilename())
        ->values();

    $mapKeys = [
        'sao-concert-tram-sao-3-26418' => 'sao-concert-tram-3',
        'the-aura-khong-the-thay-the-nov-2026' => 'the-aura',
        'edge-of-calm-tour-tiffany-young-in-ho-chi-minh-26448' => 'tiffany-young',
        'mr-siro-encore-extended-ai-cung-giau-trong-long-tang-bang-ha-noi-26333' => 'ai-cung-giau-trong-long-tang-bang',
        'giua-mot-van-tour-phung-khanh-linh-mo-rong-26459' => 'giua-mot-van-tour',
        'make-it-together-dinh-manh-ninh-will-hoang-ton-nov-2026' => 'make-it-together',
        'tinh-ha-say-hi-dem-3-26590' => 'say-hi',
    ];
    $mapKey = $mapKeys[$event->slug] ?? null;
    $seatMapImage = $mapKey
        ? collect(File::files(public_path('images/map')))
            ->first(fn ($file) => strtolower(pathinfo($file->getFilename(), PATHINFO_FILENAME)) === 'sd-'.$mapKey)
        : null;
    $seatMapImage = $seatMapImage ? 'images/map/'.$seatMapImage->getFilename() : null;

    return view('select-ticket', compact('event', 'introductionImages', 'seatMapImage'));
})->middleware('auth')->name('select-ticket');

Route::get('/ticket-detail/{event:slug}', function (Event $event) {
    abort_unless($event->is_published, 404);
    $event->load('ticketTypes');
    $mapKeys = [
        'sao-concert-tram-sao-3-26418' => 'sao-concert-tram-3',
        'the-aura-khong-the-thay-the-nov-2026' => 'the-aura',
        'edge-of-calm-tour-tiffany-young-in-ho-chi-minh-26448' => 'tiffany-young',
        'mr-siro-encore-extended-ai-cung-giau-trong-long-tang-bang-ha-noi-26333' => 'ai-cung-giau-trong-long-tang-bang',
        'giua-mot-van-tour-phung-khanh-linh-mo-rong-26459' => 'giua-mot-van-tour',
        'make-it-together-dinh-manh-ninh-will-hoang-ton-nov-2026' => 'make-it-together',
        'tinh-ha-say-hi-dem-3-26590' => 'say-hi',
    ];
    $mapKey = $mapKeys[$event->slug] ?? null;
    $mapFile = $mapKey ? collect(File::files(public_path('images/map')))->first(
        fn ($file) => strtolower(pathinfo($file->getFilename(), PATHINFO_FILENAME)) === 'sd-'.$mapKey
    ) : null;
    $seatMapImage = $mapFile ? 'images/map/'.$mapFile->getFilename() : null;

    return view('ticket-detail', compact('event', 'seatMapImage'));
})->middleware('auth')->name('ticket-detail');

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
