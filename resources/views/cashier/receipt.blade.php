<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi Pembayaran - {{ $payment->invoice->invoice_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 text-slate-800 antialiased">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Top Action Controls (hidden on print) -->
        <div class="no-print mb-6 flex items-center justify-between">
            <a href="{{ route('cashier.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 hover:text-teal-800">
                &larr; Kembali ke Daftar Tagihan Kasir
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Nota / Kwitansi (PDF / Printer)</span>
            </button>
        </div>

        <!-- Receipt Document Container -->
        <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 font-sans text-xs">
            <!-- Hospital Header -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6 text-center">
                <h2 class="text-xl font-black uppercase tracking-wider text-slate-900">RUMAH SAKIT MEDIKA UTAMA</h2>
                <p class="text-slate-500 text-[11px] mt-0.5">Jl. Kesehatan No. 123, Bandung • Telp: (022) 7654321 • Izin Faskes: 503/440/RS-Bdg</p>
                <div class="mt-3 inline-block px-3 py-1 bg-slate-100 rounded text-slate-800 font-bold uppercase tracking-widest text-[10px]">
                    BUKTI PEMBAYARAN KASIR (KWITANSI LUNAS)
                </div>
            </div>

            <!-- Receipt Meta Details -->
            <div class="grid grid-cols-2 gap-4 mb-6 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div><span class="text-slate-400">No. Kwitansi:</span> <span class="font-mono font-bold text-slate-900">{{ $payment->invoice->invoice_number }}</span></div>
                    <div><span class="text-slate-400">No. Registrasi:</span> <span class="font-mono text-slate-700">{{ $payment->invoice->registration->registration_number }}</span></div>
                    <div><span class="text-slate-400">Waktu Bayar:</span> <span class="text-slate-700">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</span></div>
                </div>
                <div class="space-y-1 text-right sm:text-left">
                    <div><span class="text-slate-400">No. Rekam Medis:</span> <span class="font-mono font-bold text-teal-800">{{ $payment->invoice->registration->patient->medical_record_number }}</span></div>
                    <div><span class="text-slate-400">Nama Pasien:</span> <span class="font-bold text-slate-900">{{ $payment->invoice->registration->patient->name }}</span></div>
                    <div><span class="text-slate-400">Poli / Dokter:</span> <span class="text-slate-700">{{ $payment->invoice->registration->polyclinic->name }} (dr. {{ $payment->invoice->registration->doctor->user->name }})</span></div>
                </div>
            </div>

            <!-- Billing Details -->
            <table class="w-full text-left mb-6">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                        <th class="py-2">Deskripsi Layanan / Item</th>
                        <th class="py-2 text-center">Jumlah</th>
                        <th class="py-2 text-right">Tarif</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2.5 font-medium text-slate-800">Jasa Konsultasi Dokter Spesialis</td>
                        <td class="py-2.5 text-center">1</td>
                        <td class="py-2.5 text-right font-mono">Rp {{ number_format($payment->invoice->consultation_fee, 0, ',', '.') }}</td>
                        <td class="py-2.5 text-right font-mono font-semibold">Rp {{ number_format($payment->invoice->consultation_fee, 0, ',', '.') }}</td>
                    </tr>
                    @if($payment->invoice->registration->medicalRecord?->prescription)
                        @foreach($payment->invoice->registration->medicalRecord->prescription->items as $item)
                            <tr>
                                <td class="py-2.5 text-slate-700">
                                    {{ $item->medicine->name }}
                                    <span class="text-[10px] text-slate-400 block">{{ $item->dosage_instructions }}</span>
                                </td>
                                <td class="py-2.5 text-center font-mono">{{ $item->quantity }} {{ $item->medicine->unit }}</td>
                                <td class="py-2.5 text-right font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-2.5 text-right font-mono font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="border-t-2 border-slate-900 font-bold">
                    <tr>
                        <td colspan="3" class="py-2.5 text-right text-slate-900 text-sm">TOTAL TAGIHAN:</td>
                        <td class="py-2.5 text-right font-mono text-base text-slate-900">
                            Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="text-slate-600 font-normal">
                        <td colspan="3" class="py-1 text-right">Uang Tunai Diterima:</td>
                        <td class="py-1 text-right font-mono">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="text-teal-700 font-bold">
                        <td colspan="3" class="py-1 text-right">UANG KEMBALIAN:</td>
                        <td class="py-1 text-right font-mono">Rp {{ number_format($payment->change_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Footer Signatures -->
            <div class="mt-8 pt-4 border-t border-dashed border-slate-200 flex justify-between items-end text-center">
                <div>
                    <p class="text-[10px] text-slate-400">Pasien / Keluarga</p>
                    <div class="h-14"></div>
                    <p class="font-semibold text-slate-800">({{ $payment->invoice->registration->patient->name }})</p>
                </div>
                <div>
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-extrabold rounded text-[11px] uppercase tracking-wider mb-2">
                        ★ LUNAS ★
                    </span>
                    <p class="text-[10px] text-slate-400">Kasir yang Bertugas</p>
                    <div class="h-10"></div>
                    <p class="font-semibold text-slate-800">({{ $payment->cashier->name }})</p>
                </div>
            </div>

            <p class="mt-8 text-center text-[10px] text-slate-400 italic">
                Semoga lekas sembuh. Simpan struk ini sebagai bukti pembayaran yang sah.
            </p>
        </div>
    </div>
</body>
</html>
