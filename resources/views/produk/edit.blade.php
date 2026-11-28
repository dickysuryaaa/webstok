<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            Ubah Produk
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-slate-200/80 shadow-sm rounded-xl p-4 sm:p-6">

                <!-- Tombol Kelola Gambar -->
                <div class="flex justify-end mb-5">
                    <a href="{{ route('produk.gambar.edit', $produk) }}"
                       class="inline-flex items-center gap-2 px-4 py-2
                              bg-emerald-600 hover:bg-emerald-700
                              text-white font-semibold rounded-lg
                              transition">

                        Kelola Gambar
                    </a>
                </div>

                <!-- Form Ubah Produk -->
                @include('produk._form', [
                    'action' => route('produk.update', $produk)
                ])

            </div>

        </div>
    </div>
</x-app-layout>