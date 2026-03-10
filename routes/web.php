<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth');

Route::get('/feed.xml', FeedController::class)->name('feed');

// SPA catch-all — must be last
Route::get('/{any}', fn () => view('app'))->where('any', '.*');
