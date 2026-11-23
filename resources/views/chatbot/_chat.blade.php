@php
    $chips = ['Halo', 'Cek stok'];
    if (! empty($contoh)) {
        $chips[] = 'stok '.$contoh;
    }
@endphp

<div x-data="{
        pesan: '',
        kirim: false,
        daftar: [{ dari: 'bot', teks: 'Halo! Ketik nama barang untuk cek stok, contoh: &quot;stok kopi&quot;.' }],
        gulir() { this.$nextTick(() => { const d = this.$refs.daftar; d.scrollTo({ top: d.scrollHeight, behavior: 'smooth' }); }); },
        tanya(teks) { this.pesan = teks; this.submit(); },
        async submit() {
            const teks = this.pesan.trim();
            if (teks === '' || this.kirim) { return; }
            this.daftar.push({ dari: 'saya', teks });
            this.pesan = '';
            this.kirim = true;
            this.gulir();
            try {
                const res = await fetch(@js(route('chatbot.pesan')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ pesan: teks }),
                });
                const data = await res.json().catch(() => ({}));
                const balasan = res.ok ? data.jawaban
                    : (data.errors?.pesan?.[0] ?? (res.status === 419 ? 'Sesi berakhir, muat ulang halaman.' : 'Terjadi kesalahan, coba lagi.'));
                this.daftar.push({ dari: 'bot', teks: balasan, galat: !res.ok });
            } catch (e) {
                this.daftar.push({ dari: 'bot', teks: 'Tidak dapat terhubung ke server.', galat: true });
            } finally {
                this.kirim = false;
                this.gulir();
                this.$nextTick(() => this.$refs.input.focus());
            }
        }
     }"
     class="flex flex-col {{ $tinggi ?? 'h-[70vh] min-h-[22rem]' }}">
    {{-- Daftar pesan --}}
    <div x-ref="daftar" class="flex-1 overflow-y-auto overscroll-contain space-y-3 p-3 sm:p-4 bg-slate-50" aria-live="polite">
        <template x-for="(p, i) in daftar" :key="i">
            <div class="flex items-end gap-2" :class="p.dari === 'saya' ? 'justify-end' : 'justify-start'">
                <template x-if="p.dari === 'bot'">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                        <x-icon name="bot" class="h-4 w-4" />
                    </span>
                </template>
                <div class="max-w-[80%] px-3.5 py-2.5 text-sm leading-relaxed whitespace-pre-line break-words tabular-nums shadow-sm"
                     :class="p.dari === 'saya'
                        ? 'bg-emerald-600 text-white rounded-2xl rounded-br-md'
                        : (p.galat ? 'bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl rounded-bl-md' : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-bl-md')"
                     x-text="p.teks"></div>
            </div>
        </template>

        {{-- Indikator mengetik --}}
        <div x-show="kirim" x-cloak class="flex items-end gap-2" aria-label="Bot sedang mengetik">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <x-icon name="bot" class="h-4 w-4" />
            </span>
            <div class="flex items-center gap-1 rounded-2xl rounded-bl-md border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:-0.3s]"></span>
                <span class="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:-0.15s]"></span>
                <span class="h-2 w-2 rounded-full bg-slate-400 animate-bounce"></span>
            </div>
        </div>
    </div>

    {{-- Pertanyaan cepat --}}
    <div class="flex gap-2 overflow-x-auto border-t border-slate-200/80 bg-white px-3 pt-3 [scrollbar-width:none]">
        @foreach ($chips as $chip)
            <button type="button" @click="tanya(@js($chip))" :disabled="kirim"
                    class="inline-flex shrink-0 items-center min-h-[44px] max-w-[14rem] px-3.5 rounded-full border border-emerald-200 bg-emerald-50 text-sm font-medium text-emerald-700 hover:bg-emerald-100 disabled:opacity-50">
                <span class="truncate">{{ $chip }}</span>
            </button>
        @endforeach
    </div>

    {{-- Input --}}
    <form @submit.prevent="submit" class="flex gap-2 bg-white p-3">
        <input x-ref="input" x-model="pesan" type="text" maxlength="255" autocomplete="off" enterkeyhint="send"
               placeholder="Tanya stok barang..." aria-label="Pesan"
               class="flex-1 min-w-0 min-h-[44px] rounded-full border-slate-300 px-4 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
        <button type="submit" :disabled="kirim || pesan.trim() === ''" aria-label="Kirim"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 disabled:bg-slate-300">
            <x-icon name="send" class="h-5 w-5" />
        </button>
    </form>
</div>
