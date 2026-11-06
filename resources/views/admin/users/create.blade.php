<x-app-layout title="Daftarkan Staf Baru - SIMRS Medika">
    <div class="max-w-2xl mx-auto space-y-6" x-data="{ role: '{{ old('role', 'doctor') }}' }">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                &larr; Kembali ke Daftar Staf
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftarkan Akun Staf Rumah Sakit</h1>
            <p class="text-sm text-slate-500 mt-1">
                Masukkan email staf (bisa email Google Gmail staf untuk mengizinkan login Google SSO).
            </p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic User Info -->
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap & Gelar Staf *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: dr. Ahmad Subagyo, Sp.A atau Siti Aminah, S.Farm."
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email Staf (Gmail / Email Resmi) *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            placeholder="nama.staf@gmail.com"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <p class="text-xs text-slate-400 mt-1">
                            *Jika diisi alamat Gmail staf, staf tersebut dapat langsung login menggunakan tombol <strong>Google SSO</strong> di halaman login.
                        </p>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-700">Hak Akses / Peran (Role) *</label>
                        <select id="role" name="role" x-model="role" required
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="doctor">Dokter (Pemeriksaan RME & E-Resep)</option>
                            <option value="pharmacist">Farmasi (Validasi Resep & Stok Obat)</option>
                            <option value="cashier">Kasir (Billing & Pembayaran)</option>
                            <option value="admin">Administrator (Front Office & Manajemen Sistem)</option>
                        </select>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password Manual (Opsional)</label>
                        <input type="password" id="password" name="password"
                            placeholder="Kosongkan jika hanya login Google SSO (default: 'password')"
                            class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <!-- Conditional Doctor Profile Fields (Visible when role === 'doctor') -->
                <div x-show="role === 'doctor'" x-transition class="pt-4 border-t border-slate-100 space-y-4">
                    <h4 class="text-xs font-bold text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Informasi Praktik Dokter Spesialis</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="polyclinic_id" class="block text-xs font-semibold text-slate-600">Poliklinik Bertugas *</label>
                            <select id="polyclinic_id" name="polyclinic_id"
                                class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal-500">
                                @foreach($polyclinics as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Ruang {{ $p->room_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sip_number" class="block text-xs font-semibold text-slate-600">No. SIP (Surat Izin Praktik) *</label>
                            <input type="text" id="sip_number" name="sip_number" value="{{ old('sip_number', 'SIP.503/' . rand(1000, 9999) . '/DINKES/2026') }}"
                                placeholder="SIP.xxx/DINKES/2026"
                                class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="specialization" class="block text-xs font-semibold text-slate-600">Spesialisasi Medis *</label>
                            <input type="text" id="specialization" name="specialization" value="{{ old('specialization', 'Dokter Umum') }}"
                                placeholder="Contoh: Spesialis Anak (Sp.A)"
                                class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="consultation_fee" class="block text-xs font-semibold text-slate-600">Tarif Konsultasi (Rp) *</label>
                            <input type="number" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', 60000) }}"
                                class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-md transition">
                        Simpan & Daftarkan Staf
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
