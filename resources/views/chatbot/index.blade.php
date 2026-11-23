@auth
    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                    <x-icon name="bot" />
                </span>
                <div class="min-w-0">
                    <h2 class="font-bold text-xl text-slate-900 leading-tight">Chatbot Cek Stok</h2>
                    <p class="text-xs text-slate-500">Tanya stok dan harga jual barang secara otomatis.</p>
                </div>
            </div>
        </x-slot>

        <div class="py-4 sm:py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white border border-slate-200/80 shadow-sm rounded-xl overflow-hidden">
                    @include('chatbot._chat', ['tinggi' => 'h-[calc(100dvh-16rem)] min-h-[22rem] md:h-[70vh]'])
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    <x-guest-layout>
        <div class="-m-5 sm:-m-6">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-200/80">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                    <x-icon name="bot" />
                </span>
                <h2 class="font-semibold text-lg text-slate-900">Chatbot Cek Stok</h2>
            </div>
            @include('chatbot._chat', ['tinggi' => 'h-[70vh] min-h-[22rem]'])
        </div>
    </x-guest-layout>
@endauth
