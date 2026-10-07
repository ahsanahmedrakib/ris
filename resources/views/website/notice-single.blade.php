@extends('layouts.website')

@section('content')

    @php
        $categoryLabel = \App\Models\Notice::CATEGORIES[$notice->category ?? 'general'] ?? 'সাধারণ';
    @endphp

    {{-- Hero --}}
    <section class="relative bg-cover bg-center overflow-hidden py-16 sm:py-20"
        style="background-image: url('{{ asset('assets/banner.png') }}')">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light opacity-90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span class="badge badge-warning">{{ $categoryLabel }}</span>
            <h1 class="mt-4 font-heading font-bold text-2xl sm:text-3xl text-white leading-snug">{{ $notice->title }}</h1>
            <p class="mt-3 text-white/70">{{ ($notice->published_at ?? $notice->created_at)->format('d M, Y') }}</p>
        </div>
    </section>

    {{-- Notice --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6 sm:p-10 reveal">
                <div class="flex items-start gap-4">
                    <div
                        class="w-14 h-14 shrink-0 rounded-2xl bg-ris-primary/10 flex items-center justify-center">
                        <svg class="w-7 h-7 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-gray-600 leading-relaxed prose prose-sm max-w-none">{!! \App\Support\HtmlSanitizer::clean($notice->content) !!}</div>

                        @if ($notice->expires_at)
                            <div class="mt-6 flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                মেয়াদোত্তীর্ণ: {{ $notice->expires_at->format('d M, Y') }}
                            </div>
                        @endif

                        @if ($notice->publisher)
                            <p class="mt-6 pt-5 border-t border-gray-100 text-sm text-gray-500">
                                প্রকাশক: <span class="font-medium text-gray-700">{{ $notice->publisher->name }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('notices') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-ris-primary text-white font-heading font-medium text-sm hover:bg-ris-dark shadow-lg shadow-ris-primary/25 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    সব নোটিশ দেখুন
                </a>
            </div>

            @if ($related->isNotEmpty())
                <div class="mt-14">
                    <h2 class="font-heading font-bold text-xl text-ris-dark mb-5">অন্যান্য নোটিশ</h2>
                    <div class="space-y-3.5 reveal-stagger">
                        @foreach ($related as $item)
                            <a href="{{ route('notices.single', $item->slug) }}"
                                class="block bg-white rounded-2xl p-4 shadow-card hover:shadow-card-hover transition-all duration-300 border border-gray-100 hover:border-ris-primary/20 reveal">
                                <h3 class="font-heading font-semibold text-ris-dark leading-snug">{{ $item->title }}</h3>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ ($item->published_at ?? $item->created_at)->format('d M, Y') }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection