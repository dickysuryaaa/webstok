@use('App\Support\Rupiah')

@php
    $filter = ['' => 'Semua', 'menipis' => 'Stok Menipis', 'habis' => 'Habis'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Produk</h2>
                <p class="mt-0.5 text-sm text-slate-500 tabular-nums">{{ $produk->total() }} produk</p>
            </div>
            @if ($pemilik)
                <a href="{{ route('produk.create') }}"
                   class="inline-flex shrink-0 items-center justify-center gap-1.5 min-h-[44px] px-4 rounded-lg bg-emerald-600 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                    <x-icon name="plus" class="h-5 w-5" />
                    Tambah Produk
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6"
         @if ($pemilik)
             x-data="{ hapus: { buka: false, url: '', nama: '' }, konfirmasi(url, nama) { this.hapus = { buka: true, url, nama }; } }"
             @keydown.escape.window="hapus.buka = false"
         @endif>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            {{-- Pencarian dan filter --}}
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <form method="GET" action="{{ route('produk.index') }}" class="flex flex-1 gap-2 min-w-0"
                      x-data="{ q: @js($q) }">
                    @if ($status !== '')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="relative flex-1 min-w-0">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                        <input type="search" name="q" x-model="q" value="{{ $q }}" placeholder="Cari nama produk"
                               class="block w-full min-h-[44px] ps-10 pe-11 border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm [&::-webkit-search-cancel-button]:hidden">
                        <button type="button" x-show="q" x-cloak aria-label="Hapus kata kunci"
                                @click="q = ''; $nextTick(() => {{ $q !== '' ? '$el.closest(`form`).submit()' : '$el.previousElementSibling.focus()' }})"
                                class="absolute right-0 top-0 inline-flex h-11 w-11 items-center justify-center text-xl text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    <button type="submit"
                            class="inline-flex shrink-0 items-center justify-center min-h-[44px] px-4 rounded-lg bg-slate-800 text-sm font-semibold text-white hover:bg-slate-700">
                        Cari
                    </button>
                </form>

                <div class="grid grid-cols-3 gap-1 rounded-xl bg-slate-200/60 p-1 md:flex" role="group" aria-label="Filter status stok">
                    @foreach ($filter as $kunci => $label)
                        @php $aktif = $status === $kunci; @endphp
                        <a href="{{ route('produk.index', array_filter(['q' => $q, 'status' => $kunci])) }}"
                           @if ($aktif) aria-current="true" @endif
                           class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-2 sm:px-3 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap transition {{ $aktif ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            @if ($kunci === 'menipis')
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            @elseif ($kunci === 'habis')
                                <span class="h-2 w-2 rounded-full bg-rose-600"></span>
                            @endif
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($produk->isEmpty())
                <x-card class="p-8 flex flex-col items-center gap-2 text-center text-sm text-slate-500">
                    <x-icon name="box" class="h-8 w-8 text-slate-300" />
                    Tidak ada produk{{ $q !== '' ? ' yang cocok dengan "'.$q.'"' : '' }}.
                </x-card>
            @else
                {{-- Kartu: layar sempit --}}
                <ul class="space-y-3 md:hidden">
                    @foreach ($produk as $p)
                        <li>
                            <x-card class="p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 font-semibold text-slate-900 break-words">{{ $p->nama_produk }}</div>
                                    <x-stok-badge :stok="$p->stok" :ambang="$ambang" />
                                </div>
                                <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                    <div class="rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-xs text-slate-500">Stok</dt>
                                        <dd class="font-semibold tabular-nums {{ $p->stok === 0 ? 'text-rose-600' : ($p->stok <= $ambang ? 'text-amber-700' : 'text-slate-900') }}">{{ $p->stok }} unit</dd>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-xs text-slate-500">Harga jual</dt>
                                        <dd class="font-semibold text-slate-900 tabular-nums">{{ Rupiah::format($p->harga_jual) }}</dd>
                                    </div>
                                    @if ($pemilik)
                                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                                            <dt class="text-xs text-slate-500">Harga beli</dt>
                                            <dd class="font-medium text-slate-700 tabular-nums">{{ Rupiah::format($p->harga_beli) }}</dd>
                                        </div>
                                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                                            <dt class="text-xs text-slate-500">Keuntungan/unit</dt>
                                            <dd class="font-medium tabular-nums {{ $p->keuntungan_per_unit < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ Rupiah::format($p->keuntungan_per_unit) }}</dd>
                                        </div>
                                    @endif
                                </dl>
                                <a href="{{ route('produk.gambar.edit', $p) }}"
                                    class="mt-3 inline-flex items-center justify-center min-h-[40px] px-3 rounded-lg border border-slate-300 text-sm  font-medium text-slate-700 hover:bg-slate-50">
                                    Kelola Gambar
                                </a>
                                @if ($pemilik)
                                    <div class="mt-3 grid grid-cols-2 gap-2">
                                        <a href="{{ route('produk.edit', $p) }}"
                                           class="inline-flex items-center justify-center gap-1.5 min-h-[44px] rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                            <x-icon name="pencil" class="h-4 w-4" /> Ubah
                                        </a>
                                        <button type="button" @click="konfirmasi(@js(route('produk.destroy', $p)), @js($p->nama_produk))"
                                                class="inline-flex items-center justify-center gap-1.5 min-h-[44px] rounded-lg border border-rose-200 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                                            <x-icon name="trash" class="h-4 w-4" /> Hapus
                                        </button>
                                    </div>
                                @endif
                            </x-card>
                        </li>
                    @endforeach
                </ul>

                {{-- Tabel: layar lebar --}}
                <x-card class="hidden md:block overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
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
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($produk as $p)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900">
                                                {{ $p->nama_produk }}
                                            </div>

                                            <a href="{{ route('produk.gambar.edit', $p) }}"
                                                class="mt-1 inline-block text-xs font-medium text-emerald-700 hover:text-emerald-800">
                                                Kelola Gambar
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $p->stok === 0 ? 'text-rose-600' : ($p->stok <= $ambang ? 'text-amber-700' : 'text-slate-900') }}">{{ $p->stok }}</td>
                                        <td class="px-4 py-3"><x-stok-badge :stok="$p->stok" :ambang="$ambang" /></td>
                                        @if ($pemilik)
                                            <td class="px-4 py-3 text-right tabular-nums text-slate-600">{{ Rupiah::format($p->harga_beli) }}</td>
                                        @endif
                                        <td class="px-4 py-3 text-right tabular-nums font-semibold text-slate-900">{{ Rupiah::format($p->harga_jual) }}</td>
                                        @if ($pemilik)
                                            <td class="px-4 py-3 text-right tabular-nums {{ $p->keuntungan_per_unit < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ Rupiah::format($p->keuntungan_per_unit) }}</td>
                                            <td class="px-4 py-2">
                                                <div class="flex justify-end gap-1">
                                                    <a href="{{ route('produk.edit', $p) }}" title="Ubah" aria-label="Ubah {{ $p->nama_produk }}"
                                                       class="inline-flex h-11 items-center justify-center gap-1.5 px-3 rounded-lg text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                                        <x-icon name="pencil" class="h-4 w-4" /> Ubah
                                                    </a>
                                                    <button type="button" title="Hapus" aria-label="Hapus {{ $p->nama_produk }}"
                                                            @click="konfirmasi(@js(route('produk.destroy', $p)), @js($p->nama_produk))"
                                                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                                                        <x-icon name="trash" />
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>

                <div>{{ $produk->links() }}</div>
            @endif
        </div>

        {{-- Modal konfirmasi hapus --}}
        @if ($pemilik)
            <div x-show="hapus.buka" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                 role="dialog" aria-modal="true" aria-labelledby="judul-hapus">
                <div x-show="hapus.buka" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="hapus.buka = false"></div>
                <div x-show="hapus.buka"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                            <x-icon name="trash" class="h-6 w-6" />
                        </span>
                        <div class="min-w-0">
                            <h3 id="judul-hapus" class="text-base font-semibold text-slate-900">Hapus produk?</h3>
                            <p class="mt-1 text-sm text-slate-600 break-words">
                                Produk <span class="font-semibold text-slate-900" x-text="'&quot;' + hapus.nama + '&quot;'"></span> akan dihapus permanen.
                                Produk yang sudah memiliki riwayat penjualan tidak dapat dihapus.
                            </p>
                        </div>
                    </div>
                    <form method="POST" :action="hapus.url" class="mt-5 grid grid-cols-2 gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="hapus.buka = false"
                                class="inline-flex items-center justify-center min-h-[44px] rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 min-h-[44px] rounded-lg bg-rose-600 text-sm font-semibold text-white hover:bg-rose-700">
                            <x-icon name="trash" class="h-4 w-4" /> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
