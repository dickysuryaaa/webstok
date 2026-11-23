@php
    $pengguna = Auth::user();
    $menu = [
        ['Dasbor', 'Dasbor', 'dashboard', 'dashboard', 'home'],
        ['Penjualan', 'Penjualan', 'penjualan.index', 'penjualan.*', 'cart'],
        ['Produk', 'Produk', 'produk.index', 'produk.*', 'box'],
        ['Chatbot', 'Chatbot', 'chatbot.index', 'chatbot.index', 'chat'],
    ];
    if ($pengguna->isPemilik()) {
        $menu[] = ['Log Chatbot', 'Log', 'chatbot.log', 'chatbot.log', 'list'];
    }
@endphp

{{-- Bilah atas --}}
<nav class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-3 h-16">
            <div class="flex items-center gap-6 min-w-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-h-[44px] min-w-0" aria-label="Beranda">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm">
                        <x-application-logo class="h-5 w-5 fill-current" />
                    </span>
                    <span class="hidden sm:block truncate font-bold text-slate-800">{{ config('app.name', 'Laravel') }}</span>
                </a>

                {{-- Menu layar lebar --}}
                <div class="hidden md:flex items-center gap-1">
                    @foreach ($menu as [$label, $pendek, $rute, $aktif, $ikon])
                        @php $sedang = request()->routeIs($aktif); @endphp
                        <a href="{{ route($rute) }}" @if ($sedang) aria-current="page" @endif
                           class="inline-flex items-center gap-2 min-h-[44px] px-3 rounded-lg text-sm font-medium transition {{ $sedang ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-icon :name="$ikon" class="h-5 w-5" />
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Profil dan peran --}}
            <x-dropdown align="right" width="w-56">
                <x-slot name="trigger">
                    <button type="button" class="flex items-center gap-2 min-h-[44px] max-w-[11rem] sm:max-w-xs ps-1 pe-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-600">
                            {{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:block truncate font-medium">{{ $pengguna->name }}</span>
                        <x-role-badge :pemilik="$pengguna->isPemilik()" />
                        <x-icon name="chevron-down" class="h-4 w-4 text-slate-400" />
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <div class="font-medium text-slate-800 break-words">{{ $pengguna->name }}</div>
                        <div class="text-xs text-slate-500 break-all">{{ $pengguna->email }}</div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">
                        <x-icon name="user" class="h-4 w-4 text-slate-400" /> {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" class="text-rose-600"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <x-icon name="logout" class="h-4 w-4" /> {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>

{{-- Navigasi bawah khusus HP --}}
<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-slate-200/80 pb-[env(safe-area-inset-bottom)]"
     aria-label="Navigasi utama">
    <ul class="grid {{ count($menu) === 5 ? 'grid-cols-5' : 'grid-cols-4' }}">
        @foreach ($menu as [$label, $pendek, $rute, $aktif, $ikon])
            @php $sedang = request()->routeIs($aktif); @endphp
            <li>
                <a href="{{ route($rute) }}" @if ($sedang) aria-current="page" @endif aria-label="{{ $label }}"
                   class="relative flex flex-col items-center justify-center gap-0.5 min-h-[56px] px-1 text-[11px] font-medium transition {{ $sedang ? 'text-emerald-600' : 'text-slate-500 active:text-slate-800' }}">
                    <span class="absolute top-0 h-0.5 w-8 rounded-full {{ $sedang ? 'bg-emerald-600' : 'bg-transparent' }}"></span>
                    <span class="flex h-7 w-12 items-center justify-center rounded-full {{ $sedang ? 'bg-emerald-50' : '' }}">
                        <x-icon :name="$ikon" class="h-5 w-5" />
                    </span>
                    <span class="truncate max-w-full">{{ $pendek }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
