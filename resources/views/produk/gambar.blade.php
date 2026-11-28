<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900">
                Kelola Gambar Produk
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan atau ganti gambar produk.
            </p>
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-slate-200
                        rounded-xl shadow-sm p-5 sm:p-6">

                <h3 class="font-semibold text-lg text-slate-900">
                    {{ $produk->nama_produk }}
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Gambar produk yang akan ditampilkan pada katalog.
                </p>

                <!-- Preview Gambar -->
                <div class="mt-5">

                    <p class="text-sm font-medium text-slate-700 mb-2">
                        Gambar Saat Ini
                    </p>

                    @if ($produk->gambar)
                        <img
                            src="{{ asset('storage/'.$produk->gambar) }}"
                            alt="{{ $produk->nama_produk }}"
                            class="w-full h-56 object-contain
                                   rounded-lg border border-slate-200
                                   bg-slate-50 p-2"
                        >
                    @else
                        <div class="w-full h-56 flex items-center
                                    justify-center rounded-lg
                                    border border-dashed border-slate-300
                                    bg-slate-50">

                            <span class="text-sm text-slate-400">
                                Belum ada gambar produk.
                            </span>

                        </div>
                    @endif

                </div>

                <!-- Form Upload -->
                <form
                    method="POST"
                    action="{{ route('produk.gambar.update', $produk) }}"
                    enctype="multipart/form-data"
                    class="mt-6 space-y-4"
                >

                    @csrf
                    @method('PUT')

                    <div>

                        <label for="gambar"
                               class="block text-sm font-medium text-slate-700">

                            Pilih Gambar Baru

                        </label>

                        <input
                            type="file"
                            name="gambar"
                            id="gambar"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                            class="mt-2 block w-full text-sm text-slate-600
                                   file:mr-4 file:rounded-lg file:border-0
                                   file:bg-emerald-50 file:px-4 file:py-2
                                   file:font-semibold file:text-emerald-700
                                   hover:file:bg-emerald-100"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Format JPG, JPEG, PNG, atau WEBP.
                            Maksimal ukuran 5 MB.
                        </p>

                        <x-input-error
                            :messages="$errors->get('gambar')"
                            class="mt-2"
                        />

                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-2">

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center min-h-[44px]
                                   px-5 rounded-lg bg-emerald-600 text-sm
                                   font-semibold text-white hover:bg-emerald-700"
                        >
                            Simpan Gambar
                        </button>

                        <a
                            href="{{ route('produk.index') }}"
                            class="inline-flex items-center justify-center
                                   min-h-[44px] px-5 rounded-lg
                                   border border-slate-300 text-sm
                                   font-semibold text-slate-700
                                   hover:bg-slate-50"
                        >
                            Kembali
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>