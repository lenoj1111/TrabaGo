@props(['percent' => 0])

<div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
    <div
        class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500"
        style="width: {{ $percent }}%;">
    </div>
</div>