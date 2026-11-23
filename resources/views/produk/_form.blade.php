@php
    $field = 'mt-1 block w-full min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
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
        <input id="stok" name="stok" type="number" inputmode="numeric" min="0" step="1" required class="{{ $field }}"
               value="{{ old('stok', $produk->stok ?? 0) }}">
        @if ($produk->exists)
            <p class="mt-1 text-xs text-gray-500">Tambah stok barang masuk dengan mengubah angka ini.</p>
        @endif
        <x-input-error :messages="$errors->get('stok')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="harga_beli" value="Harga beli (Rp)" />
        <input id="harga_beli" name="harga_beli" type="number" inputmode="numeric" min="0" step="1" required class="{{ $field }}"
               value="{{ old('harga_beli', $produk->harga_beli) }}">
        <x-input-error :messages="$errors->get('harga_beli')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="harga_jual" value="Harga jual (Rp)" />
        <input id="harga_jual" name="harga_jual" type="number" inputmode="numeric" min="0" step="1" required class="{{ $field }}"
               value="{{ old('harga_jual', $produk->harga_jual) }}">
        <x-input-error :messages="$errors->get('harga_jual')" class="mt-2" />
    </div>

    <div>
        <label for="konfirmasi_rugi" class="inline-flex items-center gap-3 min-h-[44px]">
            <input type="hidden" name="konfirmasi_rugi" value="0">
            <input id="konfirmasi_rugi" name="konfirmasi_rugi" type="checkbox" value="1"
                   @checked(old('konfirmasi_rugi'))
                   class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Saya sadar harga jual lebih rendah dari harga beli (jual rugi)</span>
        </label>
    </div>

    <div class="flex gap-2">
        <button type="submit"
                class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-6 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Simpan
        </button>
        <a href="{{ route('produk.index') }}"
           class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-6 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
            Batal
        </a>
    </div>
</form>
