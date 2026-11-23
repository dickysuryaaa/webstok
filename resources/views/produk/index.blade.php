<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk</h2>
            @if ($pemilik)
                <a href="{{ route('produk.create') }}"
                   class="inline-flex items-center justify-center min-h-[44px] px-4 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Tambah Produk
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 rounded-md bg-green-50 text-green-800 text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 rounded-md bg-red-50 text-red-800 text-sm">{{ session('error') }}</div>
            @endif

            <form method="GET" action="{{ route('produk.index') }}" class="flex gap-2">
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama produk"
                       class="flex-1 min-w-0 min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <button type="submit"
                        class="min-h-[44px] px-4 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Cari
                </button>
            </form>

            @php
                $badge = function ($stok) use ($ambang) {
                    if ($stok === 0) {
                        return ['Habis', 'bg-red-100 text-red-800'];
                    }
                    if ($stok <= $ambang) {
                        return ['Stok menipis', 'bg-yellow-100 text-yellow-800'];
                    }
                    return null;
                };
            @endphp

            @if ($produk->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600 text-sm">
                    Tidak ada produk{{ $q !== '' ? ' yang cocok dengan "'.$q.'"' : '' }}.
                </div>
            @else
                {{-- Kartu: layar sempit --}}
                <div class="space-y-3 md:hidden">
                    @foreach ($produk as $p)
                        @php $b = $badge($p->stok); @endphp
                        <div class="bg-white shadow-sm rounded-lg p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-semibold text-gray-900 break-words">{{ $p->nama_produk }}</div>
                                @if ($b)
                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium {{ $b[1] }}">{{ $b[0] }}</span>
                                @endif
                            </div>
                            <dl class="mt-2 grid grid-cols-2 gap-y-1 text-sm">
                                <dt class="text-gray-500">Stok</dt>
                                <dd class="text-right text-gray-900">{{ $p->stok }}</dd>
                                <dt class="text-gray-500">Harga jual</dt>
                                <dd class="text-right text-gray-900">{{ \App\Support\Rupiah::format($p->harga_jual) }}</dd>
                                @if ($pemilik)
                                    <dt class="text-gray-500">Harga beli</dt>
                                    <dd class="text-right text-gray-900">{{ \App\Support\Rupiah::format($p->harga_beli) }}</dd>
                                    <dt class="text-gray-500">Keuntungan/unit</dt>
                                    <dd class="text-right text-gray-900">{{ \App\Support\Rupiah::format($p->keuntungan_per_unit) }}</dd>
                                @endif
                            </dl>
                            @if ($pemilik)
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('produk.edit', $p) }}"
                                       class="flex-1 inline-flex items-center justify-center min-h-[44px] border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Ubah</a>
                                    <form method="POST" action="{{ route('produk.destroy', $p) }}" class="flex-1"
                                          onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full min-h-[44px] border border-red-300 rounded-md text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Tabel: layar lebar --}}
                <div class="hidden md:block bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3 text-right">Stok</th>
                                <th class="px-4 py-3">Status</th>
                                @if ($pemilik)
                                    <th class="px-4 py-3 text-right">Harga beli</th>
                                @endif
                                <th class="px-4 py-3 text-right">Harga jual</th>
                                @if ($pemilik)
                                    <th class="px-4 py-3 text-right">Keuntungan/unit</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($produk as $p)
                                @php $b = $badge($p->stok); @endphp
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $p->nama_produk }}</td>
                                    <td class="px-4 py-3 text-right">{{ $p->stok }}</td>
                                    <td class="px-4 py-3">
                                        @if ($b)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $b[1] }}">{{ $b[0] }}</span>
                                        @endif
                                    </td>
                                    @if ($pemilik)
                                        <td class="px-4 py-3 text-right">{{ \App\Support\Rupiah::format($p->harga_beli) }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-right">{{ \App\Support\Rupiah::format($p->harga_jual) }}</td>
                                    @if ($pemilik)
                                        <td class="px-4 py-3 text-right">{{ \App\Support\Rupiah::format($p->keuntungan_per_unit) }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('produk.edit', $p) }}"
                                                   class="inline-flex items-center min-h-[44px] px-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Ubah</a>
                                                <form method="POST" action="{{ route('produk.destroy', $p) }}"
                                                      onsubmit="return confirm('Hapus produk ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="min-h-[44px] px-3 border border-red-300 rounded-md text-red-700 hover:bg-red-50">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>{{ $produk->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
