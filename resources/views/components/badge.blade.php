@props([
    'text',
    'tone' => 'brand'
])

@php
$colors = [
    'brand' => 'text-green-700 bg-green-50 border border-green-200',
    'green' => 'text-green-700 bg-green-50 border border-green-200',
    'gold' => 'text-amber-700 bg-amber-50 border border-amber-200',
    'red' => 'text-red-700 bg-red-50 border border-red-200',
    'gray' => 'text-gray-700 bg-gray-100 border border-gray-200'
];
@endphp

<span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $colors[$tone] ?? $colors['brand'] }}">
    {{ $text }}
</span>