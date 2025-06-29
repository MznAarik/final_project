<?php

// routes/web.php
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('login', function () {
    return view('home');
})->name('login');

Route::get('signup', function () {
    return view('home');
})->name('signup');

// New added
Route::get('/buy_tickets', function () {
    return view('buy_tickets');
});

Route::get('/upcoming', function () {
    return view('upcoming');
});

Route::get('/popular', function () {
    return view('popular');
});

Route::get('/cart', function () {
    return view('cart');
});

Route::get('/my-tickets', function () {
    return view('my_tickets');
});

Route::middleware('auth')->get('/profile', function () {
    return view('profile');
});

Route::post('register', [AuthController::class, 'register'])->name('register.submit');
Route::middleware('throttle: 5, 1')->post('login', [AuthController::class, 'login'])->name('login.submit');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::get('/email/verify', [AuthController::class, 'sendVerificationEmail'])
    ->middleware('auth')
    ->name('verification.send');


Route::prefix('events')->middleware('role:admin')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('create', [EventController::class, 'create'])->name('events.create');
    Route::post('store', [EventController::class, 'store'])->name('events.store');
    Route::get('show/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('update/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('destroy/{id}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::middleware(['auth', 'role:user'])->prefix('user/')->group(function () {
    Route::get('tickets', [TicketController::class, 'index'])->name('user.tickets.index');
    Route::get('tickets/{batch_code}', [TicketController::class, 'show'])->name('user.tickets.show');
    Route::post('tickets', [TicketController::class, 'store'])->name('user.tickets.store');
    Route::put('tickets/{batch_code}', [TicketController::class, 'update'])->name('user.tickets.update');
    Route::delete('tickets/{batch_code}', [TicketController::class, 'destroy'])->name('user.tickets.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
    // Route::get('/tickets/create', [AdminTicketController::class, 'create'])->name('admin.tickets.create');
    Route::post('/tickets', [AdminTicketController::class, 'store'])->name('admin.tickets.store');
    // Route::put('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('admin.tickets.update');
    // Route::delete('/tickets/{ticket}', [AdminTicketController::class, 'destroy'])->name('admin.tickets.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin/')->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('scan-qr', [AdminController::class, 'showScanQrPage'])->name('admin.scan-qr');
    Route::get('verify-ticket', [AdminController::class, 'verifyTicket'])->name('admin.verify-ticket');
});

