@props([
'name',
'label',
'type'=>'text',
'value'=>''
])

<label class="block">

    <span class="text-sm font-semibold">
        {{ $label }}
    </span>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name,$value) }}"
        {{ $attributes->merge([
            'class'=>'mt-2 w-full border rounded px-3 py-2'
        ]) }}
    >

</label>