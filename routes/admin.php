<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\QuoteRequestController;
use App\Http\Controllers\Admin\ReelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
| Mounted at config('portfolio.admin_prefix') with the "admin." name prefix.
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('projects', ProjectController::class);

    Route::controller(ReelController::class)->prefix('reels')->as('reels.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{reel}/edit', 'edit')->name('edit');
        Route::put('/{reel}', 'update')->name('update');
        Route::delete('/{reel}', 'destroy')->name('destroy');
        Route::post('/reorder', 'reorder')->name('reorder');
    });

    Route::controller(QuoteRequestController::class)->prefix('quotes')->as('quotes.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{quote}', 'show')->name('show');
        Route::patch('/{quote}/status', 'transition')->name('transition');
        Route::delete('/{quote}', 'destroy')->name('destroy');
    });

    Route::controller(ContactMessageController::class)->prefix('messages')->as('messages.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{message}', 'show')->name('show');
        Route::delete('/{message}', 'destroy')->name('destroy');
    });
});
