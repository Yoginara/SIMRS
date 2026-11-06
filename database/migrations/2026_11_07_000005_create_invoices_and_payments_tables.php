<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 25)->unique()->index();
            $table->foreignId('registration_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('consultation_fee', 12, 2)->default(0.00);
            $table->decimal('medicine_fee', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'cancelled'])->default('unpaid')->index();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->string('payment_method', 20)->default('cash');
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
