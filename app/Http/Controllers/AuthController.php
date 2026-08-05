<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ConfirmPasswordRequest;
use App\Http\Requests\Auth\DeleteAccountRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->register($request->validated());

        Auth::login($user);

        $request->session()->regenerate();

        return redirect(route('dashboard', absolute: false));
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showConfirmPasswordForm(): View
    {
        return view('auth.confirm-password');
    }

    public function confirmPassword(ConfirmPasswordRequest $request): RedirectResponse
    {
        if (! Hash::check($request->validated()['password'], $request->user()->password)) {
            return back()->withErrors(['password' => __('auth.password')]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function editProfile(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        try {
            $this->authService->updatePassword(
                $request->user(),
                $request->validated()['current_password'],
                $request->validated()['new_password'],
            );
        } catch (ValidationException $e) {
            throw $e->errorBag('updatePassword');
        }

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        try {
            $this->authService->deleteAccount($request->user(), $request->validated()['password']);
        } catch (ValidationException $e) {
            throw $e->errorBag('userDeletion');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
