@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-3.5 py-2.5 bg-white border border-gray-200 focus:border-green-600 focus:ring-1 focus:ring-green-600 rounded-lg text-xs text-gray-900 outline-none transition-colors placeholder:text-gray-400']) }}>
