@extends('layouts.website')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">ক্যাম্পাস ইভেন্ট</h1>
            <p class="mt-3 text-white/70 text-lg">স্কুলের কার্যক্রম, উৎসব ও স্মরণীয় মুহূর্তসমূহ</p>
        </div>
    </section>

    {{-- Items --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($items->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 reveal-stagger">
                    @foreach ($items as $item)
                        <a href="{{ route('campus-events.single', $item->slug) }}"
                            class="campus-events-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 border border-gray-100 hover:border-ris-primary/20 reveal">
                            <div class="h-48 relative flex items-center justify-center">
                                @if ($item->image)
                                    <img src="{{ \App\Support\Media::images()->url($item->image) }}"
                                        alt="{{ $item->title }}" loading="lazy" decoding="async"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div
                                        class="absolute inset-0 bg-linear-to-br from-ris-primary/10 to-ris-primary/5 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-ris-primary/20" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                @if ($item->date)
                                    <div class="absolute top-4 left-4 badge">{{ $item->date->format('d M, Y') }}</div>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-heading font-semibold text-ris-dark leading-snug">{{ $item->title }}</h3>
                                <span
                                    class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-ris-primary transition-colors">
                                    বিস্তারিত পড়ুন
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">কোনো কার্যক্রম নেই</h3>
                    <p class="mt-2 text-gray-500">বর্তমানে কোনো ক্যাম্পাস কার্যক্রম প্রকাশিত হয়নি।</p>
                </div>
            @endif
        </div>
    </section>

@endsection