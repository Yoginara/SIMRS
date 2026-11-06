<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset kata sandi telah berhasil dikirim ke alamat email Anda! Silakan periksa Kotak Masuk atau folder Spam.');
        }

        $errorMessage = match ($status) {
            Password::RESET_THROTTLED => 'Mohon tunggu 1 menit sebelum meminta tautan baru (fitur keamanan anti-spam).',
            Password::INVALID_USER => 'Alamat email ini tidak terdaftar sebagai staf rumah sakit resmi.',
            default => __($status),
        };

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }

    /**
     * Display the password reset view.
     */
    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Kata sandi akun Anda telah berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
        }

        $errorMessage = match ($status) {
            Password::INVALID_TOKEN => 'Tautan reset sandi ini sudah kadaluarsa atau tidak valid. Silakan ajukan tautan baru.',
            Password::INVALID_USER => 'Alamat email ini tidak ditemukan di sistem staf kami.',
            default => __($status),
        };

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }
}
