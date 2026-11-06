<x-app-layout title="Inventori Obat - SIMRS Medika">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('pharmacy.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 mb-2">
                    &larr; Kembali ke Antrean Resep
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Inventori & Manajemen Stok Obat</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar master obat-obatan, ketersediaan stok fisik, dan harga jual.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Kode Obat</th>
                            <th class="px-6 py-3.5">Nama Obat</th>
                            <th class="px-6 py-3.5">Satuan</th>
                            <th class="px-6 py-3.5">Sisa Stok</th>
                            <th class="px-6 py-3.5">Harga Satuan</th>
                            <th class="px-6 py-3.5 text-right">Tambah Stok (Restock)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($medicines as $med)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $med->code }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $med->name }}</td>
                                <td class="px-6 py-4">{{ $med->unit }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                        {{ $med->stock < 50 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $med->stock }} {{ $med->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-semibold text-slate-900">
                                    Rp {{ number_format($med->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('pharmacy.medicines.stock', $med) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="added_stock" min="1" value="50" required
                                            class="w-20 px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-mono text-center focus:ring-2 focus:ring-teal-500">
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-medium transition">
                                            + Tambah
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($medicines->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $medicines->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
