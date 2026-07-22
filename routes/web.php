<?php

use App\Http\Controllers\AuthController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
