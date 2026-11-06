<?php

namespace App\Http\Controllers\Pharmacy;

use App\Actions\Pharmacy\DispensePrescriptionAction;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PharmacyController extends Controller
{
    /**
     * Display the list of incoming prescriptions and inventory summary.
     */
    public function index(): View
    {
        $pendingPrescriptions = Prescription::with([
            'medicalRecord.registration.patient',
            'medicalRecord.doctor.user',
            'items.medicine',
        ])
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        $dispensedToday = Prescription::with([
            'medicalRecord.registration.patient',
            'pharmacist',
            'items.medicine',
        ])
            ->where('status', 'dispensed')
            ->whereDate('dispensed_at', today())
            ->latest('dispensed_at')
            ->get();

        $lowStockMedicines = Medicine::where('stock', '<', 50)->get();

        return view('pharmacy.index', compact('pendingPrescriptions', 'dispensedToday', 'lowStockMedicines'));
    }

    /**
     * Show detailed prescription dispensing modal / page.
     */
    public function show(Prescription $prescription): View
    {
        $prescription->load([
            'medicalRecord.registration.patient',
            'medicalRecord.doctor.user',
            'items.medicine',
        ]);

        return view('pharmacy.dispense', compact('prescription'));
    }

    /**
     * Dispense medicines with atomic pessimistic locking and generate invoice.
     */
    public function dispense(Prescription $prescription, Request $request, DispensePrescriptionAction $action): RedirectResponse
    {
        try {
            DB::transaction(function () use ($prescription, $request, $action) {
                // 1. Deduct medicine stock atomically
                $action->execute($prescription, $request->user()->id);

                // 2. Generate Invoice for Cashier
                $registration = $prescription->medicalRecord->registration;
                $doctor = $prescription->medicalRecord->doctor;
                $medicineFee = $prescription->items->sum('subtotal');
                $consultationFee = $doctor->consultation_fee;
                $totalAmount = $consultationFee + $medicineFee;

                $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string) (Invoice::count() + 1), 4, '0', STR_PAD_LEFT);

                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'registration_id' => $registration->id,
                    'consultation_fee' => $consultationFee,
                    'medicine_fee' => $medicineFee,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'unpaid',
                ]);
            });

            return redirect()->route('pharmacy.index')->with(
                'status',
                "Resep untuk pasien {$prescription->medicalRecord->registration->patient->name} berhasil divalidasi, stok obat telah dikurangi, dan tagihan telah diteruskan ke Kasir."
            );
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }
    }

    /**
     * Medicine inventory list.
     */
    public function medicines(): View
    {
        $medicines = Medicine::orderBy('name')->paginate(15);

        return view('pharmacy.medicines', compact('medicines'));
    }

    /**
     * Update stock of a medicine.
     */
    public function updateStock(Medicine $medicine, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'added_stock' => ['required', 'integer', 'min:1'],
        ]);

        $medicine->increment('stock', $validated['added_stock']);

        return back()->with('status', "Stok obat {$medicine->name} berhasil ditambah sebanyak {$validated['added_stock']} {$medicine->unit}. Total stok sekarang: {$medicine->stock}.");
    }
}
