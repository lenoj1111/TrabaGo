@props([
'title'=>'',
'subtitle'=>''
])

<div class="bg-white rounded-xl border p-6 shadow-sm">

@if($title)

<h2 class="text-xl font-bold">

{{ $title }}

</h2>

@endif

@if($subtitle)

<p class="text-gray-500">

{{ $subtitle }}

</p>

@endif

<div class="mt-4">

{{ $slot }}

</div>

</div>