<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PropertyController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── Protected dashboard ──────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');

    // Properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/',               [PropertyController::class, 'index'])->name('index');
        Route::get('/create',         [PropertyController::class, 'create'])->name('create');
        Route::post('/',              [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}',     [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit',[PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}',     [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}',  [PropertyController::class, 'destroy'])->name('destroy');

        // Image management
        Route::delete('/{property}/images/{image}', [PropertyController::class, 'destroyImage'])->name('images.destroy');
        Route::post('/{property}/images/{image}/cover', [PropertyController::class, 'setCoverImage'])->name('images.cover');
    });

    // Users (admin only — enforced in controller)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',            [UserController::class, 'index'])->name('index');
        Route::get('/{user}',      [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',      [UserController::class, 'update'])->name('update');
        Route::delete('/{user}',   [UserController::class, 'destroy'])->name('destroy');
    });
});
