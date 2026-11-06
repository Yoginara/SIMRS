<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Cashier\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\ConsultationController;
use App\Http\Controllers\FrontOffice\PatientController;
use App\Http\Controllers\FrontOffice\RegistrationController;
use App\Http\Controllers\Pharmacy\PharmacyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMRS MVP
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Download & Preview Portofolio PDF
Route::get('/portfolio/download', function () {
    return response()->download(base_path('Portofolio_SIMRS_Medika.pdf'));
})->name('portfolio.download');

Route::get('/portfolio/preview', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('portfolio.pdf');
    $pdf->setPaper('a4', 'portrait');
    return $pdf->stream('Portofolio_SIMRS_Medika.pdf');
})->name('portfolio.preview');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Google OAuth 2.0 via Laravel Socialite
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Forgot & Reset Password
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. FRONT OFFICE / ADMIN MODULE
    Route::middleware('role:admin')->group(function () {
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');

        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/create', [RegistrationController::class, 'create'])->name('registrations.create');
        Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');

        // Manajemen Staf & Akun Pengguna (Google SSO Whitelist)
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
        Route::post('/admin/users/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // 2. DOCTOR MODULE (EMR & E-Prescription)
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/queue', [ConsultationController::class, 'index'])->name('queue');
        Route::get('/examine/{registration}', [ConsultationController::class, 'examine'])->name('examine');
        Route::post('/examine/{registration}', [ConsultationController::class, 'store'])->name('examine.store');
    });

    // 3. PHARMACY MODULE (Prescription Dispensing & Stock)
    Route::middleware('role:pharmacist')->prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/', [PharmacyController::class, 'index'])->name('index');
        Route::get('/dispense/{prescription}', [PharmacyController::class, 'show'])->name('show');
        Route::post('/dispense/{prescription}', [PharmacyController::class, 'dispense'])->name('dispense');
        Route::get('/medicines', [PharmacyController::class, 'medicines'])->name('medicines');
        Route::post('/medicines/{medicine}/stock', [PharmacyController::class, 'updateStock'])->name('medicines.stock');
    });

    // 4. CASHIER MODULE (Billing & Cash Payment)
    Route::middleware('role:cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/pay/{invoice}', [BillingController::class, 'pay'])->name('pay');
        Route::post('/pay/{invoice}', [BillingController::class, 'processPayment'])->name('process');
        Route::get('/receipt/{payment}', [BillingController::class, 'receipt'])->name('receipt');
    });
});
