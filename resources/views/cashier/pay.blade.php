<x-app-layout title="Pembayaran Tagihan - SIMRS Medika">
    <div class="max-w-3xl mx-auto space-y-6" x-data="{
        totalAmount: {{ $invoice->total_amount }},
        amountPaid: {{ $invoice->total_amount }},
        get change() {
            return this.amountPaid - this.totalAmount;
        },
        get isValid() {
            return this.change >= 0;
        },
        setAmount(val) {
            this.amountPaid = val;
        }
    }">
        <div>
            <a href="{{ route('cashier.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Antrean Kasir
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pembayaran Kasir & Billing</h1>
            <p class="text-sm text-slate-500 mt-1">Invoice No: <span class="font-mono font-semibold text-slate-800">{{ $invoice->invoice_number }}</span></p>
        </div>

        <!-- Patient & Visit Summary -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Nama Pasien</span>
                    <p class="font-bold text-slate-900 mt-0.5">{{ $invoice->registration->patient->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">No. Rekam Medis</span>
                    <p class="font-mono font-bold text-teal-700 mt-0.5">{{ $invoice->registration->patient->medical_record_number }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Poliklinik</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $invoice->registration->polyclinic->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Dokter Pemeriksa</span>
                    <p class="font-medium text-slate-800 mt-0.5">dr. {{ $invoice->registration->doctor->user->name }}</p>
                </div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Rincian Komponen Tagihan</h3>
                <span class="text-xs text-slate-400">Tarif Layanan Medis</span>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-6 py-4 font-semibold text-slate-800">Jasa Konsultasi Dokter Spesialis</td>
                        <td class="px-6 py-4 text-right font-mono font-semibold text-slate-900">
                            Rp {{ number_format($invoice->consultation_fee, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($invoice->registration->medicalRecord?->prescription)
                        @foreach($invoice->registration->medicalRecord->prescription->items as $item)
                            <tr class="bg-slate-50/50">
                                <td class="px-6 py-3 pl-10 text-xs text-slate-600">
                                    • Obat: {{ $item->medicine->name }} &times; {{ $item->quantity }} {{ $item->medicine->unit }} ({{ $item->dosage_instructions }})
                                </td>
                                <td class="px-6 py-3 text-right font-mono text-xs text-slate-700">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td class="px-6 py-4 font-semibold text-slate-800">Total Biaya Obat Farmasi</td>
                        <td class="px-6 py-4 text-right font-mono font-semibold text-slate-900">
                            Rp {{ number_format($invoice->medicine_fee, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-teal-50/60 font-bold text-slate-900 border-t border-teal-100">
                    <tr>
                        <td class="px-6 py-4 text-base text-teal-900">TOTAL YANG HARUS DIBAYAR:</td>
                        <td class="px-6 py-4 text-right font-mono text-2xl text-teal-800">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Cash Payment Form with Alpine.js Change Calculator -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Kalkulator Pembayaran Tunai (Cash)</span>
            </h3>

            <form action="{{ route('cashier.process', $invoice) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="amount_paid" class="block text-sm font-semibold text-slate-700">Nominal Uang Tunai Diterima (Rp) *</label>
                    <div class="mt-1 relative rounded-xl shadow-xs">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">Rp</span>
                        <input type="number" id="amount_paid" name="amount_paid" x-model.number="amountPaid" required
                            class="block w-full pl-12 pr-4 py-3 border border-slate-300 rounded-xl font-mono text-xl font-bold text-slate-900 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <!-- Quick Cash Shortcut Buttons -->
                    <div class="flex flex-wrap gap-2 mt-3">
                        <button type="button" @click="setAmount(totalAmount)"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            Uang Pas
                        </button>
                        <button type="button" @click="setAmount(50000)"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            Rp 50.000
                        </button>
                        <button type="button" @click="setAmount(100000)"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            Rp 100.000
                        </button>
                        <button type="button" @click="setAmount(150000)"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            Rp 150.000
                        </button>
                        <button type="button" @click="setAmount(200000)"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            Rp 200.000
                        </button>
                    </div>
                </div>

                <!-- Real-time Change Indicator -->
                <div class="p-5 rounded-xl border"
                    :class="isValid ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold uppercase tracking-wider">
                            <span x-show="isValid">Uang Kembalian Pasien:</span>
                            <span x-show="!isValid">Uang Kurang Sebesar:</span>
                        </span>
                        <span class="text-2xl font-mono font-extrabold"
                            x-text="isValid ? `Rp ${new Intl.NumberFormat('id-ID').format(change)}` : `- Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(change))}`">
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a href="{{ route('cashier.index') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" :disabled="!isValid"
                        :class="isValid ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer' : 'bg-slate-300 text-slate-500 cursor-not-allowed'"
                        class="px-8 py-3 rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Selesaikan Pembayaran & Cetak Kwitansi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
