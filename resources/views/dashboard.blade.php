<x-app-layout title="Dashboard - SIMRS Medika">
    <div class="space-y-6">
        <!-- Top Welcome Banner -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Selamat Datang, {{ $user->name }} 👋
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Anda masuk ke modul 
                    @if($user->hasRole('admin'))
                        <span class="font-semibold text-purple-700">Administrator / Front Office</span>
                    @elseif($user->hasRole('doctor'))
                        <span class="font-semibold text-blue-700">Dokter Spesialis / Rekam Medis Elektronik</span>
                    @elseif($user->hasRole('pharmacist'))
                        <span class="font-semibold text-emerald-700">Farmasi & Instalasi Obat</span>
                    @elseif($user->hasRole('cashier'))
                        <span class="font-semibold text-amber-700">Kasir Pembayaran & Billing</span>
                    @endif
                    — Rumah Sakit Medika.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                    Sistem Aktif (Live)
                </span>
                <span class="text-xs text-slate-400 font-mono">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        <!-- ======================= ADMIN VIEW ======================= -->
        @if ($user->hasRole('admin') && isset($stats['admin']))
            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pasien Terdaftar</span>
                    <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['admin']['total_patients'] }}</p>
                    <span class="text-xs text-teal-600 font-medium mt-1 inline-block">Database Rekam Medis</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dokter Spesialis</span>
                    <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['admin']['total_doctors'] }}</p>
                    <span class="text-xs text-blue-600 font-medium mt-1 inline-block">Praktik Aktif</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Poliklinik Tersedia</span>
                    <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['admin']['total_polyclinics'] }}</p>
                    <span class="text-xs text-purple-600 font-medium mt-1 inline-block">Unit Rawat Jalan</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kunjungan Hari Ini</span>
                    <p class="text-3xl font-extrabold text-teal-700 mt-2">{{ $stats['admin']['today_registrations'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Antrean Kunjungan</span>
                </div>
            </div>

            <!-- Recent Registrations Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Aktivitas Kunjungan Pasien Terakhir</h3>
                    <span class="text-xs text-slate-400">5 Pendaftaran Terkini</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3">No. Registrasi</th>
                                <th class="px-6 py-3">No. RM & Nama Pasien</th>
                                <th class="px-6 py-3">Poliklinik & Dokter</th>
                                <th class="px-6 py-3">No. Antrean</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($stats['admin']['recent_registrations'] as $reg)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $reg->registration_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $reg->patient->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $reg->patient->medical_record_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800">{{ $reg->polyclinic->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $reg->doctor->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-teal-600">#{{ $reg->queue_number }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            @if($reg->status === 'completed') bg-emerald-100 text-emerald-800
                                            @elseif($reg->status === 'waiting_pharmacy') bg-amber-100 text-amber-800
                                            @elseif($reg->status === 'waiting_payment') bg-blue-100 text-blue-800
                                            @else bg-slate-100 text-slate-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada kunjungan yang terdaftar hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ======================= DOCTOR VIEW ======================= -->
        @if ($user->hasRole('doctor') && isset($stats['doctor']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs md:col-span-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Informasi Praktik</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-2">{{ $stats['doctor']['profile']?->specialization ?? 'Dokter Spesialis' }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Poli: <span class="font-semibold text-slate-800">{{ $stats['doctor']['profile']?->polyclinic?->name ?? 'Umum' }}</span> (Ruang {{ $stats['doctor']['profile']?->polyclinic?->room_number ?? '-' }})</p>
                    <p class="text-xs text-slate-500 mt-0.5">SIP: <span class="font-mono text-slate-700">{{ $stats['doctor']['profile']?->sip_number ?? '-' }}</span></p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pasien Dalam Antrean Hari Ini</span>
                    <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $stats['doctor']['waiting_patients'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Menunggu Pemeriksaan</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pasien Selesai Diperiksa</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['doctor']['examined_today'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">RME Terisi Hari Ini</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Daftar Antrean Pasien Poli</h3>
                    <span class="text-xs text-slate-400">Panggilan Konsultasi RME</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($stats['doctor']['queue_list'] as $queue)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-bold flex items-center justify-center text-sm border border-blue-200">
                                    #{{ $queue->queue_number }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $queue->patient->name }}</h4>
                                    <span class="text-xs text-slate-500 font-mono">No. RM: {{ $queue->patient->medical_record_number }} • NIK: {{ $queue->patient->nik }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-400 text-sm">
                            Tidak ada pasien yang mengantre di poli Anda saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- ======================= PHARMACIST VIEW ======================= -->
        @if ($user->hasRole('pharmacist') && isset($stats['pharmacist']))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Resep Menunggu Validasi</span>
                    <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['pharmacist']['pending_prescriptions'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Antrean Farmasi Masuk</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Resep Diserahkan Hari Ini</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['pharmacist']['dispensed_today'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Stok Obat Terpotong Atomik</span>
                </div>
            </div>

            <!-- Low Stock Warning Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Peringatan Stok Obat Menipis (&lt; 50 Unit)</h3>
                    <span class="text-xs text-rose-500 font-semibold">Monitoring Inventori</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Kode Obat</th>
                                <th class="px-6 py-3">Nama Obat</th>
                                <th class="px-6 py-3">Satuan</th>
                                <th class="px-6 py-3">Sisa Stok</th>
                                <th class="px-6 py-3">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($stats['pharmacist']['low_stock_medicines'] as $med)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $med->code }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $med->name }}</td>
                                    <td class="px-6 py-4">{{ $med->unit }}</td>
                                    <td class="px-6 py-4 font-bold text-rose-600">{{ $med->stock }}</td>
                                    <td class="px-6 py-4 font-mono">Rp {{ number_format($med->price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-emerald-600 text-sm">
                                        ✓ Semua stok obat berada di atas batas aman minimum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ======================= CASHIER VIEW ======================= -->
        @if ($user->hasRole('cashier') && isset($stats['cashier']))
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tagihan Belum Lunas</span>
                    <p class="text-3xl font-extrabold text-rose-600 mt-2">{{ $stats['cashier']['unpaid_invoices'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Menunggu Pembayaran Kasir</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transaksi Lunas Hari Ini</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['cashier']['paid_today_count'] }}</p>
                    <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Kwitansi Tercetak</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Kas Terkumpul</span>
                    <p class="text-2xl font-extrabold text-teal-700 mt-2">
                        Rp {{ number_format($stats['cashier']['total_revenue_today'], 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-teal-600 font-medium mt-1 inline-block">Kas Tunai Hari Ini</span>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
