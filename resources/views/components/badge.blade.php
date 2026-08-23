@props([
    'text',
    'tone' => 'brand'
])

@php
$colors = [
    'brand' => 'text-emerald-800 bg-emerald-50 border border-emerald-200/60',
    'green' => 'text-emerald-800 bg-emerald-100 border border-emerald-200',
    'gold' => 'text-amber-800 bg-amber-50 border border-amber-200',
    'red' => 'text-rose-800 bg-rose-50 border border-rose-200',
    'gray' => 'text-slate-700 bg-slate-100 border border-slate-200'
];
@endphp

<span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $colors[$tone] ?? $colors['brand'] }}">
    {{ $text }}
</span>