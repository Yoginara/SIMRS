<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    /**
     * Display the doctor's queue for today.
     */
    public function index(Request $request): View
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->firstOrFail();

        $queue = Registration::with(['patient', 'polyclinic'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('registered_at', today())
            ->whereIn('status', ['registered', 'in_consultation'])
            ->orderBy('queue_number')
            ->get();

        $completedToday = Registration::with(['patient', 'medicalRecord'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('registered_at', today())
            ->whereNotIn('status', ['registered', 'in_consultation', 'cancelled'])
            ->orderByDesc('id')
            ->get();

        return view('doctor.queue', compact('doctor', 'queue', 'completedToday'));
    }

    /**
     * Show the examination and EMR form for a registered patient.
     */
    public function examine(Registration $registration, Request $request): View|RedirectResponse
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->firstOrFail();

        if ($registration->doctor_id !== $doctor->id) {
            abort(403, 'Pasien ini tidak berada di bawah jadwal pemeriksaan Anda.');
        }

        // Set status to in_consultation if still registered
        if ($registration->status === 'registered') {
            $registration->update(['status' => 'in_consultation']);
        }

        // Fetch past medical history for this patient
        $medicalHistory = MedicalRecord::whereHas('registration', fn($q) => $q->where('patient_id', $registration->patient_id))
            ->with(['doctor.user', 'prescription.items.medicine'])
            ->latest()
            ->take(5)
            ->get();

        $medicines = Medicine::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('doctor.examine', compact('registration', 'doctor', 'medicalHistory', 'medicines'));
    }

    /**
     * Store the medical record and electronic prescription.
     */
    public function store(Registration $registration, Request $request): RedirectResponse
    {
        $doctor = Doctor::where('user_id', $request->user()->id)->firstOrFail();

        if ($registration->doctor_id !== $doctor->id) {
            abort(403, 'Anda tidak berwenang memperbarui rekam medis kunjungan ini.');
        }

        $validated = $request->validate([
            'chief_complaint' => ['required', 'string'],
            'systolic' => ['nullable', 'numeric'],
            'diastolic' => ['nullable', 'numeric'],
            'heart_rate' => ['nullable', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'physical_examination' => ['nullable', 'string'],
            'icd10_code' => ['nullable', 'string', 'max:10'],
            'diagnosis_name' => ['required', 'string', 'max:255'],
            'doctor_notes' => ['nullable', 'string'],
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.medicine_id' => ['required_with:prescriptions', 'exists:medicines,id'],
            'prescriptions.*.quantity' => ['required_with:prescriptions', 'integer', 'min:1'],
            'prescriptions.*.dosage_instructions' => ['required_with:prescriptions', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($registration, $doctor, $validated) {
            // 1. Create Immutable Medical Record
            $vitalSigns = [
                'systolic' => $validated['systolic'] ?? null,
                'diastolic' => $validated['diastolic'] ?? null,
                'heart_rate' => $validated['heart_rate'] ?? null,
                'temperature' => $validated['temperature'] ?? null,
            ];

            $medicalRecord = MedicalRecord::create([
                'registration_id' => $registration->id,
                'doctor_id' => $doctor->id,
                'chief_complaint' => $validated['chief_complaint'],
                'vital_signs' => $vitalSigns,
                'physical_examination' => $validated['physical_examination'] ?? null,
                'icd10_code' => $validated['icd10_code'] ?? null,
                'diagnosis_name' => $validated['diagnosis_name'],
                'doctor_notes' => $validated['doctor_notes'] ?? null,
                'finalized_at' => now(),
            ]);

            // 2. Handle Prescriptions
            $hasPrescription = ! empty($validated['prescriptions']);

            if ($hasPrescription) {
                $prescription = Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'status' => 'pending',
                ]);

                foreach ($validated['prescriptions'] as $item) {
                    $medicine = Medicine::findOrFail($item['medicine_id']);
                    $unitPrice = $medicine->price;
                    $subtotal = $unitPrice * $item['quantity'];

                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $item['quantity'],
                        'dosage_instructions' => $item['dosage_instructions'],
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                }

                $registration->update(['status' => 'waiting_pharmacy']);
            } else {
                // If no medicine prescribed, generate consultation invoice directly and advance to cashier
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string) (Invoice::count() + 1), 4, '0', STR_PAD_LEFT);
                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'registration_id' => $registration->id,
                    'consultation_fee' => $doctor->consultation_fee,
                    'medicine_fee' => 0.00,
                    'total_amount' => $doctor->consultation_fee,
                    'payment_status' => 'unpaid',
                ]);

                $registration->update(['status' => 'waiting_payment']);
            }
        });

        return redirect()->route('doctor.queue')->with(
            'status',
            "Pemeriksaan RME pasien {$registration->patient->name} berhasil diselesaikan dan dikunci."
        );
    }
}
