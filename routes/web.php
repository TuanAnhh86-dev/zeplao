<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\QrScanController;
use App\Models\Event;
use App\Services\OrderReservationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) return redirect()->route('login');
    return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/qr-scan', [QrScanController::class, 'index'])->name('qr-scan');
    Route::get('/qr-scan/ticket', [QrScanController::class, 'lookup'])->name('qr-scan.lookup');
    Route::post('/qr-scan/{qrInfo}/check-in', [QrScanController::class, 'checkIn'])->name('qr-scan.check-in');
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/events', [AdminController::class, 'events'])->name('events');
    Route::get('/events/create', [AdminController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [AdminController::class, 'destroy'])->name('events.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/create', [AdminController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [AdminController::class, 'storeOrder'])->name('orders.store');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
});

Route::get('/dashboard', function () {
    if (Auth::user()->isAdmin()) return redirect()->route('admin.dashboard');
    $events = Event::query()
        ->with('ticketTypes')
        ->orderBy('starts_at')
        ->get();

    $featuredEvents = $events->values();

    return view('home', compact('events', 'featuredEvents'));
})->middleware('auth')->name('dashboard');

Route::get('/select-ticket/{event:slug}', function (Event $event) {
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

Route::get('/ticket-detail/{event:slug}', function (Event $event, OrderReservationService $reservations) {
    $event->load('ticketTypes');
    $reservedQuantities = $reservations->reservedQuantities($event->ticketTypes->pluck('id')->all());
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

    return view('ticket-detail', compact('event', 'seatMapImage', 'reservedQuantities'));
})->middleware('auth')->name('ticket-detail');
Route::post('/events/{event:slug}/orders', [OrderController::class, 'store'])->middleware('auth')->name('orders.store');
Route::get('/payment/{order}', [OrderController::class, 'payment'])->middleware('auth')->name('payment.show');
Route::get('/my-ticket', [OrderController::class, 'myTickets'])->middleware('auth')->name('my-tickets');
Route::get('/transactions', [OrderController::class, 'transactions'])->middleware('auth')->name('transactions.index');
Route::post('/payment/{order}/vnpay', [VnpayController::class, 'start'])->middleware('auth')->name('payment.vnpay.start');
Route::get('/payments/vnpay/return', [VnpayController::class, 'returned'])->name('payment.vnpay.return');
Route::get('/payments/vnpay/ipn', [VnpayController::class, 'ipn'])->name('payment.vnpay.ipn');
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('auth')->name('orders.cancel');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.authenticate');

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
