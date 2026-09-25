@props([
    'lines' => 1,
    'width' => 'w-full',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'h-3',
        'md' => 'h-4',
        'lg' => 'h-5',
        'xl' => 'h-7',
    ];

    $barHeight = $sizes[$size] ?? $sizes['md'];
    $total = max((int) $lines, 1);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }} aria-hidden="true">
    @for ($i = 0; $i < $total; $i++)
        @php
            $barWidth = $total === 1
                ? $width
                : ($i === $total - 1 ? 'w-2/5' : $width);
        @endphp
        <div class="skeleton {{ $barHeight }} {{ $barWidth }}"></div>
    @endfor
</div>
