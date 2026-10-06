@extends('layouts.website')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span class="badge badge-warning">ক্যাম্পাস ইভেন্ট</span>
            <h1 class="mt-4 font-heading font-bold text-2xl sm:text-3xl text-white leading-snug">{{ $item->title }}</h1>
            @if ($item->date)
                <p class="mt-3 text-white/70">{{ $item->date->format('d M, Y') }}</p>
            @endif
        </div>
    </section>

    {{-- Item --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($item->image)
                <img src="{{ \App\Support\Media::images()->url($item->image) }}" alt="{{ $item->title }}"
                    class="w-full h-64 sm:h-80 object-cover rounded-2xl shadow-card mb-8 reveal">
            @endif

            @if ($item->description)
                <div class="card p-6 sm:p-10 text-gray-600 leading-relaxed prose prose-sm max-w-none reveal">
                    {!! \App\Support\HtmlSanitizer::clean($item->description) !!}
                </div>
            @endif

            <div class="mt-8 text-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-ris-primary text-white font-heading font-medium text-sm hover:bg-ris-dark shadow-lg shadow-ris-primary/25 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    হোমপেজে ফিরে যান
                </a>
            </div>

            @if ($related->isNotEmpty())
                <div class="mt-14">
                    <h2 class="font-heading font-bold text-xl text-ris-dark mb-5">আরও ক্যাম্পাস ইভেন্ট</h2>
                    <div class="grid sm:grid-cols-2 gap-5 reveal-stagger">
                        @foreach ($related as $other)
                            <a href="{{ route('campus-events.single', $other->slug) }}"
                                class="bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 border border-gray-100 hover:border-ris-primary/20 reveal">
                                <div class="h-40 relative flex items-center justify-center">
                                    @if ($other->image)
                                        <img src="{{ \App\Support\Media::images()->url($other->image) }}" loading="lazy" decoding="async"
                                            alt="{{ $other->title }}"
                                            class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <div
                                            class="absolute inset-0 bg-linear-to-br from-ris-primary/10 to-ris-primary/5 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-ris-primary/20" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h3 class="font-heading font-semibold text-ris-dark leading-snug">{{ $other->title }}</h3>
                                    @if ($other->date)
                                        <p class="mt-1 text-xs text-gray-400">{{ $other->date->format('d M, Y') }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection