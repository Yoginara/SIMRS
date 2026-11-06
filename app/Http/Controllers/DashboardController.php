<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the role-aware SIMRS dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $roles = $user->getRoleNames();
        $today = now()->startOfDay();

        $stats = [];

        // Role-based contextual metrics
        if ($user->hasRole('admin')) {
            $stats['admin'] = [
                'total_patients' => Patient::count(),
                'total_doctors' => Doctor::where('is_active', true)->count(),
                'total_polyclinics' => Polyclinic::where('is_active', true)->count(),
                'today_registrations' => Registration::whereDate('registered_at', today())->count(),
                'recent_registrations' => Registration::with(['patient', 'polyclinic', 'doctor.user'])
                    ->latest()
                    ->take(5)
                    ->get(),
            ];
        }

        if ($user->hasRole('doctor')) {
            $doctorProfile = Doctor::with('polyclinic')->where('user_id', $user->id)->first();
            $polyclinicId = $doctorProfile?->polyclinic_id;

            $stats['doctor'] = [
                'profile' => $doctorProfile,
                'waiting_patients' => Registration::where('doctor_id', $doctorProfile?->id)
                    ->whereIn('status', ['registered', 'in_consultation'])
                    ->whereDate('registered_at', today())
                    ->count(),
                'examined_today' => MedicalRecord::where('doctor_id', $doctorProfile?->id)
                    ->whereDate('created_at', today())
                    ->count(),
                'queue_list' => Registration::with('patient')
                    ->where('doctor_id', $doctorProfile?->id)
                    ->whereDate('registered_at', today())
                    ->orderBy('queue_number')
                    ->take(8)
                    ->get(),
            ];
        }

        if ($user->hasRole('pharmacist')) {
            $stats['pharmacist'] = [
                'pending_prescriptions' => Prescription::where('status', 'pending')->count(),
                'dispensed_today' => Prescription::where('status', 'dispensed')
                    ->whereDate('dispensed_at', today())
                    ->count(),
                'low_stock_medicines' => Medicine::where('stock', '<', 50)->get(),
                'queue_list' => Prescription::with(['medicalRecord.registration.patient', 'medicalRecord.doctor.user'])
                    ->where('status', 'pending')
                    ->latest()
                    ->take(8)
                    ->get(),
            ];
        }

        if ($user->hasRole('cashier')) {
            $stats['cashier'] = [
                'unpaid_invoices' => Invoice::where('payment_status', 'unpaid')->count(),
                'paid_today_count' => Payment::whereDate('paid_at', today())->count(),
                'total_revenue_today' => Payment::whereDate('paid_at', today())->sum('amount_paid') - Payment::whereDate('paid_at', today())->sum('change_amount'),
                'recent_payments' => Payment::with(['invoice.registration.patient'])
                    ->latest()
                    ->take(8)
                    ->get(),
            ];
        }

        return view('dashboard', [
            'user' => $user,
            'roles' => $roles,
            'stats' => $stats,
        ]);
    }
}
