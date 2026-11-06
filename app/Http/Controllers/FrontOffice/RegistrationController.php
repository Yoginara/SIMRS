<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Display a listing of registrations for today.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $registrations = Registration::with(['patient', 'polyclinic', 'doctor.user'])
            ->when($status, fn($query, $status) => $query->where('status', $status))
            ->whereDate('registered_at', today())
            ->orderByDesc('id')
            ->paginate(15);

        return view('front-office.registrations.index', compact('registrations', 'status'));
    }

    /**
     * Show form for registering a patient to a polyclinic queue.
     */
    public function create(Request $request): View
    {
        $patients = Patient::orderBy('name')->get();
        $polyclinics = Polyclinic::where('is_active', true)->get();
        $doctors = Doctor::with(['user', 'polyclinic'])->where('is_active', true)->get();

        $selectedPatientId = $request->query('patient_id');

        return view('front-office.registrations.create', compact('patients', 'polyclinics', 'doctors', 'selectedPatientId'));
    }

    /**
     * Store registration and assign sequential queue number.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'polyclinic_id' => ['required', 'exists:polyclinics,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
        ]);

        // Sequential daily queue number for selected polyclinic
        $latestQueue = Registration::where('polyclinic_id', $validated['polyclinic_id'])
            ->whereDate('registered_at', today())
            ->max('queue_number');

        $queueNumber = ($latestQueue ?? 0) + 1;

        // Unique registration code
        $countToday = Registration::whereDate('registered_at', today())->count() + 1;
        $registrationNumber = 'REG-' . date('Ymd') . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);

        $registration = Registration::create([
            'registration_number' => $registrationNumber,
            'patient_id' => $validated['patient_id'],
            'polyclinic_id' => $validated['polyclinic_id'],
            'doctor_id' => $validated['doctor_id'],
            'queue_number' => $queueNumber,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return redirect()->route('registrations.index')->with(
            'status',
            "Pasien berhasil didaftarkan ke antrean #{$queueNumber} dengan Nomor Registrasi: {$registration->registration_number}."
        );
    }
}
