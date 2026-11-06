<?php

namespace App\Actions\Pharmacy;

use App\Exceptions\InsufficientStockException;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class DispensePrescriptionAction
{
    /**
     * Dispense a prescription with pessimistic locking and database transaction.
     *
     * @throws InsufficientStockException
     */
    public function execute(Prescription $prescription, int $pharmacistId): void
    {
        DB::transaction(function () use ($prescription, $pharmacistId) {
            $items = $prescription->items()->with('medicine')->get();

            foreach ($items as $item) {
                // Pessimistic lock for update to prevent race conditions
                $medicine = Medicine::where('id', $item->medicine_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($medicine->stock < $item->quantity) {
                    throw new InsufficientStockException(
                        "Stok obat '{$medicine->name}' tidak mencukupi (Tersedia: {$medicine->stock}, Dibutuhkan: {$item->quantity})."
                    );
                }

                $medicine->decrement('stock', $item->quantity);
            }

            $prescription->update([
                'status' => 'dispensed',
                'pharmacist_id' => $pharmacistId,
                'dispensed_at' => now(),
            ]);

            // Transition registration state to waiting cashier payment
            $prescription->medicalRecord->registration->update([
                'status' => 'waiting_payment',
            ]);
        });
    }
}
