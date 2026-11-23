<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Log Chatbot</h2>
            <p class="mt-0.5 text-sm text-slate-500 tabular-nums">{{ $log->total() }} percakapan tercatat</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if ($log->isEmpty())
                <div class="bg-white border border-slate-200/80 shadow-sm rounded-xl p-8 flex flex-col items-center gap-2 text-center text-sm text-slate-500">
                    <x-icon name="chat" class="h-8 w-8 text-slate-300" />
                    Belum ada percakapan.
                </div>
            @else
                {{-- Kartu di layar sempit --}}
                <ul class="space-y-3 md:hidden">
                    @foreach ($log as $l)
                        <li class="bg-white border border-slate-200/80 shadow-sm rounded-xl p-4 text-sm">
                            <div class="flex items-center justify-between gap-2 text-xs text-slate-500">
                                <span class="truncate font-medium text-slate-700">{{ $l->user?->name ?? 'Tamu' }}</span>
                                <span class="shrink-0 tabular-nums">{{ $l->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <div class="max-w-[85%] rounded-2xl rounded-br-md bg-emerald-600 px-3 py-2 text-white break-words">{{ $l->pertanyaan }}</div>
                            </div>
                            <div class="mt-2 flex">
                                <div class="max-w-[85%] rounded-2xl rounded-bl-md border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 whitespace-pre-line break-words tabular-nums">{{ $l->jawaban }}</div>
                            </div>
                            @if ($l->produk)
                                <div class="mt-2 text-xs text-slate-500">Produk: <span class="font-medium text-slate-700">{{ $l->produk->nama_produk }}</span></div>
                            @endif
                        </li>
                    @endforeach
                </ul>

                {{-- Tabel di layar lebar --}}
                <div class="hidden md:block bg-white border border-slate-200/80 shadow-sm rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3">Pertanyaan</th>
                                    <th class="px-4 py-3">Jawaban</th>
                                    <th class="px-4 py-3">Produk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 align-top">
                                @foreach ($log as $l)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-500 tabular-nums">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $l->user?->name ?? 'Tamu' }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-900 break-words">{{ $l->pertanyaan }}</td>
                                        <td class="px-4 py-3 text-slate-700 whitespace-pre-line break-words tabular-nums">{{ $l->jawaban }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $l->produk?->nama_produk ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>{{ $log->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
