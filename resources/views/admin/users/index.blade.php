<x-app-layout title="Kelola Staf & Akun Pengguna - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Staf & Hak Akses (RBAC)</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftarkan email staf resmi agar dapat login menggunakan Email/Password maupun Google SSO (*Whitelist*).
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>+ Daftarkan Staf Baru</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Nama Staf</th>
                            <th class="px-6 py-3.5">Email Terdaftar</th>
                            <th class="px-6 py-3.5">Peran (Role)</th>
                            <th class="px-6 py-3.5">Status Akun</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                        <span class="ml-1 text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-normal">(Anda)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-700">
                                    {{ $u->email }}
                                    @if($u->google_id)
                                        <span class="inline-flex items-center text-[10px] text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded ml-1 font-sans">
                                            ✓ Google Linked
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($u->getRoleNames() as $r)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                                            @if($r === 'admin') bg-purple-100 text-purple-700
                                            @elseif($r === 'doctor') bg-blue-100 text-blue-700
                                            @elseif($r === 'pharmacist') bg-emerald-100 text-emerald-700
                                            @elseif($r === 'cashier') bg-amber-100 text-amber-700
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $r }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($u->id !== auth()->id())
                                        <div class="inline-flex items-center gap-2">
                                            <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition
                                                        {{ $u->is_active ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                                    {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} ({{ $u->email }}) secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Akun aktif saat ini</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
