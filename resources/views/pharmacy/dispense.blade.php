<x-app-layout title="Validasi & Dispensing Obat - SIMRS Medika">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('pharmacy.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Antrean Resep
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Validasi & Penyerahan Resep Obat</h1>
            <p class="text-sm text-slate-500 mt-1">Pastikan ketersediaan stok fisik dan verifikasi aturan pakai sebelum menyerahkan obat.</p>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Nama Pasien</span>
                    <p class="font-bold text-slate-900 mt-0.5">{{ $prescription->medicalRecord->registration->patient->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">No. RM</span>
                    <p class="font-mono font-bold text-teal-700 mt-0.5">{{ $prescription->medicalRecord->registration->patient->medical_record_number }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Dokter Peresep</span>
                    <p class="font-medium text-slate-800 mt-0.5">dr. {{ $prescription->medicalRecord->doctor->user->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Diagnosa</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $prescription->medicalRecord->diagnosis_name }}</p>
                </div>
            </div>
        </div>

        <!-- Prescribed Items Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Rincian Obat yang Diresepkan</h3>
                <span class="text-xs text-slate-400">Total: {{ $prescription->items->count() }} Jenis Obat</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Nama Obat</th>
                            <th class="px-6 py-3.5">Jumlah</th>
                            <th class="px-6 py-3.5">Aturan Pakai (Signa)</th>
                            <th class="px-6 py-3.5">Stok Tersedia</th>
                            <th class="px-6 py-3.5">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($prescription->items as $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $item->medicine->name }}
                                    <div class="text-xs text-slate-400 font-mono">Kode: {{ $item->medicine->code }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                    {{ $item->quantity }} {{ $item->medicine->unit }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $item->dosage_instructions }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->medicine->stock >= $item->quantity)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Tersedia ({{ $item->medicine->stock }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">
                                            Kurang (Hanya {{ $item->medicine->stock }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono font-semibold text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 text-slate-900 font-bold border-t border-slate-200">
                        <tr>
                            <td colspan="4" class="px-6 py-3.5 text-right">Total Biaya Obat:</td>
                            <td class="px-6 py-3.5 font-mono text-teal-700 text-base">
                                Rp {{ number_format($prescription->items->sum('subtotal'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Dispense Form -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                <p class="font-semibold text-slate-700">Perhatian Apoteker:</p>
                <p>Klik tombol di samping untuk memotong stok fisik secara atomik dan meneruskan tagihan ke Kasir.</p>
            </div>
            <form action="{{ route('pharmacy.dispense', $prescription) }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md shadow-emerald-500/20 text-sm transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Validasi Resep & Potong Stok Obat</span>
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
