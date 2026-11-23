@props(['stok', 'ambang'])

@if ($stok === 0)
    <span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20']) }}>
        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>Habis
    </span>
@elseif ($stok <= $ambang)
    <span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-500/30']) }}>
        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Stok menipis
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20']) }}>
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Tersedia
    </span>
@endif
