{{--
    Testimonial card - floating avatar + colored footer bar design.
    Accent colors alternate between orange (#f28b4d) and magenta (#e6007e) across cards.
--}}
@php
    $accentThemes = [
        ['bg' => 'bg-[#f28b4d]', 'text' => 'text-[#f28b4d]', 'hex' => '#f28b4d'],
        ['bg' => 'bg-[#e6007e]', 'text' => 'text-[#e6007e]', 'hex' => '#e6007e'],
        ['bg' => 'bg-[#7c5cff]', 'text' => 'text-[#7c5cff]', 'hex' => '#7c5cff'],
        ['bg' => 'bg-[#12b8a6]', 'text' => 'text-[#12b8a6]', 'hex' => '#12b8a6'],
        ['bg' => 'bg-[#2f6fed]', 'text' => 'text-[#2f6fed]', 'hex' => '#2f6fed'],
        ['bg' => 'bg-[#22c55e]', 'text' => 'text-[#22c55e]', 'hex' => '#22c55e'],
    ];

    $cardIndex = isset($loop) ? $loop->index : 0;
    $accent = $accentThemes[$cardIndex % count($accentThemes)];
@endphp

<div
    class="relative bg-white rounded-2xl shadow-xl pt-12 pb-4 px-6 flex flex-col justify-between h-full">
    {{-- Floating Avatar --}}
    <div
        class="absolute -top-6 right-6 w-16 h-16 rounded-full {{ $accent['bg'] }} border-4 border-white flex items-center justify-center shadow-md overflow-hidden">
        @if ($testimonial->photo)
            <img src="{{ Storage::url($testimonial->photo) }}" alt="{{ $testimonial->name }}"
                class="w-full h-full rounded-full object-cover">
        @else
            <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24">
                <path
                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
            </svg>
        @endif
    </div>

    <div>
        {{-- Client Info --}}
        <h3 class="text-lg font-bold {{ $accent['text'] }}">{{ $testimonial->name }}</h3>
        @if ($testimonial->designation)
            <p class="text-xs text-gray-500 mb-3">{{ $testimonial->designation }}</p>
        @else
            <p class="mb-3"></p>
        @endif
        <hr class="border-gray-200 mb-4" />

        {{-- Review Text --}}
        <p class="text-xs text-gray-600 leading-relaxed line-clamp-6" title="{{ $testimonial->message }}">
            {{ $testimonial->message }}
        </p>
    </div>

    {{-- Footer Bar: colored wave (full-width bottom + left half) curving into white on the right --}}
    <div class="-mx-6 -mb-4 h-14 mt-6 relative overflow-hidden rounded-b-2xl">
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 220 50" preserveAspectRatio="none"
            aria-hidden="true">
            <path d="M0 0 H90 Q124 0 132 56 V56 H0 Z" fill="{{ $accent['hex'] }}" />
        </svg>
        <div class="relative h-full flex items-center justify-between px-6">
            <div class="flex space-x-1 shrink-0">
                @for ($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 fill-current {{ $i < $testimonial->rating ? 'text-white' : 'text-white/30' }}"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.787 1.399 8.168-7.333-3.854-7.333 3.854 1.399-8.168-5.934-5.787 8.2-1.192z" />
                    </svg>
                @endfor
            </div>
            <svg class="w-6 h-6 fill-current {{ $accent['text'] }} shrink-0" viewBox="0 0 24 24">
                <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z" />
            </svg>
        </div>
    </div>
</div>