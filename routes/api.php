<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PlaygroundController;
use Illuminate\Support\Facades\Route;

// Public blog
Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('api.blog.index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('api.blog.show');
});

// Admin (auth:sanctum)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me'])->name('api.admin.me');

    Route::get('/blog', [AdminBlogController::class, 'index'])->name('api.admin.blog.index');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('api.admin.blog.store');
    Route::get('/blog/{id}', [AdminBlogController::class, 'show'])->name('api.admin.blog.show');
    Route::put('/blog/{id}', [AdminBlogController::class, 'update'])->name('api.admin.blog.update');
    Route::delete('/blog/{id}', [AdminBlogController::class, 'destroy'])->name('api.admin.blog.destroy');
    Route::patch('/blog/{id}/publish', [AdminBlogController::class, 'togglePublish'])->name('api.admin.blog.togglePublish');

    Route::post('/ai/generate', [AiController::class, 'generate'])->name('api.admin.ai.generate');
});

// Guest playground (session + rate limited)
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    'guest-rate-limit',
])->prefix('playground')->group(function () {
    Route::post('/generate', [PlaygroundController::class, 'generate'])->name('api.playground.generate');
    Route::post('/key', [PlaygroundController::class, 'setKey'])->name('api.playground.setKey');
});

// Playground status — outside rate limit middleware (needs session for key check)
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
])->prefix('playground')->group(function () {
    Route::get('/status', [PlaygroundController::class, 'status'])->name('api.playground.status');
});
