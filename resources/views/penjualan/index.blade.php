@use('App\Support\Rupiah')

<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Penjualan</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pilih produk, atur jumlah, lalu simpan.</p>
        </div>
    </x-slot>


    @php $ambang = (int) config('toko.stok_menipis', 5); @endphp
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
             x-data="{
                produk: @js($pilihan),
                kataKunci: '',
                selectedId: @js((int) old('produk_id', 0) ?: null),
                jumlah: @js((int) old('jumlah', 1)),
                ambang: {{ $ambang }},
                get terpilih() { return this.produk.find(p => p.id === this.selectedId) ?? null; },
                get hasil() {
                    const k = this.kataKunci.trim().toLowerCase();
                    return this.produk.filter(p => k === '' || p.nama.toLowerCase().includes(k));
                },
                rp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); },
                pilih(p) {
                    if (p.stok === 0) { return; }
                    this.selectedId = p.id; this.jumlah = 1;
                    this.$nextTick(() => this.$refs.panel.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                },
                get n() { const j = parseInt(this.jumlah, 10); return Number.isFinite(j) ? j : 0; },
                tambah() { if (this.terpilih) { this.jumlah = Math.min(this.n + 1, Math.max(this.terpilih.stok, 1)); } },
                kurang() { this.jumlah = Math.max(this.n - 1, 1); },
                get lebih() { return this.terpilih && this.n > this.terpilih.stok; },
                get valid() { return this.terpilih && this.n >= 1 && !this.lebih; }
             }">

            {{-- Katalog produk --}}
            <section>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-3">
                    <h3 class="font-semibold text-slate-800">Pilih Produk</h3>
                    <input type="search" x-model="kataKunci" placeholder="Saring produk (opsional)" autocomplete="off"
                           class="block w-full sm:w-72 min-h-[44px] border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <template x-for="p in hasil" :key="p.id">
                        <button type="button" @click="pilih(p)" :disabled="p.stok === 0"
                                class="relative min-w-0 text-left rounded-xl border bg-white p-3 shadow-sm min-h-[44px] focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                :class="(selectedId === p.id ? 'border-emerald-600 ring-2 ring-emerald-600' : 'border-slate-200')
                                    + (p.stok === 0 ? ' opacity-50 cursor-not-allowed' : ' hover:border-emerald-400 active:bg-emerald-50')">
                            <span x-show="selectedId === p.id" x-cloak class="absolute right-2 top-2 text-emerald-600">
                                <x-icon name="check" class="h-5 w-5" />
                            </span>
                            <span class="block pe-6 text-sm font-semibold text-slate-900 break-words" x-text="p.nama"></span>
                            <span class="mt-1 block text-sm text-slate-700 tabular-nums" x-text="p.harga_teks"></span>
                            <span class="mt-2 inline-block rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums ring-1 ring-inset"
                                  :class="p.stok === 0 ? 'bg-rose-50 text-rose-700 ring-rose-600/20' : (p.stok <= ambang ? 'bg-amber-50 text-amber-800 ring-amber-500/30' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20')"
                                  x-text="p.stok === 0 ? 'Habis' : 'Stok ' + p.stok"></span>
                        </button>
                    </template>
                </div>
                <p x-show="hasil.length === 0" x-cloak class="mt-3 text-sm text-slate-500">Produk tidak ditemukan.</p>
            </section>

            {{-- Panel konfirmasi transaksi --}}
            <div x-ref="panel" class="bg-white border border-slate-200/80 shadow-sm rounded-xl p-4 sm:p-6 max-w-xl">
                <p x-show="!terpilih" class="text-sm text-slate-500">Ketuk salah satu produk di atas untuk mencatat penjualan.</p>

                <form method="POST" action="{{ route('penjualan.store') }}" class="space-y-4" x-show="terpilih" x-cloak>
                    @csrf
                    <input type="hidden" name="produk_id" :value="selectedId">

                    <div>
                        <div class="font-semibold text-slate-900 break-words" x-text="terpilih?.nama"></div>
                        <div class="text-sm text-slate-600 tabular-nums">
                            Sisa stok <span x-text="terpilih?.stok"></span> unit &middot; <span x-text="terpilih?.harga_teks"></span> / unit
                        </div>
                        <x-input-error :messages="$errors->get('produk_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="jumlah" value="Jumlah" />
                        <div class="mt-1 flex items-stretch gap-2">
                            <button type="button" @click="kurang()" :disabled="n <= 1" aria-label="Kurangi jumlah"
                                    class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40">
                                <x-icon name="minus" class="h-5 w-5" />
                            </button>
                            <input id="jumlah" name="jumlah" type="number" inputmode="numeric" min="1" step="1" x-model="jumlah" required
                                   class="block w-full min-w-0 min-h-[48px] text-center text-lg font-semibold tabular-nums border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm"
                                   :class="lebih ? 'border-rose-400 text-rose-700' : ''">
                            <button type="button" @click="tambah()" :disabled="terpilih && n >= terpilih.stok" aria-label="Tambah jumlah"
                                    class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-40">
                                <x-icon name="plus" class="h-5 w-5" />
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                        <p x-show="lebih" x-cloak class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600">
                            <x-icon name="alert" class="h-5 w-5" /> Jumlah melebihi stok yang tersedia.
                        </p>
                    </div>

                    <div class="flex items-baseline justify-between gap-2 rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <span class="font-semibold text-slate-800">Estimasi Total Bayar</span>
                        <span class="text-xl font-bold tabular-nums" :class="valid ? 'text-emerald-700' : 'text-slate-400'"
                              x-text="terpilih && n > 0 ? rp(n * terpilih.harga) : 'Rp 0'"></span>
                    </div>

                    <button type="submit" :disabled="!valid"
                            class="inline-flex w-full items-center justify-center gap-2 min-h-[52px] rounded-lg bg-emerald-600 font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300">
                        <x-icon name="check" /> Simpan Penjualan
                    </button>
                </form>
            </div>

            {{-- Daftar penjualan hari ini --}}
            <section>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <h3 class="font-semibold text-slate-800">Penjualan hari ini ({{ $hariIni->count() }})</h3>
                    <span class="text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('d F Y') }}</span>
                </div>

                @if ($hariIni->isEmpty())
                    <x-card class="p-6 flex flex-col items-center gap-2 text-center text-sm text-slate-500">
                        <x-icon name="receipt" class="h-8 w-8 text-slate-300" />
                        Belum ada penjualan hari ini.
                    </x-card>
                @else
                    {{-- Kartu: layar sempit --}}
                    <ul class="space-y-3 md:hidden">
                        @foreach ($hariIni as $j)
                            <li>
                                <x-card class="p-4 flex gap-3">
                                    <span class="flex h-11 w-14 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-700 tabular-nums">
                                        {{ $j->tanggal_penjualan->format('H:i') }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-semibold text-slate-900 break-words">{{ $j->produk->nama_produk }}</div>
                                        <div class="mt-1 flex flex-wrap items-baseline justify-between gap-x-3 text-sm">
                                            <span class="text-slate-500 tabular-nums">{{ $j->jumlah }} unit</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ Rupiah::format($j->total_harga) }}</span>
                                        </div>
                                        @if ($pemilik)
                                            <div class="mt-0.5 flex justify-between gap-3 text-xs">
                                                <span class="text-slate-500">Keuntungan</span>
                                                <span class="tabular-nums font-medium {{ $j->keuntungan < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ Rupiah::format($j->keuntungan) }}</span>
                                            </div>
                                        @endif
                                    </div>
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
                                        <th class="px-4 py-3">Waktu</th>
                                        <th class="px-4 py-3">Produk</th>
                                        <th class="px-4 py-3 text-right">Jumlah</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        @if ($pemilik)
                                            <th class="px-4 py-3 text-right">Keuntungan</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($hariIni as $j)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3 text-slate-500 tabular-nums">{{ $j->tanggal_penjualan->format('H:i') }}</td>
                                            <td class="px-4 py-3 font-medium text-slate-900">{{ $j->produk->nama_produk }}</td>
                                            <td class="px-4 py-3 text-right tabular-nums">{{ $j->jumlah }}</td>
                                            <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ Rupiah::format($j->total_harga) }}</td>
                                            @if ($pemilik)
                                                <td class="px-4 py-3 text-right tabular-nums {{ $j->keuntungan < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ Rupiah::format($j->keuntungan) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
