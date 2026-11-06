<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_number', 20)->unique()->index();
            $table->string('nik', 16)->unique()->index()->comment('Nomor Induk Kependudukan 16 digit');
            $table->string('name', 255);
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone', 20);
            $table->text('address');
            $table->string('blood_type', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 25)->unique()->index();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('polyclinic_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->integer('queue_number');
            $table->enum('status', [
                'registered',
                'in_consultation',
                'waiting_pharmacy',
                'waiting_payment',
                'completed',
                'cancelled'
            ])->default('registered')->index();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('patients');
    }
};
