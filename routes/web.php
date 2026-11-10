<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BlogPageController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\SeriesPageController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth');

Route::get('/feed.xml', FeedController::class)->name('feed');

// Series pages first, or /blog/{slug} would take /blog/series as a post slug
Route::get('/blog/series', [SeriesPageController::class, 'index'])->name('blog.series.index');
Route::get('/blog/series/{slug}', [SeriesPageController::class, 'show'])->name('blog.series.page');

// Per-post meta and real 404s for shared links; must precede the catch-all
Route::get('/blog/{slug}', BlogPageController::class)->name('blog.page');

// SPA catch-all — must be last
Route::get('/{any}', fn () => view('app'))->where('any', '.*');
