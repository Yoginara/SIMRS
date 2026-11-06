<?php

namespace Tests\Unit;

use App\Actions\Pharmacy\DispensePrescriptionAction;
use App\Actions\Registration\GenerateMedicalRecordNumberAction;
use App\Exceptions\InsufficientStockException;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimrsBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_generate_medical_record_number_action_creates_sequential_numbers(): void
    {
        $action = new GenerateMedicalRecordNumberAction();
        $nextNumber = $action->execute();

        $expectedPrefix = 'RM-' . date('Ym') . '-';
        $this->assertStringStartsWith($expectedPrefix, $nextNumber);
    }

    public function test_dispense_prescription_action_deducts_stock_and_updates_status(): void
    {
        $patient = Patient::first();
        $doctor = Doctor::first();
        $pharmacist = User::role('pharmacist')->first();

        $registration = Registration::create([
            'registration_number' => 'REG-TEST-001',
            'patient_id' => $patient->id,
            'polyclinic_id' => $doctor->polyclinic_id,
            'doctor_id' => $doctor->id,
            'queue_number' => 1,
            'status' => 'waiting_pharmacy',
            'registered_at' => now(),
        ]);

        $medicalRecord = MedicalRecord::create([
            'registration_id' => $registration->id,
            'doctor_id' => $doctor->id,
            'chief_complaint' => 'Nyeri lambung',
            'diagnosis_name' => 'Gastritis',
            'finalized_at' => now(),
        ]);

        $medicine = Medicine::where('code', 'MED-003')->first();
        $initialStock = $medicine->stock;

        $prescription = Prescription::create([
            'medical_record_id' => $medicalRecord->id,
            'status' => 'pending',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'quantity' => 5,
            'dosage_instructions' => '3x1 tablet',
            'unit_price' => $medicine->price,
            'subtotal' => $medicine->price * 5,
        ]);

        $action = new DispensePrescriptionAction();
        $action->execute($prescription, $pharmacist->id);

        $this->assertEquals($initialStock - 5, $medicine->fresh()->stock);
        $this->assertEquals('dispensed', $prescription->fresh()->status);
        $this->assertEquals('waiting_payment', $registration->fresh()->status);
    }

    public function test_dispense_prescription_action_fails_when_stock_is_insufficient(): void
    {
        $this->expectException(InsufficientStockException::class);

        $patient = Patient::first();
        $doctor = Doctor::first();
        $pharmacist = User::role('pharmacist')->first();

        $registration = Registration::create([
            'registration_number' => 'REG-TEST-002',
            'patient_id' => $patient->id,
            'polyclinic_id' => $doctor->polyclinic_id,
            'doctor_id' => $doctor->id,
            'queue_number' => 2,
            'status' => 'waiting_pharmacy',
            'registered_at' => now(),
        ]);

        $medicalRecord = MedicalRecord::create([
            'registration_id' => $registration->id,
            'doctor_id' => $doctor->id,
            'chief_complaint' => 'Infeksi',
            'diagnosis_name' => 'Faringitis',
            'finalized_at' => now(),
        ]);

        $medicine = Medicine::where('code', 'MED-002')->first();
        $prescription = Prescription::create([
            'medical_record_id' => $medicalRecord->id,
            'status' => 'pending',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'quantity' => $medicine->stock + 100, // melebihi stok yang ada
            'dosage_instructions' => '3x1 kaplet',
            'unit_price' => $medicine->price,
            'subtotal' => $medicine->price * ($medicine->stock + 100),
        ]);

        $action = new DispensePrescriptionAction();
        $action->execute($prescription, $pharmacist->id);
    }
}
