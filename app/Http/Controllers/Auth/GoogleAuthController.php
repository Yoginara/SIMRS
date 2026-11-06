<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Integrasi Google OAuth memerlukan konfigurasi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada file .env. Saat ini silakan gunakan login Email & Password biasa atau tombol ⚡ Quick Demo Login di bawah.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and verify enterprise whitelist.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mengautentikasi dengan akun Google. Silakan coba kembali.',
            ]);
        }

        // Whitelist Check: Only pre-registered staff accounts are permitted
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akses ditolak: Akun Google Anda (' . $googleUser->getEmail() . ') belum didaftarkan sebagai staf faskes resmi oleh Administrator.',
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akses dinonaktifkan: Akun Anda sedang dalam status non-aktif. Silakan hubungi Administrator.',
            ]);
        }

        // Synchronize Google ID
        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }
}
