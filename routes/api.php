<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::delete('/profile', [AuthController::class, 'destroy']);

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->withTrashed()->name('projects.show');

        Route::middleware('admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->withTrashed()->name('projects.update');
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
            Route::put('/projects/{project}/restore', [ProjectController::class, 'restore'])->withTrashed()->name('projects.restore');
        });
    });
});
