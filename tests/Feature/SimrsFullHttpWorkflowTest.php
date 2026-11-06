<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimrsFullHttpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_complete_end_to_end_hospital_workflow_via_http(): void
    {
        $admin = User::where('email', 'admin@simrs.test')->first();
        $doctorUser = User::where('email', 'dokter@simrs.test')->first();
        $pharmacist = User::where('email', 'farmasi@simrs.test')->first();
        $cashier = User::where('email', 'kasir@simrs.test')->first();

        $doctor = Doctor::where('user_id', $doctorUser->id)->first();
        $medicine = Medicine::where('code', 'MED-001')->first(); // Paracetamol
        $initialStock = $medicine->stock;

        // -------------------------------------------------------------
        // STEP 1: FRONT OFFICE (ADMIN) - REGISTRASI PASIEN & POLI
        // -------------------------------------------------------------
        $response = $this->actingAs($admin)->post('/patients', [
            'nik' => '3273019909990001',
            'name' => 'Bambang Soepomo',
            'birth_date' => '1988-09-09',
            'gender' => 'male',
            'phone' => '081299998888',
            'address' => 'Jl. Kebon Jati No. 88, Bandung',
            'blood_type' => 'B',
        ]);

        $response->assertRedirect('/patients');
        $patient = Patient::where('nik', '3273019909990001')->first();
        $this->assertNotNull($patient);

        // Pendaftaran ke Antrean Poli
        $regResponse = $this->actingAs($admin)->post('/registrations', [
            'patient_id' => $patient->id,
            'polyclinic_id' => $doctor->polyclinic_id,
            'doctor_id' => $doctor->id,
        ]);

        $regResponse->assertRedirect('/registrations');
        $registration = Registration::where('patient_id', $patient->id)->first();
        $this->assertNotNull($registration);
        $this->assertEquals('registered', $registration->status);

        // -------------------------------------------------------------
        // STEP 2: DOKTER POLI - PEMERIKSAAN RME & PERESEPAN ELEKTRONIK
        // -------------------------------------------------------------
        $this->actingAs($doctorUser)->get("/doctor/examine/{$registration->id}")
            ->assertStatus(200);

        $examResponse = $this->actingAs($doctorUser)->post("/doctor/examine/{$registration->id}", [
            'chief_complaint' => 'Demam dan pusing berulang sejak kemarin sore.',
            'systolic' => 120,
            'diastolic' => 80,
            'heart_rate' => 80,
            'temperature' => 38.0,
            'physical_examination' => 'Mata cekung (-), febris (+)',
            'icd10_code' => 'R50.9',
            'diagnosis_name' => 'Febris Akut / Demam',
            'doctor_notes' => 'Banyak minum air putih dan istirahat.',
            'prescriptions' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => 2,
                    'dosage_instructions' => '3x1 tablet sesudah makan bila demam',
                ],
            ],
        ]);

        $examResponse->assertRedirect('/doctor/queue');
        $registration->refresh();
        $this->assertEquals('waiting_pharmacy', $registration->status);
        $this->assertNotNull($registration->medicalRecord);
        $this->assertNotNull($registration->medicalRecord->prescription);

        $prescription = $registration->medicalRecord->prescription;
        $this->assertEquals('pending', $prescription->status);

        // -------------------------------------------------------------
        // STEP 3: FARMASI - DISPENSING & PENGURANGAN STOK ATOMIK
        // -------------------------------------------------------------
        $this->actingAs($pharmacist)->get("/pharmacy/dispense/{$prescription->id}")
            ->assertStatus(200);

        $dispenseResponse = $this->actingAs($pharmacist)->post("/pharmacy/dispense/{$prescription->id}");
        $dispenseResponse->assertRedirect('/pharmacy');

        $prescription->refresh();
        $registration->refresh();
        $medicine->refresh();

        $this->assertEquals('dispensed', $prescription->status);
        $this->assertEquals('waiting_payment', $registration->status);
        $this->assertEquals($initialStock - 2, $medicine->stock);

        $invoice = Invoice::where('registration_id', $registration->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('unpaid', $invoice->payment_status);
        $this->assertEquals($doctor->consultation_fee + ($medicine->price * 2), $invoice->total_amount);

        // -------------------------------------------------------------
        // STEP 4: KASIR - PERHITUNGAN BILLING & PEMBAYARAN TUNAI
        // -------------------------------------------------------------
        $this->actingAs($cashier)->get("/cashier/pay/{$invoice->id}")
            ->assertStatus(200);

        $amountPaid = 100000.00;
        $payResponse = $this->actingAs($cashier)->post("/cashier/pay/{$invoice->id}", [
            'amount_paid' => $amountPaid,
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);
        $payResponse->assertRedirect("/cashier/receipt/{$payment->id}");

        $invoice->refresh();
        $registration->refresh();

        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals('completed', $registration->status);
        $this->assertEquals($amountPaid - $invoice->total_amount, $payment->change_amount);

        // Verify receipt view renders correctly
        $receiptResponse = $this->actingAs($cashier)->get("/cashier/receipt/{$payment->id}");
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('BUKTI PEMBAYARAN KASIR');
        $receiptResponse->assertSee($patient->name);
        $receiptResponse->assertSee('LUNAS');
    }
}
