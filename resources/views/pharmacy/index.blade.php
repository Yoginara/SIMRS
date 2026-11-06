<x-app-layout title="Instalasi Farmasi - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Instalasi Farmasi & Dispensing Obat</h1>
                <p class="text-sm text-slate-500 mt-1">Validasi resep dokter dan pengeluaran obat dengan pemotongan stok otomatis.</p>
            </div>
            <a href="{{ route('pharmacy.medicines') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Kelola Inventori & Stok Obat</span>
            </a>
        </div>

        <!-- Pending Prescriptions Section -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Resep Masuk Menunggu Validasi</h3>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
                    {{ $pendingPrescriptions->count() }} Resep Menunggu
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($pendingPrescriptions as $prescription)
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900 text-base">
                                    {{ $prescription->medicalRecord->registration->patient->name }}
                                </span>
                                <span class="font-mono text-xs text-teal-700 bg-teal-50 px-2 py-0.5 rounded font-bold">
                                    {{ $prescription->medicalRecord->registration->patient->medical_record_number }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500">
                                Dokter Perujuk: <span class="font-semibold text-slate-700">dr. {{ $prescription->medicalRecord->doctor->user->name }}</span> • 
                                Diagnosa: <span class="font-medium text-slate-700">{{ $prescription->medicalRecord->diagnosis_name }}</span>
                            </div>
                            <!-- Prescription items summary -->
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($prescription->items as $item)
                                    <span class="inline-flex items-center text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200">
                                        {{ $item->medicine->name }} &times; {{ $item->quantity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('pharmacy.show', $prescription) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Periksa & Serahkan Obat</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 text-sm">
                        Belum ada antrean resep obat yang masuk saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Dispensed Today Section -->
        @if($dispensedToday->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Resep Diserahkan Hari Ini</h3>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $dispensedToday->count() }} Resep Selesai</span>
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    @foreach($dispensedToday as $disp)
                        <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/50">
                            <div>
                                <span class="font-semibold text-slate-800">{{ $disp->medicalRecord->registration->patient->name }}</span>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Waktu Penyerahan: {{ $disp->dispensed_at->format('H:i') }} • Oleh: {{ $disp->pharmacist->name }}
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Selesai Dispensed
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
