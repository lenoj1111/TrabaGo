@props([
'route',
'label',
'code'
])

<a
href="{{ route($route) }}"
class="border rounded-lg px-3 py-2">

<div class="text-xs text-gray-500">

{{ $code }}

</div>

<div>

{{ $label }}

</div>

</a>