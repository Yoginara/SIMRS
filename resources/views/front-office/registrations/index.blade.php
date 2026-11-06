<x-app-layout title="Pendaftaran Kunjungan - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Antrean Rawat Jalan Hari Ini</h1>
                <p class="text-sm text-slate-500 mt-1">Alur pendaftaran pasien ke Poliklinik & Dokter Spesialis.</p>
            </div>
            <a href="{{ route('registrations.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Daftarkan Pasien ke Antrean Poli</span>
            </a>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <a href="{{ route('registrations.index') }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ !$status ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Semua Antrean
            </a>
            <a href="{{ route('registrations.index', ['status' => 'registered']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $status === 'registered' ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Menunggu Dokter
            </a>
            <a href="{{ route('registrations.index', ['status' => 'in_consultation']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $status === 'in_consultation' ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Sedang Diperiksa
            </a>
            <a href="{{ route('registrations.index', ['status' => 'waiting_pharmacy']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $status === 'waiting_pharmacy' ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Di Farmasi
            </a>
            <a href="{{ route('registrations.index', ['status' => 'waiting_payment']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $status === 'waiting_payment' ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Di Kasir
            </a>
            <a href="{{ route('registrations.index', ['status' => 'completed']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $status === 'completed' ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Selesai
            </a>
        </div>

        <!-- Registrations Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">No. Antrean</th>
                            <th class="px-6 py-3.5">No. Registrasi</th>
                            <th class="px-6 py-3.5">Nama Pasien & No. RM</th>
                            <th class="px-6 py-3.5">Poli & Dokter</th>
                            <th class="px-6 py-3.5">Waktu Daftar</th>
                            <th class="px-6 py-3.5">Status Alur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($registrations as $reg)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 font-bold text-lg text-teal-600">
                                    #{{ $reg->queue_number }}
                                </td>
                                <td class="px-6 py-4 font-mono font-medium text-slate-800">
                                    {{ $reg->registration_number }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $reg->patient->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $reg->patient->medical_record_number }} • NIK: {{ $reg->patient->nik }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $reg->polyclinic->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $reg->doctor->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ $reg->registered_at->format('H:i:s') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        @if($reg->status === 'completed') bg-emerald-100 text-emerald-800
                                        @elseif($reg->status === 'waiting_pharmacy') bg-amber-100 text-amber-800
                                        @elseif($reg->status === 'waiting_payment') bg-blue-100 text-blue-800
                                        @elseif($reg->status === 'in_consultation') bg-purple-100 text-purple-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada antrean pendaftaran pada kategori ini hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registrations->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
