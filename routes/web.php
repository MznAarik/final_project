<?php

// routes/web.php
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('login', function () {
    return view('login');
})->name('login');

Route::get('signup', function () {
    return view('signup');
})->name('signup');

Route::post('register', [AuthController::class, 'register'])->name('register.submit');
Route::post('login', [AuthController::class, 'login'])->name('login.submit');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::get('/email/verify', [AuthController::class, 'sendVerificationEmail'])
    ->middleware('auth')
    ->name('verification.send');

Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/upcomings', [AdminController::class, 'upcoming'])->name('admin.upcoming');
});

Route::prefix('events')->middleware('role:admin')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('create', [EventController::class, 'create'])->name('events.create');
    Route::post('store', [EventController::class, 'store'])->name('events.store');
    Route::get('show/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('update/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('destroy/{id}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('user.tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('user.tickets.show');
    Route::post('/tickets', [TicketController::class, 'store'])->name('user.tickets.store');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('user.tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('user.tickets.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Route::get('/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
    // Route::get('/tickets/create', [AdminTicketController::class, 'create'])->name('admin.tickets.create');
    Route::post('/tickets', [AdminTicketController::class, 'store'])->name('admin.tickets.store');
    // Route::put('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('admin.tickets.update');
    // Route::delete('/tickets/{ticket}', [AdminTicketController::class, 'destroy'])->name('admin.tickets.destroy');
});
