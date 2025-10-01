@props(['title','value','icon' => 'fa-database','color' => 'blue'])

@php
$map = [
'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
'yellow' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
'green' => 'bg-green-50 text-green-700 border-green-200',
'red' => 'bg-red-50 text-red-700 border-red-200',
'teal' => 'bg-teal-50 text-teal-700 border-teal-200',
];
$cls = $map[$color] ?? $map['blue'];
@endphp

<div class="rounded-xl border p-4 {{ $cls }}">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white/60 rounded-lg">
            <i class="fas {{ $icon }}"></i>
        </div>
        <div>
            <div class="text-sm">{{ $title }}</div>
            <div class="text-2xl font-extrabold">{{ $value }}</div>
        </div>
    </div>
</div>