@auth
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chatbot Cek Stok</h2>
        </x-slot>

        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    @include('chatbot._chat')
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    <x-guest-layout>
        <h2 class="font-semibold text-lg text-gray-800 mb-3">Chatbot Cek Stok</h2>
        @include('chatbot._chat')
    </x-guest-layout>
@endauth
