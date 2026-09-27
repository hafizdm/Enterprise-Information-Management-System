<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(
            Str::lower($request->input('username')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (!Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {

            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->route('dashboard');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = $request->user();

        $user->update([
            'password' => $validated['password'],
            'must_change_password' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * Logout current user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}