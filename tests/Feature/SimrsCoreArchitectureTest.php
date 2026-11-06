<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimrsCoreArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_roles_and_demo_users_are_correctly_seeded(): void
    {
        $admin = User::where('email', 'admin@simrs.test')->first();
        $doctor = User::where('email', 'dokter@simrs.test')->first();
        $pharmacist = User::where('email', 'farmasi@simrs.test')->first();
        $cashier = User::where('email', 'kasir@simrs.test')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));

        $this->assertNotNull($doctor);
        $this->assertTrue($doctor->hasRole('doctor'));

        $this->assertNotNull($pharmacist);
        $this->assertTrue($pharmacist->hasRole('pharmacist'));

        $this->assertNotNull($cashier);
        $this->assertTrue($cashier->hasRole('cashier'));
    }

    public function test_end_to_end_simrs_workflow_from_registration_to_cashier(): void
    {
        $patient = Patient::first();
        $doctor = Doctor::with('polyclinic')->first();
        $pharmacist = User::role('pharmacist')->first();
        $cashier = User::role('cashier')->first();

        // 1. FRONT OFFICE: Pendaftaran Pasien ke Poli
        $registration = Registration::create([
            'registration_number' => 'REG-' . date('Ymd') . '-0001',
            'patient_id' => $patient->id,
            'polyclinic_id' => $doctor->polyclinic_id,
            'doctor_id' => $doctor->id,
            'queue_number' => 1,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'registered',
        ]);

        // 2. DOKTER: Pemeriksaan RME dan Pembuatan Resep Elektronik
        $registration->update(['status' => 'in_consultation']);

        $medicalRecord = MedicalRecord::create([
            'registration_id' => $registration->id,
            'doctor_id' => $doctor->id,
            'chief_complaint' => 'Demam tinggi selama 3 hari disertai pusing dan nyeri ulu hati.',
            'vital_signs' => [
                'systolic' => 120,
                'diastolic' => 80,
                'heart_rate' => 84,
                'temperature' => 38.5,
            ],
            'physical_examination' => 'Mata anemis (-), epigastrium nyeri tekan (+)',
            'icd10_code' => 'K30',
            'diagnosis_name' => 'Dispepsia / Sakit Maag Akut',
            'doctor_notes' => 'Hindari konsumsi makanan pedas, asam, dan kopi.',
            'finalized_at' => now(),
        ]);

        $prescription = Prescription::create([
            'medical_record_id' => $medicalRecord->id,
            'status' => 'pending',
        ]);

        $medicine = Medicine::where('code', 'MED-003')->first(); // Antasida Doen
        $initialStock = $medicine->stock;
        $prescribedQty = 2;

        $prescriptionItem = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'quantity' => $prescribedQty,
            'dosage_instructions' => '3x1 hari 1 jam sebelum makan (dikunyah)',
            'unit_price' => $medicine->price,
            'subtotal' => $medicine->price * $prescribedQty,
        ]);

        $registration->update(['status' => 'waiting_pharmacy']);

        // 3. FARMASI: Validasi Resep & Pengurangan Stok Obat Atomik
        DB::transaction(function () use ($prescription, $pharmacist) {
            foreach ($prescription->items as $item) {
                $med = Medicine::where('id', $item->medicine_id)->lockForUpdate()->first();
                $this->assertGreaterThanOrEqual($item->quantity, $med->stock);
                $med->decrement('stock', $item->quantity);
            }

            $prescription->update([
                'status' => 'dispensed',
                'pharmacist_id' => $pharmacist->id,
                'dispensed_at' => now(),
            ]);

            $prescription->medicalRecord->registration->update(['status' => 'waiting_payment']);
        });

        // Verifikasi stok berkurang tepat
        $this->assertEquals($initialStock - $prescribedQty, $medicine->fresh()->stock);
        $this->assertEquals('waiting_payment', $registration->fresh()->status);

        // 4. KASIR: Pembuatan Invoice & Pembayaran Tunai
        $consultationFee = $doctor->consultation_fee;
        $medicineFee = $prescriptionItem->subtotal;
        $totalAmount = $consultationFee + $medicineFee;

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-0001',
            'registration_id' => $registration->id,
            'consultation_fee' => $consultationFee,
            'medicine_fee' => $medicineFee,
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
        ]);

        $amountPaid = 100000.00; // Pasien bayar 100.000 tunai
        $changeAmount = $amountPaid - $totalAmount;

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'cashier_id' => $cashier->id,
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $invoice->update(['payment_status' => 'paid']);
        $registration->update(['status' => 'completed']);

        // Assertions Akhir
        $this->assertEquals('paid', $invoice->fresh()->payment_status);
        $this->assertEquals('completed', $registration->fresh()->status);
        $this->assertEquals(100000.00 - ($doctor->consultation_fee + ($medicine->price * 2)), $changeAmount);
    }
}
