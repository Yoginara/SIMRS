<x-app-layout title="Daftar Antrean Poli - SIMRS Medika">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('registrations.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Daftar Antrean
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Pasien ke Antrean Poliklinik</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih pasien, poli tujuan, dan dokter spesialis yang bertugas hari ini.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
            <form action="{{ route('registrations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="patient_id" class="block text-sm font-semibold text-slate-700">Pilih Pasien Terdaftar *</label>
                    <select id="patient_id" name="patient_id" required
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (string)$patient->id === (string)$selectedPatientId ? 'selected' : '' }}>
                                [{{ $patient->medical_record_number }}] {{ $patient->name }} - NIK: {{ $patient->nik }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">
                        Pasien belum terdaftar? <a href="{{ route('patients.create') }}" class="text-teal-600 font-semibold hover:underline">Tambah pasien baru disini</a>.
                    </p>
                </div>

                <div>
                    <label for="polyclinic_id" class="block text-sm font-semibold text-slate-700">Poliklinik Tujuan *</label>
                    <select id="polyclinic_id" name="polyclinic_id" required
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                        <option value="">-- Pilih Poliklinik --</option>
                        @foreach($polyclinics as $poli)
                            <option value="{{ $poli->id }}">
                                {{ $poli->name }} (Ruang {{ $poli->room_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="doctor_id" class="block text-sm font-semibold text-slate-700">Dokter yang Bertugas *</label>
                    <select id="doctor_id" name="doctor_id" required
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                        <option value="">-- Pilih Dokter Spesialis --</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}">
                                {{ $doc->user->name }} — {{ $doc->specialization }} (Tarif: Rp {{ number_format($doc->consultation_fee, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('registrations.index') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-md transition">
                        Konfirmasi Pendaftaran & Buat Antrean
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
