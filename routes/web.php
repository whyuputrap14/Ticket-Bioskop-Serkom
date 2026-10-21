<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\RegistrationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [OrderController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::resource('/registrations', RegistrationController::class)
        ->parameters(['registrations' => 'registration'])
        ->except(['show'])
        ->names('registrations');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::resource('/orders', OrderController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->names('orders');
    Route::resource('/films', FilmController::class)->except(['show'])->names('films');
    Route::resource('/showtimes', ShowtimeController::class)->except(['show'])->names('showtimes');
});

Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/user/dashboard', [OrderController::class, 'customerDashboard'])->name('customer.dashboard');
    Route::get('/jadwal-tayang', [OrderController::class, 'index'])->name('customer.index');
    Route::get('/pemesanan-tiket', [OrderController::class, 'index'])->name('customer.booking');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('customer.checkout');
});

Route::get('/', [OrderController::class, 'index'])->name('home');

require __DIR__.'/auth.php';