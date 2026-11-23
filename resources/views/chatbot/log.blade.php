<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Chatbot</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                @if ($log->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada percakapan.</p>
                @else
                    {{-- Kartu di layar sempit --}}
                    <div class="space-y-3 sm:hidden">
                        @foreach ($log as $l)
                            <div class="border border-gray-200 rounded-md p-3 text-sm">
                                <div class="text-xs text-gray-500">
                                    {{ $l->created_at->format('d/m/Y H:i') }} &middot; {{ $l->user?->name ?? 'Tamu' }}
                                </div>
                                <div class="mt-1 font-medium break-words">{{ $l->pertanyaan }}</div>
                                <div class="mt-1 text-gray-700 whitespace-pre-line break-words">{{ $l->jawaban }}</div>
                                @if ($l->produk)
                                    <div class="mt-1 text-xs text-gray-500">Produk: {{ $l->produk->nama_produk }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Tabel di layar lebar --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="text-left text-gray-500">
                                <tr>
                                    <th class="py-2 pe-4">Waktu</th>
                                    <th class="py-2 pe-4">Pengguna</th>
                                    <th class="py-2 pe-4">Pertanyaan</th>
                                    <th class="py-2 pe-4">Jawaban</th>
                                    <th class="py-2">Produk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 align-top">
                                @foreach ($log as $l)
                                    <tr>
                                        <td class="py-2 pe-4 whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-2 pe-4">{{ $l->user?->name ?? 'Tamu' }}</td>
                                        <td class="py-2 pe-4 break-words">{{ $l->pertanyaan }}</td>
                                        <td class="py-2 pe-4 whitespace-pre-line break-words">{{ $l->jawaban }}</td>
                                        <td class="py-2">{{ $l->produk?->nama_produk ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $log->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
