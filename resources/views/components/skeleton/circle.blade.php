@props([
    'size' => 'w-12 h-12',
])

<div {{ $attributes->merge(['class' => 'skeleton shrink-0 ' . $size]) }} aria-hidden="true"></div>
