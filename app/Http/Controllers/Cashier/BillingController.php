<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Display unpaid invoices and payment history.
     */
    public function index(): View
    {
        $unpaidInvoices = Invoice::with([
            'registration.patient',
            'registration.polyclinic',
            'registration.doctor.user',
            'registration.medicalRecord.prescription.items.medicine',
        ])
            ->where('payment_status', 'unpaid')
            ->orderBy('id')
            ->get();

        $recentPayments = Payment::with([
            'invoice.registration.patient',
            'invoice.registration.polyclinic',
            'cashier',
        ])
            ->latest('paid_at')
            ->take(15)
            ->get();

        return view('cashier.index', compact('unpaidInvoices', 'recentPayments'));
    }

    /**
     * Show cashier payment processing page.
     */
    public function pay(Invoice $invoice): View|RedirectResponse
    {
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('cashier.index')->with('status', 'Tagihan ini telah lunas dibayar.');
        }

        $invoice->load([
            'registration.patient',
            'registration.polyclinic',
            'registration.doctor.user',
            'registration.medicalRecord.prescription.items.medicine',
        ]);

        return view('cashier.pay', compact('invoice'));
    }

    /**
     * Store cash payment and calculate change.
     */
    public function processPayment(Invoice $invoice, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:' . $invoice->total_amount],
        ], [
            'amount_paid.min' => 'Nominal uang tunai yang diserahkan kurang dari total tagihan (Rp ' . number_format($invoice->total_amount, 0, ',', '.') . ').',
        ]);

        $changeAmount = $validated['amount_paid'] - $invoice->total_amount;

        $payment = DB::transaction(function () use ($invoice, $request, $validated, $changeAmount) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'cashier_id' => $request->user()->id,
                'amount_paid' => $validated['amount_paid'],
                'change_amount' => $changeAmount,
                'payment_method' => 'cash',
                'paid_at' => now(),
            ]);

            $invoice->update(['payment_status' => 'paid']);
            $invoice->registration->update(['status' => 'completed']);

            return $payment;
        });

        return redirect()->route('cashier.receipt', $payment)->with(
            'status',
            'Pembayaran berhasil diproses! Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.')
        );
    }

    /**
     * Show clean printable receipt.
     */
    public function receipt(Payment $payment): View
    {
        $payment->load([
            'invoice.registration.patient',
            'invoice.registration.polyclinic',
            'invoice.registration.doctor.user',
            'invoice.registration.medicalRecord.prescription.items.medicine',
            'cashier',
        ]);

        return view('cashier.receipt', compact('payment'));
    }
}
