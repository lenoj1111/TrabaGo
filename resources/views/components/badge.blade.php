@props([
    'text',
    'tone' => 'brand'
])

@php
$colors = [
    'brand' => 'text-blue-700 bg-blue-100',
    'green' => 'text-green-700 bg-green-100',
    'gold' => 'text-yellow-700 bg-yellow-100',
    'red' => 'text-red-700 bg-red-100',
    'gray' => 'text-gray-700 bg-gray-100'
];
@endphp

<span class="text-xs font-semibold px-2 py-1 rounded {{ $colors[$tone] }}">
    {{ $text }}
</span>