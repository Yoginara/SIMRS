<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->text('chief_complaint')->comment('Keluhan Utama Pasien');
            $table->json('vital_signs')->nullable()->comment('Tensi, Nadi, Suhu, Pernapasan dsb');
            $table->text('physical_examination')->nullable()->comment('Pemeriksaan Fisik');
            $table->string('icd10_code', 10)->nullable()->index()->comment('Kode Diagnosa ICD-10');
            $table->string('diagnosis_name', 255)->comment('Deskripsi Diagnosa Medis');
            $table->text('doctor_notes')->nullable()->comment('Catatan Tambahan Dokter / Instruksi Medis');
            $table->timestamp('finalized_at')->nullable()->comment('Kunci Pengubahan RME');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
