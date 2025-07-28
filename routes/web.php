<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function App\Helpers\svg_to_png;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('login', [HomeController::class, 'index'])->name('login');
Route::get('signup', [HomeController::class, 'index'])->name('signup');
Route::get('buy_tickets', [HomeController::class, 'showAllEvents'])->name('buy_tickets');
Route::get('upcoming', [HomeController::class, 'showUpcomingEvents'])->name('upcoming');
Route::get('popular', [HomeController::class, 'showPopularEvents'])->name('popular');

// Route::get('/my-tickets', function () {
//     return view('my_tickets');
// })->name('my_tickets');

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

Route::middleware(['checkRole:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('scan-qr', [AdminController::class, 'showScanQrPage'])->name('admin.scanQr');
    Route::get('verify-ticket', [AdminController::class, 'verifyTicket'])->name('admin.verify-ticket');
});

Route::prefix('events')->middleware('checkRole:admin')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('create', [EventController::class, 'create'])->name('events.create');
    Route::post('store', [EventController::class, 'store'])->name('events.store');
    Route::get('show/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('update/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('destroy/{id}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::middleware(['checkRole:user'])->prefix('user/')->group(function () {
    Route::get('tickets', [TicketController::class, 'index'])->name('user.tickets.index');
    Route::get('tickets/{batch_code}', [TicketController::class, 'show'])->name('user.tickets.show');
    Route::post('tickets', [TicketController::class, 'store'])->name('user.tickets.store');
    Route::put('tickets/{batch_code}', [TicketController::class, 'update'])->name('user.tickets.update');
    Route::delete('tickets/{batch_code}', [TicketController::class, 'destroy'])->name('user.tickets.destroy');

});

Route::middleware(['checkRole:user'])->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'addCart'])->name('cart.add');
    Route::put('/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{eventId}/{index}', [CartController::class, 'removeSingle'])->name('cart.removeSingle');

    Route::get('/checkout', [PaymentController::class, 'showPaymentForm'])->name('cart.checkout');
    Route::post('/checkout/process', [PaymentController::class, 'processPayment'])->name('cart.checkout.process');
    Route::get('/checkout/success', [PaymentController::class, 'successCallback'])->name('cart.checkout.success');
    Route::get('/checkout/failure', [PaymentController::class, 'failureCallback'])->name('cart.checkout.failure');
    Route::post('/checkout/paypal/notify', [PaymentController::class, 'paypalNotify'])->name('cart.checkout.paypal.notify');
});

Route::middleware(['checkRole:admin'])->prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::patch('/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
});

// Route::middleware([ 'checkRole:admin'])->prefix('admin')->group(function () {
//     Route::get('/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
//     // Route::get('/tickets/create', [AdminTicketController::class, 'create'])->name('admin.tickets.create');
//     Route::post('/tickets', [AdminTicketController::class, 'store'])->name('admin.tickets.store');

Route::get('/test-alert', function () {
    return redirect()->route('home')->with([
        'status' => 2,
        'message' => 'Welcome to the Admin Dashboard'
    ]);
});
