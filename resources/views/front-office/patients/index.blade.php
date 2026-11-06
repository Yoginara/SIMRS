<x-app-layout title="Data Pasien - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Database Pasien Rumah Sakit</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data identitas dan nomor rekam medis pasien.</p>
            </div>
            <a href="{{ route('patients.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Daftar Pasien Baru</span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs">
            <form action="{{ route('patients.index') }}" method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari berdasarkan Nama Pasien, No. RM (misal: RM-202611-0001), atau NIK 16 digit..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                <button type="submit"
                    class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium rounded-xl transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Patients Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">No. Rekam Medis</th>
                            <th class="px-6 py-3.5">NIK (Kependudukan)</th>
                            <th class="px-6 py-3.5">Nama Pasien</th>
                            <th class="px-6 py-3.5">Tanggal Lahir / JK</th>
                            <th class="px-6 py-3.5">No. Telepon</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patients as $patient)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 font-mono font-bold text-teal-700">{{ $patient->medical_record_number }}</td>
                                <td class="px-6 py-4 font-mono text-slate-600">{{ $patient->nik }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $patient->name }}</td>
                                <td class="px-6 py-4">
                                    <div>{{ $patient->birth_date->format('d/m/Y') }}</div>
                                    <span class="text-xs text-slate-400 font-medium capitalize">{{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">{{ $patient->phone }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('registrations.create', ['patient_id' => $patient->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold rounded-lg border border-teal-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span>Daftarkan ke Poli</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    Tidak ada data pasien yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($patients->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
