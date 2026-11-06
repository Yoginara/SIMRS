<x-app-layout title="Antrean Poli Dokter - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Antrean Pasien Poliklinik</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $doctor->specialization }} — Poli {{ $doctor->polyclinic->name }} (Ruang {{ $doctor->polyclinic->room_number }})
                </p>
            </div>
            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-xl text-xs font-semibold text-blue-800">
                Total Menunggu: {{ $queue->count() }} Pasien
            </div>
        </div>

        <!-- Waiting Queue Section -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Pasien Menunggu Pemeriksaan</h3>
                <span class="text-xs text-slate-400">Urutan Berdasarkan Nomor Antrean</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($queue as $reg)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50">
                        <div class="flex items-start sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-extrabold flex items-center justify-center text-lg shadow-sm shrink-0">
                                #{{ $reg->queue_number }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900">{{ $reg->patient->name }}</h4>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1 font-mono">
                                    <span class="text-teal-700 font-bold">No. RM: {{ $reg->patient->medical_record_number }}</span>
                                    <span>NIK: {{ $reg->patient->nik }}</span>
                                    <span>Usia: {{ \Carbon\Carbon::parse($reg->patient->birth_date)->age }} thn ({{ $reg->patient->gender === 'male' ? 'L' : 'P' }})</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $reg->status === 'in_consultation' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $reg->status === 'in_consultation' ? 'Sedang Diperiksa' : 'Menunggu Panggilan' }}
                            </span>
                            <a href="{{ route('doctor.examine', $reg) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>{{ $reg->status === 'in_consultation' ? 'Lanjutkan Pemeriksaan' : 'Panggil & Periksa RME' }}</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-slate-600">Tidak ada antrean pasien saat ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Pasien baru yang didaftarkan oleh Front Office akan langsung muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Completed Today Section -->
        @if($completedToday->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Pasien yang Selesai Diperiksa Hari Ini</h3>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $completedToday->count() }} Pasien Selesai</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($completedToday as $comp)
                        <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/50">
                            <div>
                                <span class="font-semibold text-slate-800 text-sm">{{ $comp->patient->name }}</span>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Diagnosa: <span class="text-slate-700 font-medium">{{ $comp->medicalRecord?->diagnosis_name ?? '-' }} (ICD-10: {{ $comp->medicalRecord?->icd10_code ?? '-' }})</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ ucfirst(str_replace('_', ' ', $comp->status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
