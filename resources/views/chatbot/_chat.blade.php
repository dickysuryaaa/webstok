<div x-data="{
        pesan: '',
        kirim: false,
        daftar: [{ dari: 'bot', teks: 'Halo! Ketik nama barang untuk cek stok, contoh: &quot;stok kopi&quot;.' }],
        gulir() { this.$nextTick(() => { this.$refs.daftar.scrollTop = this.$refs.daftar.scrollHeight; }); },
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
                this.daftar.push({ dari: 'bot', teks: balasan });
            } catch (e) {
                this.daftar.push({ dari: 'bot', teks: 'Tidak dapat terhubung ke server.' });
            } finally {
                this.kirim = false;
                this.gulir();
                this.$nextTick(() => this.$refs.input.focus());
            }
        }
     }"
     class="flex flex-col h-[70vh] min-h-[320px]">
    <div x-ref="daftar" class="flex-1 overflow-y-auto space-y-2 p-3 bg-gray-50 rounded-md" aria-live="polite">
        <template x-for="(p, i) in daftar" :key="i">
            <div class="flex" :class="p.dari === 'saya' ? 'justify-end' : 'justify-start'">
                <div class="max-w-[85%] px-3 py-2 rounded-lg text-sm whitespace-pre-line break-words"
                     :class="p.dari === 'saya' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-800'"
                     x-text="p.teks"></div>
            </div>
        </template>
        <div x-show="kirim" class="text-xs text-gray-500">Mengetik...</div>
    </div>

    <form @submit.prevent="submit" class="mt-3 flex gap-2">
        <input x-ref="input" x-model="pesan" type="text" maxlength="255" autocomplete="off"
               placeholder="Tanya stok barang..."
               class="flex-1 min-h-[44px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <button type="submit" :disabled="kirim"
                class="min-h-[44px] px-4 bg-indigo-600 text-white rounded-md font-semibold text-sm disabled:opacity-50">
            Kirim
        </button>
    </form>
</div>
