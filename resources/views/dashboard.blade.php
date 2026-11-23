<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dasbor</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Pemilih tanggal --}}
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2">
                <div class="flex-1 min-w-[10rem]">
                    <x-input-label for="tanggal" value="Tanggal rekap" />
                    <input id="tanggal" type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}"
                           max="{{ $hariIni->format('Y-m-d') }}" required
                           class="mt-1 block w-full min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                </div>
                <x-primary-button>Tampilkan</x-primary-button>
                @unless ($adalahHariIni)
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center min-h-[44px] px-4 border border-gray-300 bg-white rounded-md text-sm text-gray-700 hover:bg-gray-50">Hari ini</a>
                @endunless
            </form>
            <x-input-error :messages="$errors->get('tanggal')" />

            <p class="text-sm text-gray-600">
                Rekap {{ $adalahHariIni ? 'hari ini, ' : '' }}{{ $tanggal->locale('id')->translatedFormat('l, d F Y') }}
            </p>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 {{ $pemilik ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Jumlah transaksi</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $transaksi }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Total penjualan</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 break-words">{{ \App\Support\Rupiah::format($total) }}</div>
                </div>
                @if ($pemilik)
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total keuntungan</div>
                        <div class="mt-1 text-2xl font-semibold break-words {{ $untung < 0 ? 'text-red-700' : 'text-gray-900' }}">{{ \App\Support\Rupiah::format($untung) }}</div>
                    </div>
                @endif
            </div>

            {{-- Stok menipis --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Stok menipis ({{ $menipis->count() }})</h3>
                @if ($menipis->isEmpty())
                    <div class="bg-white shadow-sm sm:rounded-lg p-4 text-sm text-gray-600">Tidak ada produk dengan stok menipis.</div>
                @else
                    <ul class="bg-white shadow-sm rounded-lg divide-y divide-gray-100">
                        @foreach ($menipis as $p)
                            <li>
                                <a href="{{ route('produk.index', ['q' => $p->nama_produk]) }}"
                                   class="flex items-center justify-between gap-3 min-h-[44px] px-4 py-2 hover:bg-gray-50">
                                    <span class="min-w-0 break-words text-sm text-gray-900">{{ $p->nama_produk }}</span>
                                    <span class="shrink-0 flex items-center gap-2 text-sm">
                                        <span class="text-gray-600">Stok {{ $p->stok }}</span>
                                        @if ($p->stok === 0)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Habis</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Stok menipis</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
