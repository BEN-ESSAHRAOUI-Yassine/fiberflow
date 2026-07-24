<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('confirm-password', [AuthController::class, 'showConfirmPasswordForm'])->name('password.confirm');
    Route::post('confirm-password', [AuthController::class, 'confirmPassword']);

    Route::put('password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('profile', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::patch('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::delete('profile', [AuthController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::name('admin.')->group(function () {
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->withTrashed()->name('projects.show');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->withTrashed()->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->withTrashed()->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->withTrashed()->name('projects.destroy');
        Route::put('projects/{project}/restore', [ProjectController::class, 'restore'])->withTrashed()->name('projects.restore');
    });

    Route::middleware('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });
});
