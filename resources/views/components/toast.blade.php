@php
    // Status bawaan Breeze (mis. 'profile-updated') sudah punya tampilan sendiri di halaman profil.
    $statusBreeze = ['profile-updated', 'password-updated', 'verification-link-sent'];
    $pesanToast = [];
    foreach (['success' => 'success', 'status' => 'success', 'error' => 'error'] as $kunci => $jenis) {
        $isi = session($kunci);
        if (is_string($isi) && $isi !== '' && ! in_array($isi, $statusBreeze, true)) {
            $pesanToast[] = ['jenis' => $jenis, 'teks' => $isi];
        }
    }
@endphp

@if ($pesanToast)
    <div class="pointer-events-none fixed inset-x-0 top-3 z-[60] flex flex-col items-center gap-2 px-3 sm:top-4 sm:items-end sm:px-6" aria-live="polite">
        @foreach ($pesanToast as $t)
            @php $sukses = $t['jenis'] === 'success'; @endphp
            <div x-data="{ tampil: false }"
                 x-init="$nextTick(() => tampil = true); setTimeout(() => tampil = false, 4000)"
                 x-show="tampil"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 role="{{ $sukses ? 'status' : 'alert' }}"
                 class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border bg-white p-3 shadow-lg {{ $sukses ? 'border-emerald-200' : 'border-rose-200' }}">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $sukses ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    <x-icon :name="$sukses ? 'check' : 'alert'" />
                </span>
                <p class="min-w-0 flex-1 break-words py-1 text-sm text-slate-800">{{ $t['teks'] }}</p>
                <button type="button" @click="tampil = false" aria-label="Tutup"
                        class="-m-1 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-600">&times;</button>
            </div>
        @endforeach
    </div>
@endif
