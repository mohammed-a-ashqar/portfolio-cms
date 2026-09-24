<?php

declare(strict_types=1);

use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProjectController;
use App\Http\Controllers\Front\QuoteController;
use App\Http\Controllers\Front\ReelController;
use App\Http\Controllers\Front\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/work', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/work/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/reels', [ReelController::class, 'index'])->name('reels.index');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

/*
| Write endpoints are rate limited: the Action layer also throttles per email,
| but stopping a flood at the router keeps it off the database entirely.
*/
Route::middleware('throttle:6,1')->group(function (): void {
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/quote', [QuoteController::class, 'store'])->name('quotes.store');
});

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::get('/quote', [QuoteController::class, 'create'])->name('quotes.create');
