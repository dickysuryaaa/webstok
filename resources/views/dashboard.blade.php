@php
    $tglIni = $tanggal->format('Y-m-d');
    $tglHariIni = $hariIni->format('Y-m-d');
    $tglKemarin = $kemarin->format('Y-m-d');
    $habis = $menipis->where('stok', 0);
    $hampir = $menipis->where('stok', '>', 0);
    $pintasan = 'inline-flex items-center justify-center min-h-[44px] px-2 sm:px-4 rounded-lg text-sm font-semibold border transition';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="min-w-0">
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Rekap {{ $adalahHariIni ? 'hari ini, ' : '' }}{{ $tanggal->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <a href="{{ route('penjualan.index') }}"
               class="inline-flex shrink-0 items-center justify-center gap-1.5 min-h-[44px] px-4 rounded-lg bg-emerald-600 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                <x-icon name="plus" class="h-5 w-5" />
                Catat Penjualan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Pemilih tanggal --}}
            <x-card class="p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1 min-w-0">
                        <x-input-label for="tanggal" value="Tanggal rekap" />
                        <input id="tanggal" type="date" name="tanggal" value="{{ $tglIni }}"
                               max="{{ $tglHariIni }}" required onchange="this.form.requestSubmit()"
                               class="mt-1 block w-full min-h-[44px] border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg shadow-sm tabular-nums">
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:flex">
                        <a href="{{ route('dashboard') }}"
                           class="{{ $pintasan }} {{ $tglIni === $tglHariIni ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' }}">Hari Ini</a>
                        <a href="{{ route('dashboard', ['tanggal' => $tglKemarin]) }}"
                           class="{{ $pintasan }} {{ $tglIni === $tglKemarin ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' }}">Kemarin</a>
                        <button type="submit" class="{{ $pintasan }} bg-white border-slate-300 text-slate-700 hover:bg-slate-50">Tampilkan</button>
                    </div>
                </form>
                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
            </x-card>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 gap-3 {{ $pemilik ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                <x-card class="p-4 flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <x-icon name="receipt" class="h-6 w-6" />
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm text-slate-500">Jumlah transaksi</div>
                        <div class="mt-0.5 text-2xl font-bold text-slate-900 tabular-nums">{{ $transaksi }}</div>
                    </div>
                </x-card>
                <x-card class="p-4 flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <x-icon name="cash" class="h-6 w-6" />
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm text-slate-500">Total penjualan</div>
                        <div class="mt-0.5 text-2xl font-bold text-slate-900 tabular-nums break-words">{{ \App\Support\Rupiah::format($total) }}</div>
                    </div>
                </x-card>
                @if ($pemilik)
                    <x-card class="p-4 flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $untung < 0 ? 'bg-rose-50 text-rose-600' : 'bg-violet-50 text-violet-600' }}">
                            <x-icon name="trend" class="h-6 w-6" />
                        </span>
                        <div class="min-w-0">
                            <div class="text-sm text-slate-500">Total keuntungan</div>
                            <div class="mt-0.5 text-2xl font-bold tabular-nums break-words {{ $untung < 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ \App\Support\Rupiah::format($untung) }}</div>
                        </div>
                    </x-card>
                @endif
            </div>

            {{-- Stok menipis --}}
            <section>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <h3 class="font-semibold text-slate-800">Stok menipis ({{ $menipis->count() }})</h3>
                    <span class="text-xs text-slate-500">Ambang &le; <span class="tabular-nums">{{ $ambang }}</span> unit</span>
                </div>

                @if ($menipis->isEmpty())
                    <x-card class="p-4 flex items-center gap-3 text-sm text-slate-600">
                        <x-icon name="check" class="h-6 w-6 text-emerald-600" />
                        Tidak ada produk dengan stok menipis.
                    </x-card>
                @else
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach ([['Habis', $habis, 'rose'], ['Stok menipis', $hampir, 'amber']] as [$judul, $daftar, $warna])
                            @continue($daftar->isEmpty())
                            <x-card class="overflow-hidden">
                                <div class="flex items-center gap-2 px-4 py-3 border-b {{ $warna === 'rose' ? 'bg-rose-50 border-rose-100 text-rose-700' : 'bg-amber-50 border-amber-100 text-amber-800' }}">
                                    <x-icon name="alert" class="h-5 w-5" />
                                    <span class="text-sm font-semibold">{{ $judul }}</span>
                                    <span class="ms-auto text-xs font-semibold tabular-nums">{{ $daftar->count() }} produk</span>
                                </div>
                                <ul class="divide-y divide-slate-100">
                                    @foreach ($daftar as $p)
                                        <li class="flex items-center gap-3 px-4 py-2 min-h-[56px]">
                                            <a href="{{ route('produk.index', ['q' => $p->nama_produk]) }}"
                                               class="min-w-0 flex-1 break-words text-sm font-medium text-slate-900 hover:text-emerald-700">{{ $p->nama_produk }}</a>
                                            <span class="shrink-0 text-sm tabular-nums {{ $p->stok === 0 ? 'text-rose-600 font-semibold' : 'text-amber-700 font-semibold' }}">{{ $p->stok }} unit</span>
                                            @if ($pemilik)
                                                <a href="{{ route('produk.edit', $p) }}" aria-label="Ubah {{ $p->nama_produk }}"
                                                   class="inline-flex shrink-0 items-center justify-center h-11 w-11 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-emerald-700">
                                                    <x-icon name="pencil" class="h-5 w-5" />
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </x-card>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
