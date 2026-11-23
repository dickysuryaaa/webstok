@use('App\Support\Rupiah')

<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Penjualan</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pilih produk, atur jumlah, lalu simpan.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-5 lg:items-start">
            {{-- Form catat penjualan --}}
            <div class="bg-white border border-slate-200/80 shadow-sm rounded-xl p-4 sm:p-6 lg:col-span-2 lg:sticky lg:top-20"
                 x-data="{
                    produk: @js($pilihan),
                    cari: '',
                    buka: false,
                    sorot: 0,
                    terpilih: null,
                    jumlah: @js((int) old('jumlah', 1)),
                    init() {
                        const id = @js((int) old('produk_id', 0));
                        if (id) { this.terpilih = this.produk.find(p => p.id === id) ?? null; }
                        this.$watch('cari', () => { this.sorot = 0; });
                    },
                    get hasil() {
                        const k = this.cari.trim().toLowerCase();
                        return this.produk.filter(p => k === '' || p.nama.toLowerCase().includes(k)).slice(0, 50);
                    },
                    rp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); },
                    pilih(p) {
                        if (!p) { return; }
                        this.terpilih = p; this.buka = false; this.cari = '';
                        if (this.n < 1) { this.jumlah = 1; }
                        this.$nextTick(() => { this.$refs.jumlah.focus(); this.$refs.jumlah.select(); });
                    },
                    ganti() { this.terpilih = null; this.buka = true; this.$nextTick(() => this.$refs.cari.focus()); },
                    gerak(d) { if (!this.buka) { this.buka = true; return; } const m = this.hasil.length; if (m) { this.sorot = (this.sorot + d + m) % m; } },
                    get n() { const j = parseInt(this.jumlah, 10); return Number.isFinite(j) ? j : 0; },
                    tambah() { this.jumlah = this.terpilih ? Math.min(this.n + 1, Math.max(this.terpilih.stok, 1)) : this.n + 1; },
                    kurang() { this.jumlah = Math.max(this.n - 1, 1); },
                    get habis() { return this.terpilih && this.terpilih.stok === 0; },
                    get lebih() { return this.terpilih && this.n > this.terpilih.stok; },
                    get sisa() { return this.terpilih ? this.terpilih.stok - this.n : 0; },
                    get valid() { return this.terpilih && this.n >= 1 && !this.lebih; }
                 }">
                <form method="POST" action="{{ route('penjualan.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="produk_id" :value="terpilih ? terpilih.id : ''">

                    <div>
                        <x-input-label for="cari" value="Produk" />

                        {{-- Produk terpilih --}}
                        <div x-show="terpilih" x-cloak class="mt-1 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 p-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-600 ring-1 ring-emerald-200">
                                <x-icon name="box" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900 break-words" x-text="terpilih?.nama"></div>
                                <div class="text-xs tabular-nums" :class="habis ? 'text-rose-600 font-semibold' : 'text-slate-600'">
                                    <span x-text="habis ? 'Stok habis' : 'Sisa stok ' + terpilih?.stok + ' unit'"></span>
                                    &middot; <span x-text="terpilih?.harga_teks"></span>
                                </div>
                            </div>
                            <button type="button" @click="ganti()"
                                    class="shrink-0 min-h-[44px] px-3 rounded-lg text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Ganti</button>
                        </div>

                        {{-- Pencarian produk --}}
                        <div x-show="!terpilih" class="relative mt-1" @click.outside="buka = false">
                            <div class="relative">
                                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                                <input id="cari" x-ref="cari" type="text" autocomplete="off" placeholder="Ketik atau ketuk untuk memilih produk"
                                       x-model="cari" @focus="buka = true" @click="buka = true" @input="buka = true"
                                       @keydown.arrow-down.prevent="gerak(1)" @keydown.arrow-up.prevent="gerak(-1)"
                                       @keydown.enter.prevent="pilih(hasil[sorot])" @keydown.escape="buka = false"
                                       role="combobox" :aria-expanded="buka"
                                       class="block w-full min-h-[48px] ps-10 pe-10 border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm">
                                <button type="button" x-show="cari" x-cloak @click="cari = ''; $refs.cari.focus()" aria-label="Hapus pencarian"
                                        class="absolute right-0 top-0 inline-flex h-12 w-11 items-center justify-center text-xl text-slate-400 hover:text-slate-600">&times;</button>
                            </div>
                            <ul x-show="buka" x-cloak role="listbox"
                                x-transition.opacity.duration.100ms
                                class="absolute z-30 mt-1 w-full max-h-72 overflow-y-auto overscroll-contain bg-white border border-slate-200 rounded-xl shadow-lg divide-y divide-slate-100">
                                <template x-for="(p, i) in hasil" :key="p.id">
                                    <li>
                                        <button type="button" @click="pilih(p)" @mouseenter="sorot = i"
                                                class="flex w-full min-h-[52px] items-center gap-3 px-3 py-2 text-left focus:outline-none"
                                                :class="sorot === i ? 'bg-emerald-50' : 'hover:bg-slate-50'">
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm font-medium text-slate-900 break-words" x-text="p.nama"></span>
                                                <span class="block text-xs text-slate-500 tabular-nums" x-text="p.harga_teks"></span>
                                            </span>
                                            <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums ring-1 ring-inset"
                                                  :class="p.stok === 0 ? 'bg-rose-50 text-rose-700 ring-rose-600/20' : (p.stok <= {{ (int) config('toko.stok_menipis', 5) }} ? 'bg-amber-50 text-amber-800 ring-amber-500/30' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20')"
                                                  x-text="p.stok === 0 ? 'Habis' : 'Stok ' + p.stok"></span>
                                        </button>
                                    </li>
                                </template>
                                <li x-show="hasil.length === 0" class="px-3 py-4 text-sm text-slate-500">Produk tidak ditemukan.</li>
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('produk_id')" class="mt-2" />
                    </div>

                    <div x-show="terpilih" x-cloak>
                        <x-input-label for="jumlah" value="Jumlah" />
                        <div class="mt-1 flex items-stretch gap-2">
                            <button type="button" @click="kurang()" :disabled="n <= 1" aria-label="Kurangi jumlah"
                                    class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 active:bg-slate-100 disabled:opacity-40">
                                <x-icon name="minus" class="h-5 w-5" />
                            </button>
                            <input id="jumlah" x-ref="jumlah" name="jumlah" type="number" inputmode="numeric" min="1" step="1"
                                   x-model="jumlah" required
                                   class="block w-full min-w-0 min-h-[48px] text-center text-lg font-semibold tabular-nums border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm"
                                   :class="lebih ? 'border-rose-400 text-rose-700' : ''">
                            <button type="button" @click="tambah()" :disabled="habis || (terpilih && n >= terpilih.stok)" aria-label="Tambah jumlah"
                                    class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-40">
                                <x-icon name="plus" class="h-5 w-5" />
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                    </div>

                    {{-- Ringkasan transaksi live --}}
                    <div x-show="terpilih" x-cloak class="rounded-xl border p-4"
                         :class="habis || lebih ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50'">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ringkasan Transaksi</div>
                        <dl class="mt-2 space-y-1.5 text-sm">
                            <div class="flex justify-between gap-2">
                                <dt class="text-slate-600">Harga satuan</dt>
                                <dd class="font-medium text-slate-900 tabular-nums" x-text="terpilih ? rp(terpilih.harga) : '-'"></dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-slate-600">Sisa stok setelah transaksi</dt>
                                <dd class="font-medium tabular-nums" :class="sisa < 0 ? 'text-rose-600' : (sisa <= {{ (int) config('toko.stok_menipis', 5) }} ? 'text-amber-700' : 'text-slate-900')"
                                    x-text="sisa < 0 ? 'Tidak cukup' : sisa + ' unit'"></dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-2 border-t border-slate-200 pt-2">
                                <dt class="font-semibold text-slate-800">Estimasi total bayar</dt>
                                <dd class="text-xl font-bold tabular-nums" :class="valid ? 'text-emerald-700' : 'text-slate-400'"
                                    x-text="terpilih && n > 0 ? rp(n * terpilih.harga) : 'Rp 0'"></dd>
                            </div>
                        </dl>
                        <p x-show="habis" x-cloak class="mt-3 flex items-center gap-2 text-sm font-medium text-rose-700">
                            <x-icon name="alert" class="h-5 w-5" /> Stok produk ini habis.
                        </p>
                        <p x-show="!habis && lebih" x-cloak class="mt-3 flex items-center gap-2 text-sm font-medium text-rose-700">
                            <x-icon name="alert" class="h-5 w-5" /> Jumlah melebihi stok yang tersedia.
                        </p>
                    </div>

                    <button type="submit" x-show="terpilih" x-cloak :disabled="!valid"
                            class="inline-flex w-full items-center justify-center gap-2 min-h-[52px] rounded-lg bg-emerald-600 font-semibold text-white shadow-sm hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300">
                        <x-icon name="check" /> Simpan Penjualan
                    </button>
                </form>
            </div>

            {{-- Daftar penjualan hari ini --}}
            <section class="lg:col-span-3">
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
