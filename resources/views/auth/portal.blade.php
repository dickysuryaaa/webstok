<x-guest-layout>

    <div class="text-center mb-8">

        <h1 class="mt-4 text-2xl font-bold text-slate-900">
            Selamat Datang di Toko Stok
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Silakan pilih halaman login sesuai jenis akun Anda.
        </p>
    </div>

    <div class="space-y-4">

        <!-- Pelanggan -->
        <a href="{{ route('pelanggan.login') }}"
           class="block rounded-xl border-2 border-emerald-200
                  bg-emerald-50 p-5 hover:border-emerald-500
                  hover:bg-emerald-100 transition">

            <div class="flex items-center gap-4">

                <div class="flex-1">
                    <h2 class="font-bold text-slate-900">
                        Area Pelanggan
                    </h2>

                    <p class="text-xs text-slate-600 mt-1">
                        Login atau daftar akun pelanggan.
                    </p>
                </div>

                <span class="text-emerald-700 text-xl">→</span>
            </div>

        </a>

        <!-- Pengelola -->
        <a href="{{ route('login') }}"
           class="block rounded-xl border border-slate-200
                  bg-white p-5 hover:border-slate-400
                  hover:bg-slate-50 transition">

            <div class="flex items-center gap-4">

                <div class="flex-1">
                    <h2 class="font-bold text-slate-900">
                        Area Pengelola Toko
                    </h2>

                    <p class="text-xs text-slate-600 mt-1">
                        Login untuk pemilik dan pekerja.
                    </p>
                </div>

                <span class="text-slate-500 text-xl">→</span>
            </div>

        </a>

    </div>

    <p class="mt-7 text-center text-xs text-slate-400">
        © {{ date('Y') }} Toko Stok
    </p>

</x-guest-layout>