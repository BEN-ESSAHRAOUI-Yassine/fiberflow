<?php

use App\Http\Controllers\Api\AuthController;
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

        Route::middleware('admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });
    });
});
