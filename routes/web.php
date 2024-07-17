<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\GoogleSearchConsoleController;
use App\Http\Controllers\SitemapController;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/disconnect-gsc', [ProfileController::class, 'disconnectGsc'])->name('profile.disconnect-gsc');
});


Route::get('/dashboard/sites', [GoogleSearchConsoleController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.sites');
Route::get('/dashboard/sites/{id}', [SitesController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard.sites.show');


Route::get('/auth/google', [GoogleSearchConsoleController::class, 'connect'])->name('google.connect');
Route::get('/auth/google/callback', [GoogleSearchConsoleController::class, 'callback'])->name('google.callback');
Route::get('/auth/google/refresh', [GoogleSearchConsoleController::class, 'refresh'])
    ->middleware('auth')
    ->name('google.refresh');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/sites/{site}/sitemaps', [SitemapController::class, 'index'])->name('sites.sitemaps');
});

Route::get('/dashboard/sites/{siteId}/refresh-sitemaps', [GoogleSearchConsoleController::class, 'refreshSitemaps'])->middleware('auth')->name('refresh.sitemaps');
