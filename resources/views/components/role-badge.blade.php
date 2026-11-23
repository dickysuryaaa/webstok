@props(['pemilik' => false])

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset '.($pemilik ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-700 ring-slate-500/20')]) }}>
    {{ $pemilik ? 'Pemilik' : 'Pekerja' }}
</span>
