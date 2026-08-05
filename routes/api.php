<?php

use App\Http\Controllers\Api\AuditController as ApiAuditController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DatasetController;
use App\Http\Controllers\Api\NetworkController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::delete('/profile', [AuthController::class, 'destroy']);

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->withTrashed()->name('projects.show');

        Route::get('/projects/{project}/datasets', [DatasetController::class, 'index'])->name('datasets.index');
        Route::get('/projects/{project}/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');

        Route::get('/projects/{project}/network', [NetworkController::class, 'index'])->name('projects.network');

        Route::get('/projects/{project}/audits', [ApiAuditController::class, 'index'])->name('audits.index');
        Route::post('/projects/{project}/audits', [ApiAuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [ApiAuditController::class, 'show'])->name('audits.show');
        Route::post('/audits/{audit}/retry', [ApiAuditController::class, 'retry'])->name('audits.retry');

        Route::middleware('admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->withTrashed()->name('projects.update');
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
            Route::put('/projects/{project}/restore', [ProjectController::class, 'restore'])->withTrashed()->name('projects.restore');
            Route::post('/projects/{project}/datasets/import', [DatasetController::class, 'import'])->name('datasets.import');
            Route::delete('/projects/{project}/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
        });
    });
});
