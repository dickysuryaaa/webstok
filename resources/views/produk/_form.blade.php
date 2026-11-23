@php
    $field = 'mt-1 block w-full min-h-[44px] border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm';
    $angka = $field.' ps-10 tabular-nums';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5"
      x-data="{
          beli: @js((string) old('harga_beli', $produk->harga_beli)),
          jual: @js((string) old('harga_jual', $produk->harga_jual)),
          rugiDicentang: @js((bool) old('konfirmasi_rugi')),
          get b() { const n = parseInt(this.beli, 10); return Number.isFinite(n) ? n : null; },
          get j() { const n = parseInt(this.jual, 10); return Number.isFinite(n) ? n : null; },
          get lengkap() { return this.b !== null && this.j !== null; },
          get untung() { return this.lengkap ? this.j - this.b : 0; },
          get rugi() { return this.lengkap && this.j < this.b; },
          rp(n) { return (n < 0 ? '-Rp ' : 'Rp ') + new Intl.NumberFormat('id-ID').format(Math.abs(n)); }
      }">
    @csrf
    @if ($produk->exists)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="nama_produk" value="Nama produk" />
        <input id="nama_produk" name="nama_produk" type="text" maxlength="150" required class="{{ $field }}"
               value="{{ old('nama_produk', $produk->nama_produk) }}">
        <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="stok" value="Stok (unit)" />
        <input id="stok" name="stok" type="number" inputmode="numeric" min="0" step="1" required class="{{ $field }} tabular-nums"
               value="{{ old('stok', $produk->stok ?? 0) }}">
        @if ($produk->exists)
            <p class="mt-1 text-xs text-slate-500">Tambah stok barang masuk dengan mengubah angka ini.</p>
        @endif
        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="harga_beli" value="Harga beli (Rp)" />
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 mt-0.5 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input id="harga_beli" name="harga_beli" type="number" inputmode="numeric" min="0" step="1" required class="{{ $angka }}"
                       x-model="beli" value="{{ old('harga_beli', $produk->harga_beli) }}">
            </div>
            <x-input-error :messages="$errors->get('harga_beli')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="harga_jual" value="Harga jual (Rp)" />
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 mt-0.5 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input id="harga_jual" name="harga_jual" type="number" inputmode="numeric" min="0" step="1" required class="{{ $angka }}"
                       x-model="jual" value="{{ old('harga_jual', $produk->harga_jual) }}">
            </div>
            <x-input-error :messages="$errors->get('harga_jual')" class="mt-2" />
        </div>
    </div>

    {{-- Kalkulasi langsung keuntungan per unit --}}
    <div class="flex items-center justify-between gap-3 rounded-xl border px-4 py-3"
         :class="!lengkap ? 'border-slate-200 bg-slate-50' : (rugi ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50')">
        <span class="text-sm font-medium text-slate-700">Keuntungan per unit</span>
        <span class="text-lg font-bold tabular-nums"
              :class="!lengkap ? 'text-slate-400' : (rugi ? 'text-rose-600' : 'text-emerald-700')"
              x-text="lengkap ? rp(untung) : 'Rp -'">Rp -</span>
    </div>

    <div class="rounded-xl border p-3 transition"
         :class="rugi ? (rugiDicentang ? 'border-amber-300 bg-amber-50' : 'border-rose-300 bg-rose-50 ring-2 ring-rose-200') : 'border-slate-200'">
        <label for="konfirmasi_rugi" class="flex items-center gap-3 min-h-[44px] cursor-pointer">
            <input type="hidden" name="konfirmasi_rugi" value="0">
            <input id="konfirmasi_rugi" name="konfirmasi_rugi" type="checkbox" value="1"
                   @checked(old('konfirmasi_rugi')) x-model="rugiDicentang"
                   class="h-5 w-5 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <span class="text-sm text-slate-700">Saya sadar harga jual lebih rendah dari harga beli (jual rugi)</span>
        </label>
        <p x-show="rugi && !rugiDicentang" x-cloak class="mt-1 flex items-center gap-2 text-xs font-medium text-rose-700">
            <x-icon name="alert" class="h-4 w-4" /> Harga jual di bawah harga beli. Centang kotak ini untuk melanjutkan.
        </p>
        <x-input-error :messages="$errors->get('konfirmasi_rugi')" class="mt-2" />
    </div>

    <div class="flex gap-2 pt-1">
        <button type="submit"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 min-h-[44px] px-6 rounded-lg bg-emerald-600 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <x-icon name="check" class="h-5 w-5" /> Simpan
        </button>
        <a href="{{ route('produk.index') }}"
           class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-6 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>
