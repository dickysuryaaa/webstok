<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Penjualan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-3 rounded-md bg-green-50 text-green-800 text-sm">{{ session('status') }}</div>
            @endif

            {{-- Form catat penjualan --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6"
                 x-data="{
                    produk: @js($pilihan),
                    cari: '',
                    buka: false,
                    terpilih: null,
                    jumlah: @js(old('jumlah', 1)),
                    init() {
                        const id = @js((int) old('produk_id', 0));
                        if (id) { this.terpilih = this.produk.find(p => p.id === id) ?? null; }
                    },
                    get hasil() {
                        const k = this.cari.trim().toLowerCase();
                        return this.produk.filter(p => k === '' || p.nama.toLowerCase().includes(k)).slice(0, 30);
                    },
                    pilih(p) { this.terpilih = p; this.buka = false; this.cari = ''; this.$nextTick(() => { this.$refs.jumlah.focus(); this.$refs.jumlah.select(); }); },
                    ganti() { this.terpilih = null; this.buka = true; this.$nextTick(() => this.$refs.cari.focus()); },
                    get total() { const j = parseInt(this.jumlah, 10); return this.terpilih && j > 0 ? new Intl.NumberFormat('id-ID').format(j * this.terpilih.harga) : null; },
                    get lebih() { return this.terpilih && parseInt(this.jumlah, 10) > this.terpilih.stok; }
                 }">
                <form method="POST" action="{{ route('penjualan.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="produk_id" :value="terpilih ? terpilih.id : ''">

                    <div>
                        <x-input-label for="cari" value="Produk" />

                        {{-- Produk terpilih --}}
                        <div x-show="terpilih" x-cloak class="mt-1 rounded-md border border-indigo-200 bg-indigo-50 p-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-semibold text-gray-900 break-words" x-text="terpilih?.nama"></div>
                                <button type="button" @click="ganti()"
                                        class="shrink-0 min-h-[44px] px-3 text-sm text-indigo-700 underline">Ganti</button>
                            </div>
                            <dl class="grid grid-cols-2 gap-y-1 text-sm">
                                <dt class="text-gray-600">Sisa stok</dt>
                                <dd class="text-right font-medium" :class="terpilih?.stok === 0 ? 'text-red-700' : 'text-gray-900'"
                                    x-text="terpilih?.stok === 0 ? 'Habis' : terpilih?.stok + ' unit'"></dd>
                                <dt class="text-gray-600">Harga jual</dt>
                                <dd class="text-right font-medium text-gray-900" x-text="terpilih?.harga_teks"></dd>
                            </dl>
                        </div>

                        {{-- Pencarian produk --}}
                        <div x-show="!terpilih" class="relative" @click.outside="buka = false">
                            <input id="cari" x-ref="cari" type="search" autocomplete="off" placeholder="Ketik nama produk"
                                   x-model="cari" @focus="buka = true" @input="buka = true"
                                   class="mt-1 block w-full min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <ul x-show="buka" x-cloak
                                class="absolute z-20 mt-1 w-full max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg divide-y divide-gray-100">
                                <template x-for="p in hasil" :key="p.id">
                                    <li>
                                        <button type="button" @click="pilih(p)"
                                                class="w-full min-h-[44px] px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                                            <span class="block text-sm text-gray-900 break-words" x-text="p.nama"></span>
                                            <span class="block text-xs" :class="p.stok === 0 ? 'text-red-700' : 'text-gray-500'"
                                                  x-text="(p.stok === 0 ? 'Habis' : 'Stok ' + p.stok) + ' - ' + p.harga_teks"></span>
                                        </button>
                                    </li>
                                </template>
                                <li x-show="hasil.length === 0" class="px-3 py-3 text-sm text-gray-500">Produk tidak ditemukan.</li>
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('produk_id')" class="mt-2" />
                    </div>

                    <div x-show="terpilih" x-cloak>
                        <x-input-label for="jumlah" value="Jumlah" />
                        <input id="jumlah" x-ref="jumlah" name="jumlah" type="number" inputmode="numeric" min="1" step="1"
                               x-model="jumlah" required
                               class="mt-1 block w-full min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <p x-show="lebih" x-cloak class="mt-1 text-sm text-red-700">Jumlah melebihi stok yang tersedia.</p>
                        <p x-show="total && !lebih" x-cloak class="mt-1 text-sm text-gray-600">Total: Rp <span x-text="total"></span></p>
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                    </div>

                    <button type="submit" x-show="terpilih" x-cloak
                            class="w-full min-h-[48px] bg-gray-800 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Simpan Penjualan
                    </button>
                </form>
            </div>

            {{-- Daftar penjualan hari ini --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Penjualan hari ini ({{ $hariIni->count() }})</h3>

                @if ($hariIni->isEmpty())
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600 text-sm">Belum ada penjualan hari ini.</div>
                @else
                    {{-- Kartu: layar sempit --}}
                    <div class="space-y-3 md:hidden">
                        @foreach ($hariIni as $j)
                            <div class="bg-white shadow-sm rounded-lg p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="font-semibold text-gray-900 break-words">{{ $j->produk->nama_produk }}</div>
                                    <div class="shrink-0 text-xs text-gray-500">{{ $j->tanggal_penjualan->format('H:i') }}</div>
                                </div>
                                <dl class="mt-2 grid grid-cols-2 gap-y-1 text-sm">
                                    <dt class="text-gray-500">Jumlah</dt>
                                    <dd class="text-right text-gray-900">{{ $j->jumlah }}</dd>
                                    <dt class="text-gray-500">Total</dt>
                                    <dd class="text-right text-gray-900">{{ \App\Support\Rupiah::format($j->total_harga) }}</dd>
                                    @if ($pemilik)
                                        <dt class="text-gray-500">Keuntungan</dt>
                                        <dd class="text-right {{ $j->keuntungan < 0 ? 'text-red-700' : 'text-gray-900' }}">{{ \App\Support\Rupiah::format($j->keuntungan) }}</dd>
                                    @endif
                                </dl>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tabel: layar lebar --}}
                    <div class="hidden md:block bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($hariIni as $j)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-500">{{ $j->tanggal_penjualan->format('H:i') }}</td>
                                        <td class="px-4 py-3 text-gray-900">{{ $j->produk->nama_produk }}</td>
                                        <td class="px-4 py-3 text-right">{{ $j->jumlah }}</td>
                                        <td class="px-4 py-3 text-right">{{ \App\Support\Rupiah::format($j->total_harga) }}</td>
                                        @if ($pemilik)
                                            <td class="px-4 py-3 text-right {{ $j->keuntungan < 0 ? 'text-red-700' : '' }}">{{ \App\Support\Rupiah::format($j->keuntungan) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
