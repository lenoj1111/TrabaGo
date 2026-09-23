@props(['percent' => 0])

<div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
    <div
        class="bg-green-600 h-2 rounded-full transition-all duration-300"
        style="width: {{ $percent }}%;">
    </div>
</div>