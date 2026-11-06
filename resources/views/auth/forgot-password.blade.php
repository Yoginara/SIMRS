<x-guest-layout title="Lupa Password - SIMRS MVP">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Pemulihan Password</h2>
            <p class="mt-2 text-sm text-slate-600">
                Masukkan email staf yang terdaftar untuk menerima tautan reset password.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-100">
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

                <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Staf Terdaftar</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                                placeholder="nama@simrs.test">
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150">
                            Kirim Tautan Reset Password
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                            &larr; Kembali ke halaman login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
