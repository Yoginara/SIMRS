<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with roles, permissions, master data, and initial accounts.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Core Permissions
        $permissions = [
            'manage-users',
            'manage-master-data',
            'register-patient',
            'view-patient-queue',
            'create-medical-record',
            'view-medical-records',
            'create-prescription',
            'dispense-medication',
            'manage-medicines',
            'process-payment',
            'view-invoices',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles & Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->givePermissionTo([
            'view-patient-queue',
            'create-medical-record',
            'view-medical-records',
            'create-prescription',
        ]);

        $pharmacistRole = Role::firstOrCreate(['name' => 'pharmacist']);
        $pharmacistRole->givePermissionTo([
            'dispense-medication',
            'manage-medicines',
            'view-medical-records',
        ]);

        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $cashierRole->givePermissionTo([
            'process-payment',
            'view-invoices',
        ]);

        // 3. Create Default Users for Each Role
        $defaultPassword = Hash::make('password');

        $admin = User::firstOrCreate(
            ['email' => 'admin@simrs.test'],
            [
                'name' => 'Administrator SIMRS',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        $doctorUser = User::firstOrCreate(
            ['email' => 'dokter@simrs.test'],
            [
                'name' => 'dr. Budi Santoso, Sp.PD',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        $doctorUser->syncRoles(['doctor']);

        $pharmacistUser = User::firstOrCreate(
            ['email' => 'farmasi@simrs.test'],
            [
                'name' => 'apt. Siti Rahma, S.Farm.',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        $pharmacistUser->syncRoles(['pharmacist']);

        $cashierUser = User::firstOrCreate(
            ['email' => 'kasir@simrs.test'],
            [
                'name' => 'Dewi Lestari, A.Md.',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        $cashierUser->syncRoles(['cashier']);

        // 4. Create Polyclinics
        $poliDalam = Polyclinic::firstOrCreate(
            ['name' => 'Poli Penyakit Dalam'],
            ['room_number' => 'R-101', 'is_active' => true]
        );

        $poliUmum = Polyclinic::firstOrCreate(
            ['name' => 'Poli Umum'],
            ['room_number' => 'R-102', 'is_active' => true]
        );

        $poliGigi = Polyclinic::firstOrCreate(
            ['name' => 'Poli Gigi & Mulut'],
            ['room_number' => 'R-103', 'is_active' => true]
        );

        $poliAnak = Polyclinic::firstOrCreate(
            ['name' => 'Poli Anak'],
            ['room_number' => 'R-104', 'is_active' => true]
        );

        // 5. Create Doctor Profile
        Doctor::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'polyclinic_id' => $poliDalam->id,
                'sip_number' => 'SIP.446/0012/DINKES/2024',
                'specialization' => 'Spesialis Penyakit Dalam',
                'consultation_fee' => 75000.00,
                'is_active' => true,
            ]
        );

        // 6. Master Medicines
        $medicines = [
            ['code' => 'MED-001', 'name' => 'Paracetamol 500 mg', 'unit' => 'Strip', 'stock' => 200, 'price' => 5000.00],
            ['code' => 'MED-002', 'name' => 'Amoxicillin 500 mg', 'unit' => 'Strip', 'stock' => 150, 'price' => 12000.00],
            ['code' => 'MED-003', 'name' => 'Antasida Doen Tablet', 'unit' => 'Strip', 'stock' => 100, 'price' => 6000.00],
            ['code' => 'MED-004', 'name' => 'Cetirizine 10 mg', 'unit' => 'Strip', 'stock' => 80, 'price' => 8500.00],
            ['code' => 'MED-005', 'name' => 'Ibuprofen 400 mg', 'unit' => 'Strip', 'stock' => 120, 'price' => 7500.00],
            ['code' => 'MED-006', 'name' => 'Omeprazole 20 mg', 'unit' => 'Strip', 'stock' => 90, 'price' => 18000.00],
            ['code' => 'MED-007', 'name' => 'Metformin 500 mg', 'unit' => 'Strip', 'stock' => 110, 'price' => 9000.00],
            ['code' => 'MED-008', 'name' => 'Amlodipine 5 mg', 'unit' => 'Strip', 'stock' => 100, 'price' => 10000.00],
        ];

        foreach ($medicines as $item) {
            Medicine::firstOrCreate(['code' => $item['code']], $item);
        }

        // 7. Seed Sample Patients (NIK 16 digit & No RM Standar)
        $patients = [
            [
                'medical_record_number' => 'RM-202611-0001',
                'nik' => '3273011204900001',
                'name' => 'Ahmad Fauzi',
                'birth_date' => '1990-04-12',
                'gender' => 'male',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 45, Bandung',
                'blood_type' => 'O',
            ],
            [
                'medical_record_number' => 'RM-202611-0002',
                'nik' => '3273015508950002',
                'name' => 'Nurul Hidayah',
                'birth_date' => '1995-08-15',
                'gender' => 'female',
                'phone' => '081398765432',
                'address' => 'Jl. Diponegoro No. 12, Bandung',
                'blood_type' => 'A',
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::firstOrCreate(['medical_record_number' => $patientData['medical_record_number']], $patientData);
        }
    }
}
