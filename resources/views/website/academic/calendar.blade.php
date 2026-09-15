@extends('layouts.website')

@section('title', 'একাডেমিক ক্যালেন্ডার — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">একাডেমিক ক্যালেন্ডার</h1>
            <p class="mt-3 text-white/70 text-lg">পরীক্ষা, ছুটি ও গুরুত্বপূর্ণ তারিখসমূহ</p>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($calendars->count())
                <div class="space-y-6 reveal-stagger">
                    @foreach ($calendars as $calendar)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden group reveal">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-6 sm:p-8">
                                <div class="w-14 h-14 shrink-0 rounded-2xl bg-ris-primary/10 flex items-center justify-center group-hover:bg-ris-primary transition-all duration-300">
                                    <svg class="w-7 h-7 text-ris-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-heading font-bold text-lg text-ris-dark">{{ $calendar->title }}</h3>
                                        @if ($calendar->year)
                                            <span class="px-2.5 py-0.5 bg-ris-primary/10 text-ris-primary text-xs font-medium rounded-full">{{ $calendar->year }}</span>
                                        @endif
                                    </div>
                                    @if ($calendar->description)
                                        <p class="mt-1 text-sm text-gray-500">{{ $calendar->description }}</p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-400">আপলোড: {{ $calendar->created_at->format('d M, Y') }}</p>
                                </div>
                                <a href="{{ Storage::url($calendar->file_path) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    দেখুন / ডাউনলোড
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো ক্যালেন্ডার নেই</h3>
                    <p class="mt-2 text-gray-500">শীঘ্রই একাডেমিক ক্যালেন্ডার আপলোড করা হবে।</p>
                </div>
            @endif
        </div>
    </section>

@endsection