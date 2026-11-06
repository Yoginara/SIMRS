<x-app-layout title="Pemeriksaan RME Pasien - SIMRS Medika">
    <div class="space-y-6" x-data="{
        prescriptionItems: [],
        medicines: {{ Js::from($medicines) }},
        addPrescriptionItem() {
            this.prescriptionItems.push({
                medicine_id: '',
                quantity: 1,
                dosage_instructions: '3x1 hari sesudah makan',
                selectedMedicine: null
            });
        },
        removePrescriptionItem(index) {
            this.prescriptionItems.splice(index, 1);
        },
        updateMedicine(index, id) {
            const med = this.medicines.find(m => m.id == id);
            this.prescriptionItems[index].selectedMedicine = med || null;
        }
    }">
        <div>
            <a href="{{ route('doctor.queue') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Antrean Poli
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pemeriksaan Rekam Medis Elektronik (RME)</h1>
                    <p class="text-sm text-slate-500 mt-1">Konsultasi Poliklinik & Peresepan Obat Elektronik.</p>
                </div>
                <div class="px-4 py-2 bg-purple-50 border border-purple-200 rounded-xl text-xs font-semibold text-purple-800">
                    No. Antrean: #{{ $registration->queue_number }} ({{ $registration->registration_number }})
                </div>
            </div>
        </div>

        <!-- Patient Header Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Nama Pasien</span>
                    <p class="font-bold text-slate-900 text-base mt-0.5">{{ $registration->patient->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">No. Rekam Medis</span>
                    <p class="font-mono font-bold text-teal-700 text-base mt-0.5">{{ $registration->patient->medical_record_number }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">NIK & Usia</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $registration->patient->nik }} ({{ \Carbon\Carbon::parse($registration->patient->birth_date)->age }} thn)</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Jenis Kelamin / Gol. Darah</span>
                    <p class="font-medium text-slate-800 mt-0.5">
                        {{ $registration->patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} 
                        @if($registration->patient->blood_type) (Gol. {{ $registration->patient->blood_type }}) @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Medical History Preview -->
        @if($medicalHistory->isNotEmpty())
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                    Riwayat Kunjungan Sebelumnya (Rekam Medis Historis)
                </h4>
                <div class="space-y-3">
                    @foreach($medicalHistory as $history)
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200 text-xs">
                            <div class="flex items-center justify-between font-semibold text-slate-800">
                                <span>{{ $history->created_at->format('d M Y') }} — dr. {{ $history->doctor->user->name }}</span>
                                <span class="font-mono text-teal-700">ICD-10: {{ $history->icd10_code ?? '-' }}</span>
                            </div>
                            <p class="text-slate-600 mt-1"><span class="font-medium text-slate-700">Diagnosa:</span> {{ $history->diagnosis_name }}</p>
                            <p class="text-slate-500 mt-0.5"><span class="font-medium text-slate-700">Keluhan:</span> {{ $history->chief_complaint }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Examination Form -->
        <form action="{{ route('doctor.examine.store', $registration) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Vital Signs & Examination -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xs space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                    1. Tanda Vital & Pemeriksaan Fisik
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Sistolik (mmHg)</label>
                        <input type="number" name="systolic" value="{{ old('systolic', 120) }}"
                            class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Diastolik (mmHg)</label>
                        <input type="number" name="diastolic" value="{{ old('diastolic', 80) }}"
                            class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Denyut Nadi (bpm)</label>
                        <input type="number" name="heart_rate" value="{{ old('heart_rate', 80) }}"
                            class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" name="temperature" value="{{ old('temperature', 36.5) }}"
                            class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                    </div>
                </div>

                <div>
                    <label for="chief_complaint" class="block text-sm font-semibold text-slate-700">Anamnesis / Keluhan Utama Pasien *</label>
                    <textarea id="chief_complaint" name="chief_complaint" rows="3" required
                        placeholder="Contoh: Pasien mengeluh pusing berputar, mual dan nyeri lambung sejak 2 hari yang lalu..."
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('chief_complaint') }}</textarea>
                </div>

                <div>
                    <label for="physical_examination" class="block text-sm font-semibold text-slate-700">Pemeriksaan Fisik (Objektif)</label>
                    <textarea id="physical_examination" name="physical_examination" rows="2"
                        placeholder="Contoh: Kesadaran compos mentis, konjungtiva anemis (-), nyeri tekan epigastrium (+)..."
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('physical_examination') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="icd10_code" class="block text-sm font-semibold text-slate-700">Kode ICD-10 (Standar Medis)</label>
                        <input type="text" id="icd10_code" name="icd10_code" value="{{ old('icd10_code', 'K30') }}"
                            placeholder="Contoh: K30, I10, J00"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl font-mono text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="diagnosis_name" class="block text-sm font-semibold text-slate-700">Deskripsi Diagnosa Medis *</label>
                        <input type="text" id="diagnosis_name" name="diagnosis_name" value="{{ old('diagnosis_name', 'Dispepsia / Sakit Maag Akut') }}" required
                            placeholder="Contoh: Dispepsia Sindrom Akut"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="doctor_notes" class="block text-sm font-semibold text-slate-700">Catatan & Anjuran Dokter (Edukasi Pasien)</label>
                    <textarea id="doctor_notes" name="doctor_notes" rows="2"
                        placeholder="Contoh: Hindari makanan pedas dan berlemak, kontrol kembali jika keluhan berlanjut dalam 3 hari..."
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('doctor_notes') }}</textarea>
                </div>
            </div>

            <!-- Section 2: E-Prescription Builder (Alpine.js) -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        2. Peresepan Elektronik (E-Prescription)
                    </h3>
                    <button type="button" @click="addPrescriptionItem()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg border border-emerald-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Tambah Obat</span>
                    </button>
                </div>

                <!-- Empty State -->
                <template x-if="prescriptionItems.length === 0">
                    <div class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-sm">
                        <p>Belum ada obat yang diresepkan. Klik tombol <strong>+ Tambah Obat</strong> jika pasien memerlukan resep obat dari Farmasi.</p>
                        <p class="text-xs text-slate-400 mt-1">(Jika tanpa resep, tagihan langsung dialihkan ke Kasir hanya untuk biaya konsultasi dokter).</p>
                    </div>
                </template>

                <!-- Prescription Rows -->
                <div class="space-y-3">
                    <template x-for="(item, index) in prescriptionItems" :key="index">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Obat</label>
                                <select :name="`prescriptions[${index}][medicine_id]`" required
                                    @change="updateMedicine(index, $event.target.value)"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal-500">
                                    <option value="">-- Pilih Obat --</option>
                                    <template x-for="med in medicines" :key="med.id">
                                        <option :value="med.id" x-text="`${med.name} (Stok: ${med.stock} ${med.unit})`"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah</label>
                                <input type="number" min="1" :name="`prescriptions[${index}][quantity]`" x-model="item.quantity" required
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal-500 font-mono">
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Aturan Pakai / Dosis</label>
                                <input type="text" :name="`prescriptions[${index}][dosage_instructions]`" x-model="item.dosage_instructions" required
                                    placeholder="Contoh: 3x1 sesudah makan"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal-500">
                            </div>

                            <div class="sm:col-span-1 text-right pt-4 sm:pt-0">
                                <button type="button" @click="removePrescriptionItem(index)"
                                    class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Hapus baris obat">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100 shadow-xs">
                <a href="{{ route('doctor.queue') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-bold shadow-md shadow-teal-500/20 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Finalisasi & Kunci Rekam Medis (Kirim ke Farmasi/Kasir)</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
