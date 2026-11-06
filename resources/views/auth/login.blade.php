<x-guest-layout title="Masuk - SIMRS MVP">
    <div class="sm:mx-auto sm:w-full sm:max-w-md" x-data="{
        fillCredentials(email) {
            $refs.emailInput.value = email;
            $refs.passwordInput.value = 'password';
        }
    }">
        <!-- Hospital Branding Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-teal-600 text-white shadow-lg shadow-teal-500/30 mb-4">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">SIMRS Medika</h2>
            <p class="mt-2 text-sm text-slate-600">
                Sistem Informasi Manajemen Rumah Sakit Terpadu
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-100">
                <!-- Status Notifications -->
                @if (session('status'))
                    <div class="mb-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700 border border-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg bg-rose-50 p-4 text-sm text-rose-700 border border-rose-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-5" action="{{ route('login') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Alamat Email Staf</label>
                        <div class="mt-1">
                            <input x-ref="emailInput" id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                                placeholder="nama@simrs.test">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-teal-600 hover:text-teal-500">
                                Lupa password?
                            </a>
                        </div>
                        <div class="mt-1">
                            <input x-ref="passwordInput" id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-slate-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-slate-700">Ingat saya di perangkat ini</label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150">
                            Masuk ke Sistem
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-slate-400 text-xs uppercase font-medium">atau masuk dengan</span>
                        </div>
                    </div>

                    <!-- Google OAuth 2.0 Button -->
                    <div class="mt-4">
                        <a href="{{ route('auth.google') }}"
                            class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-slate-200 rounded-xl shadow-xs bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition duration-150">
                            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.27-2.09 3.67-5.17 3.67-9.15z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.27v3.15C3.25 21.31 7.35 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.27 14.24c-.25-.72-.38-1.49-.38-2.24s.13-1.52.38-2.24V6.61H1.27C.46 8.23 0 10.06 0 12s.46 3.77 1.27 5.39l4-3.15z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.25 2.69 1.27 6.61l4 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/>
                            </svg>
                            <span>Google Single Sign-On (Staf Medis)</span>
                        </a>
                        <p class="mt-2 text-center text-xs text-slate-400">
                            *Hanya staf internal yang emailnya telah didaftarkan oleh Administrator.
                        </p>
                    </div>
                </div>

                <!-- Quick Demo Login Switcher (Ideal for Recruiter / Reviewer) -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 text-center">
                        ⚡ Quick Demo Login (1-Klik Isi Form)
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button type="button" @click="fillCredentials('admin@simrs.test')"
                            class="p-2 text-left rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 transition">
                            <span class="font-bold text-slate-800 block">Admin / FO</span>
                            <span class="text-slate-500 text-[11px]">admin@simrs.test</span>
                        </button>
                        <button type="button" @click="fillCredentials('dokter@simrs.test')"
                            class="p-2 text-left rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 transition">
                            <span class="font-bold text-slate-800 block">Dokter Poli</span>
                            <span class="text-slate-500 text-[11px]">dokter@simrs.test</span>
                        </button>
                        <button type="button" @click="fillCredentials('farmasi@simrs.test')"
                            class="p-2 text-left rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 transition">
                            <span class="font-bold text-slate-800 block">Apoteker</span>
                            <span class="text-slate-500 text-[11px]">farmasi@simrs.test</span>
                        </button>
                        <button type="button" @click="fillCredentials('kasir@simrs.test')"
                            class="p-2 text-left rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 transition">
                            <span class="font-bold text-slate-800 block">Kasir Billing</span>
                            <span class="text-slate-500 text-[11px]">kasir@simrs.test</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-center text-slate-400 mt-2">Password default: <code class="text-teal-600 font-mono">password</code></p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
