<x-app-layout title="Kasir & Billing - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kasir Pembayaran & Billing Tagihan</h1>
                <p class="text-sm text-slate-500 mt-1">Penerimaan pembayaran tunai, kalkulasi uang kembalian, dan cetak bukti kwitansi.</p>
            </div>
            <div class="px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-xs font-semibold text-amber-800">
                Tagihan Belum Lunas: {{ $unpaidInvoices->count() }} Pasien
            </div>
        </div>

        <!-- Unpaid Invoices Section -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Antrean Pembayaran Kasir</h3>
                <span class="text-xs text-rose-600 font-semibold">Menunggu Pelunasan</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($unpaidInvoices as $invoice)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900 text-base">
                                    {{ $invoice->registration->patient->name }}
                                </span>
                                <span class="font-mono text-xs text-teal-700 bg-teal-50 px-2 py-0.5 rounded font-bold">
                                    {{ $invoice->registration->patient->medical_record_number }}
                                </span>
                                <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $invoice->invoice_number }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500">
                                Poli: <span class="font-medium text-slate-700">{{ $invoice->registration->polyclinic->name }}</span> • 
                                Dokter: <span class="font-medium text-slate-700">dr. {{ $invoice->registration->doctor->user->name }}</span>
                            </div>
                            <div class="text-xs text-slate-600 flex gap-4 pt-1 font-mono">
                                <span>Konsultasi: Rp {{ number_format($invoice->consultation_fee, 0, ',', '.') }}</span>
                                <span>Obat: Rp {{ number_format($invoice->medicine_fee, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block font-semibold uppercase">Total Tagihan</span>
                                <span class="text-xl font-mono font-extrabold text-teal-800">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="{{ route('cashier.pay', $invoice) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Proses Bayar Tunai</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 text-sm">
                        Tidak ada antrean tagihan yang belum lunas.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Payments Table -->
        @if($recentPayments->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Riwayat Pembayaran Kasir Selesai</h3>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $recentPayments->count() }} Kwitansi Tercetak</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3.5">No. Invoice</th>
                                <th class="px-6 py-3.5">Nama Pasien & RM</th>
                                <th class="px-6 py-3.5">Total Tagihan</th>
                                <th class="px-6 py-3.5">Dibayar (Tunai)</th>
                                <th class="px-6 py-3.5">Kembalian</th>
                                <th class="px-6 py-3.5">Waktu Transaksi</th>
                                <th class="px-6 py-3.5 text-right">Kwitansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentPayments as $pay)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $pay->invoice->invoice_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $pay->invoice->registration->patient->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $pay->invoice->registration->patient->medical_record_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-teal-800">Rp {{ number_format($pay->invoice->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-mono text-slate-700">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-mono text-slate-500">Rp {{ number_format($pay->change_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $pay->paid_at->format('d/m H:i') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('cashier.receipt', $pay) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span>Cetak Nota</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
