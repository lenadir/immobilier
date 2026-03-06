<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Plateforme Immobilière
|--------------------------------------------------------------------------
|
| Toutes les routes sont préfixées par /api (config/app.php)
| Authentification : Laravel Sanctum (token Bearer)
|
| Rôles :
|   admin  → accès total
|   agent  → gestion de ses propres biens
|   guest  → lecture seule (biens publiés)
|
*/

// ─── Routes publiques (sans authentification) ─────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login',    [AuthController::class, 'login'])->name('auth.login');
});

// ─── Routes publiques — lecture seule (biens publiés) ────────────────────────
// NOTE : les routes statiques (/trashed) sont enregistrées AVANT le joker /{id}
// pour éviter que Laravel ne les intercepte comme un identifiant numérique.

Route::prefix('properties')->name('api.properties.')->group(function () {
    Route::get('/',        [PropertyController::class, 'index'])->name('index');
    Route::get('/trashed', [PropertyController::class, 'trashed'])
        ->name('trashed')
        ->middleware(['auth:sanctum', 'role:admin']);
    Route::get('/{id}',    [PropertyController::class, 'show'])->name('show');
});

// ─── Routes protégées (Sanctum) ───────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('auth/me',      [AuthController::class, 'me'])->name('auth.me');

    // ── Biens immobiliers ─────────────────────────────────────────────────────

    Route::prefix('properties')->name('api.properties.')->group(function () {

        // Corbeille & restauration (admin uniquement)
        Route::middleware('role:admin')->group(function () {
            Route::patch('/{property}/restore', [PropertyController::class, 'restore'])->name('restore');
            Route::delete('/{property}/force',  [PropertyController::class, 'forceDelete'])->name('force-delete');
        });

        // Création / modification / suppression (agent + admin)
        Route::middleware('role:admin,agent')->group(function () {
            Route::post('/',                [PropertyController::class, 'store'])->name('store');
            Route::put('/{property}',       [PropertyController::class, 'update'])->name('update');
            Route::patch('/{property}',     [PropertyController::class, 'update'])->name('patch');
            Route::delete('/{property}',    [PropertyController::class, 'destroy'])->name('destroy');

            // Images d'un bien
            Route::post('/{property}/images', [ImageController::class, 'store'])->name('images.store');
        });
    });

    // ── Images (suppression & couverture) ─────────────────────────────────────

    Route::prefix('images')->name('api.images.')->middleware('role:admin,agent')->group(function () {
        Route::delete('/{image}',        [ImageController::class, 'destroy'])->name('destroy');
        Route::patch('/{image}/cover',   [ImageController::class, 'setCover'])->name('cover');
    });

    // ── Gestion des utilisateurs (admin) ──────────────────────────────────────

    Route::prefix('users')->name('api.users.')->middleware('role:admin')->group(function () {
        Route::get('/',        [UserController::class, 'index'])->name('index');
        Route::get('/{id}',    [UserController::class, 'show'])->name('show');
        Route::put('/{id}',    [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});
