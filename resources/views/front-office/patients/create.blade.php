<x-app-layout title="Daftar Pasien Baru - SIMRS Medika">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Daftar Pasien
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Registrasi Pasien Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi data identitas pasien sesuai KTP/Identitas resmi.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
            <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Auto-generated RM Number Notice -->
                <div class="p-4 bg-teal-50/70 border border-teal-200/60 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-teal-800 uppercase tracking-wider block">No. Rekam Medis (Auto-Generated)</span>
                        <span class="text-lg font-mono font-bold text-teal-900">{{ $generatedRmNumber }}</span>
                    </div>
                    <span class="text-xs text-teal-700 bg-white px-2.5 py-1 rounded-full font-medium border border-teal-200">
                        Format Standar Medis
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-slate-700">NIK (16 Digit KTP) *</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required maxlength="16"
                            placeholder="Contoh: 327301xxxxxxxxxx"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl font-mono text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap Pasien *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Ahmad Fauzi"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-semibold text-slate-700">Tanggal Lahir *</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-semibold text-slate-700">Jenis Kelamin *</label>
                        <select id="gender" name="gender" required
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                            placeholder="081234567890"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="blood_type" class="block text-sm font-semibold text-slate-700">Golongan Darah</label>
                        <select id="blood_type" name="blood_type"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="">-- Pilih (Opsional) --</option>
                            <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-700">Alamat Tempat Tinggal *</label>
                    <textarea id="address" name="address" rows="3" required
                        placeholder="Alamat lengkap domisili pasien..."
                        class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('patients.index') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-md transition">
                        Simpan Pasien Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
