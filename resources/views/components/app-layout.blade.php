@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIMRS Medika - Sistem Informasi Manajemen Rumah Sakit' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800">
    <div class="min-h-full">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-lg font-bold tracking-tight text-slate-900 block leading-tight">SIMRS Medika</span>
                                    <span class="text-[11px] text-slate-500 font-medium block">Hospital Management System</span>
                                </div>
                            </a>
                        </div>

                        <!-- Role-based Navigation Links -->
                        <div class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                Dashboard
                            </a>

                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('patients.index') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('patients.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Data Pasien
                                </a>
                                <a href="{{ route('registrations.index') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('registrations.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Pendaftaran Kunjungan
                                </a>
                            @endif

                            @if(auth()->user()->hasRole('doctor'))
                                <a href="{{ route('doctor.queue') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('doctor.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Antrean Pemeriksaan Poli
                                </a>
                            @endif

                            @if(auth()->user()->hasRole('pharmacist'))
                                <a href="{{ route('pharmacy.index') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pharmacy.index', 'pharmacy.show') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Antrean Resep
                                </a>
                                <a href="{{ route('pharmacy.medicines') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pharmacy.medicines') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Inventori Obat
                                </a>
                            @endif

                            @if(auth()->user()->hasRole('cashier'))
                                <a href="{{ route('cashier.index') }}"
                                    class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('cashier.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Billing & Kasir
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- User Profile & Session Controls -->
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                            <div class="flex items-center justify-end gap-1.5 mt-0.5">
                                @foreach(auth()->user()->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($role === 'admin') bg-purple-100 text-purple-700
                                        @elseif($role === 'doctor') bg-blue-100 text-blue-700
                                        @elseif($role === 'pharmacist') bg-emerald-100 text-emerald-700
                                        @elseif($role === 'cashier') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Canvas -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 border border-emerald-200 flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 border border-rose-200">
                        <div class="font-semibold mb-1">Perhatian: Terjadi kesalahan input:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
