@extends('layouts.website')

@section('title', 'রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- ═══ Hero Banner (green.edu.bd style) ═══ --}}
    <section class="hero-banner-section relative overflow-hidden">
        <div class="swiper hero-swiper animate-fade-in">
            <div class="swiper-wrapper">
                @php
                    $heroSlides = [
                        [
                            'image' => 'images/home/hero-1.jpg',
                            'title' => 'শিক্ষাই আলোকিত ভবিষ্যতের পথ',
                            'desc' => 'রেশমা ইন্টারন্যাশনাল স্কুলে আপনার সন্তানের জন্য সেরা শিক্ষা অভিজ্ঞতা।',
                            'btn' => 'ভর্তি করুন',
                            'link' => 'admission',
                        ],
                        [
                            'image' => 'images/home/hero-2.jpg',
                            'title' => 'আধুনিক শিক্ষা পদ্ধতি',
                            'desc' => 'ডিজিটাল ক্লাসরুম ও ইন্টারঅ্যাক্টিভ লার্নিংয়ের মাধ্যমে শিক্ষা।',
                            'btn' => 'আমাদের সম্পর্কে',
                            'link' => 'about',
                        ],
                        [
                            'image' => 'images/home/hero-3.jpg',
                            'title' => 'নিরাপদ ও আরামদায়ক পরিবেশ',
                            'desc' => 'প্রতিটি শিশুর নিরাপত্তা ও সুস্বাস্থ্য আমাদের অগ্রাধিকার।',
                            'btn' => 'যোগাযোগ করুন',
                            'link' => 'contact',
                        ],
                        [
                            'image' => 'images/home/hero-4.jpg',
                            'title' => 'মেধাবৃত্তি ও বৃত্তি সুবিধা',
                            'desc' => 'প্রতিভাবান শিক্ষার্থীদের জন্য বিশেষ মেধাবৃত্তি কার্যক্রম।',
                            'btn' => 'মেধাবৃত্তি',
                            'link' => 'scholarship',
                        ],
                    ];
                @endphp
                @foreach ($heroSlides as $slide)
                    <div class="swiper-slide relative">
                        <img src="{{ asset($slide['image']) }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                            class="absolute inset-0 w-full h-full object-cover">
                        {{-- Green gradient overlay (green.edu.bd style) --}}
                        <div class="hero-overlay absolute inset-0 flex items-center">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                                <div class="max-w-xl">
                                    <h2
                                        class="font-heading font-bold text-3xl sm:text-4xl lg:text-5xl text-white leading-tight drop-shadow-lg">
                                        {{ $slide['title'] }}
                                    </h2>
                                    <hr class="w-20 h-1 bg-white mt-4 mb-4 rounded">
                                    <p class="text-white/90 text-base sm:text-lg leading-relaxed drop-shadow">
                                        {{ $slide['desc'] }}
                                    </p>
                                    <a href="{{ route($slide['link']) }}"
                                        class="inline-flex items-center gap-2 mt-6 px-8 py-3 bg-white text-ris-primary font-heading font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg">
                                        {{ $slide['btn'] }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        {{-- Wave divider --}}
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
            <svg viewBox="0 0 1440 80" fill="none">
                <path d="M0 40C240 80 480 0 720 40C960 80 1200 0 1440 40V80H0V40Z" fill="white" />
            </svg>
        </div>
    </section>

    {{-- ═══ Statistics Counters (green.edu.bd style) ═══ --}}
    <section class="py-16 sm:py-20 bg-white relative overflow-hidden">
        {{-- Decorative background --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-ris-primary/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 w-48 h-48 bg-ris-primary/5 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            @php
                $stats = [
                    [
                        'value' => 500,
                        'suffix' => '+',
                        'label' => 'মোট ছাত্র-ছাত্রী',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    ],
                    [
                        'value' => 30,
                        'suffix' => '+',
                        'label' => 'শিক্ষকমণ্ডলী',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                    ],
                    [
                        'value' => 8,
                        'suffix' => '',
                        'label' => 'শ্রেণি',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                    ],
                    [
                        'value' => 11,
                        'suffix' => '+',
                        'label' => 'বছরের অভিজ্ঞতা',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 reveal-stagger">
                @foreach ($stats as $stat)
                    <div
                        class="stat-card text-center p-6 sm:p-8 rounded-2xl bg-white border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 reveal">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-ris-primary/10 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $stat['icon'] !!}
                            </svg>
                        </div>
                        <div class="font-heading font-bold text-3xl sm:text-4xl text-gradient">
                            <span data-count="{{ $stat['value'] }}">০</span>{{ $stat['suffix'] }}
                        </div>
                        <div class="mt-2 text-sm sm:text-base text-ris-gray font-medium">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ Message Section (green.edu.bd style — portraits + quotes) ═══ --}}
    <section class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">বার্তা</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">চেয়ারপার্সন ও প্রধান শিক্ষকের
                    বাণী</h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 reveal-stagger">
                {{-- Chairman --}}
                <div
                    class="message-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 reveal">
                    <div class="flex flex-col sm:flex-row">
                        <div class="sm:w-1/3 bg-ris-primary/5 flex items-center justify-center p-8">
                            <img src="{{ asset('images/home/ra.jpg') }}" alt="রেশমা আকতার" class="w-32 h-32 rounded-full object-cover">
                        </div>
                        <div class="sm:w-2/3 p-6 sm:p-8">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-ris-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                                </svg>
                                <h3 class="font-heading font-bold text-lg text-ris-dark">চেয়ারপার্সনের বাণী</h3>
                            </div>
                            <p class="text-gray-600 leading-relaxed text-sm italic">"প্রিয় অভিভাবকগণ, আমরা বিশ্বাস করি
                                প্রতিটি শিশু অসাধারণ সম্ভাবনায় পূর্ণ। রেশমা ইন্টারন্যাশনাল স্কুল আপনার সন্তানের এই
                                সম্ভাবনাকে সমৃদ্ধ করতে প্রতিশ্রুতিবদ্ধ।"</p>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="font-heading font-bold text-ris-dark">রেশমা আকতার</p>
                                <p class="text-xs text-gray-500">চেয়ারপার্সন, রেশমা ইন্টারন্যাশনাল স্কুল</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Head Teacher --}}
                <div
                    class="message-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 reveal">
                    <div class="flex flex-col sm:flex-row">
                        <div class="sm:w-1/3 bg-ris-primary/5 flex items-center justify-center p-8">
                            <img src="{{ asset('images/home/si.jpg') }}" alt="সাইফুল ইসলাম" class="w-32 h-32 rounded-full object-cover">
                        </div>
                        <div class="sm:w-2/3 p-6 sm:p-8">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-ris-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                                </svg>
                                <h3 class="font-heading font-bold text-lg text-ris-dark">প্রধান শিক্ষকের বাণী</h3>
                            </div>
                            <p class="text-gray-600 leading-relaxed text-sm italic">"প্রিয় শিক্ষার্থী ও অভিভাবকগণ, শিক্ষা
                                হলো আলোর পথ যা জীবনকে আলোকিত করে। আমাদের স্কুলে আমরা আধুনিক শিক্ষা পদ্ধতির পাশাপাশি নৈতিক
                                মূল্যবোধ ও সুশিক্ষার পরিবেশ তৈরি করেছি।"</p>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="font-heading font-bold text-ris-dark">সাইফুল ইসলাম</p>
                                <p class="text-xs text-gray-500">প্রধান শিক্ষক, রেশমা ইন্টারন্যাশনাল স্কুল</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ Programs / Departments (green.edu.bd faculty flip card style) ═══ --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">আমাদের
                    প্রোগ্রাম</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">শিক্ষার ধাপসমূহ</h2>
                <p class="mt-3 text-gray-500">প্রতিটি ধাপে আমরা ছাত্রদের জন্য সেরা শিক্ষা অভিজ্ঞতা নিশ্চিত করি।</p>
            </div>

            @php
                $programs = [
                    [
                        'title' => 'প্লে গ্রুপ',
                        'range' => 'প্লে',
                        'desc' => 'খেলাধুলা ও আনন্দের মাধ্যমে শিশুর প্রথম পাঠ।',
                        'color' => 'from-pink-500 to-rose-500',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'title' => 'নার্সারি',
                        'range' => 'নার্সারি',
                        'desc' => 'শিশুর প্রস্তুতি, ভাষা ও মৌলিক দক্ষতা গড়ে তোলা।',
                        'color' => 'from-blue-500 to-indigo-500',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                    ],
                    [
                        'title' => 'কেজি',
                        'range' => 'কেজি',
                        'desc' => 'প্রাথমিক পাঠের প্রস্তুতি ও মৌলিক দক্ষতা গড়ে তোলা।',
                        'color' => 'from-emerald-500 to-teal-500',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                    ],
                    [
                        'title' => 'প্রাথমিক শিক্ষা',
                        'range' => '১ম — ৫ম শ্রেণি',
                        'desc' => '১ম থেকে ৫ম শ্রেণি পর্যন্ত মৌলিক শিক্ষা।',
                        'color' => 'from-amber-500 to-orange-500',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v9"/>',
                    ],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                @foreach ($programs as $program)
                    <div
                        class="program-card group relative bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-2 reveal">
                        {{-- Card front --}}
                        <div
                            class="relative h-48 bg-linear-to-br {{ $program['color'] }} flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <svg class="w-16 h-16 text-white relative z-10 drop-shadow-lg" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                {!! $program['icon'] !!}
                            </svg>
                            <div class="absolute bottom-0 left-0 right-0 h-16 bg-linear-to-t from-white to-transparent">
                            </div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="font-heading font-bold text-lg text-ris-dark">{{ $program['title'] }}</h3>
                            <p class="text-xs font-heading text-ris-primary font-semibold mt-1">{{ $program['range'] }}
                            </p>
                            <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $program['desc'] }}</p>
                            <a href="{{ route('admission') }}"
                                class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
                                বিস্তারিত
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ Promo / Video Section (green.edu.bd style) ═══ --}}
    <section class="promo-section py-16 sm:py-20 bg-ris-dark overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Text --}}
                <div class="reveal-left">
                    <span class="text-ris-light font-heading font-semibold text-sm uppercase tracking-wider">আমাদের
                        সম্পর্কে</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl lg:text-4xl text-white leading-tight">
                        শিক্ষার মাধ্যমে
                        <span class="text-ris-light">আলোকিত ভবিষ্যত</span>
                        গড়ে তুলুন
                    </h2>
                    <p class="mt-5 text-white/70 leading-relaxed">
                        রেশমা ইন্টারন্যাশনাল স্কুল গোপালগঞ্জের ঘুল্লিবাড়ি মোড় ৪৩৯ নং অবস্থিত একটি আধুনিক শিক্ষাপ্রতিষ্ঠান।
                        আমরা বিশ্বাস করি প্রতিটি শিশু অসাধারণ সম্ভাবনায় পূর্ণ।
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('admission') }}" class="btn-primary">
                            ভর্তি করুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="{{ route('about') }}"
                            class="btn-secondary border-white/30 text-white hover:bg-white/10">
                            আরও জানুন
                        </a>
                    </div>
                </div>

                {{-- Video placeholder --}}
                <div class="relative reveal-right">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-video bg-ris-dark-700 flex items-center justify-center group cursor-pointer"
                        x-data="{ playing: false }">
                        {{-- Thumbnail placeholder --}}
                        <div
                            class="absolute inset-0 bg-linear-to-br from-ris-primary/20 to-ris-dark flex items-center justify-center">
                            <div class="text-center">
                                <div
                                    class="play-btn-circle w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto group-hover:bg-ris-primary/80 transition-all duration-300">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                                <p class="text-white/70 mt-4 text-sm font-heading">ভিডিও দেখুন</p>
                            </div>
                        </div>
                    </div>
                    {{-- Decorative element --}}
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-ris-primary/20 rounded-2xl -z-10"></div>
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-ris-light/20 rounded-full -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ Campus Life Section (green.edu.bd style) ═══ --}}
    <section class="py-16 sm:py-20 campus-life-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                {{-- Left decorative area --}}
                <div class="lg:col-span-4 hidden lg:block">
                    <div class="relative">
                        <div
                            class="w-full h-80 rounded-2xl bg-linear-to-br from-ris-primary/10 to-ris-primary/5 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-24 h-24 text-ris-primary/20 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-ris-primary/40 font-heading font-semibold mt-4">ক্যাম্পাস লাইফ</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: News carousel --}}
                <div class="lg:col-span-8">
                    <div class="mb-8 reveal">
                        <span
                            class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ক্যাম্পাস
                            কার্যক্রম</span>
                        <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">ক্যাম্পাস লাইফ</h2>
                    </div>

                    <div class="swiper campus-life-swiper reveal">
                        <div class="swiper-wrapper">
                            @php
                                $campusNews = [
                                    [
                                        'title' => 'বিজ্ঞান ও প্রযুক্তি মেলা ২০২৬ অনুষ্ঠিত',
                                        'date' => '৩১ আগস্ট, ২০২৬',
                                        'placeholder' => true,
                                    ],
                                    [
                                        'title' => 'আন্তর্জাতিক ভাষা দিবস পালন',
                                        'date' => '২৫ আগস্ট, ২০২৬',
                                        'placeholder' => true,
                                    ],
                                    [
                                        'title' => 'ক্রীড়া প্রতিযোগিতা ২০২৬',
                                        'date' => '১৭ আগস্ট, ২০২৬',
                                        'placeholder' => true,
                                    ],
                                    [
                                        'title' => 'সাংস্কৃতিক অনুষ্ঠান — আমার সোনার বাংলা',
                                        'date' => '১০ আগস্ট, ২০২৬',
                                        'placeholder' => true,
                                    ],
                                ];
                            @endphp
                            @foreach ($campusNews as $news)
                                <div class="swiper-slide">
                                    <div
                                        class="campus-life-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-full">
                                        <div
                                            class="h-48 bg-linear-to-br from-ris-primary/10 to-ris-primary/5 flex items-center justify-center relative">
                                            <svg class="w-16 h-16 text-ris-primary/20" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <div class="absolute top-4 left-4 badge">{{ $news['date'] }}</div>
                                        </div>
                                        <div class="p-5">
                                            <h4 class="font-heading font-semibold text-ris-dark leading-snug">
                                                {{ $news['title'] }}</h4>
                                            <hr class="my-3 border-gray-100">
                                            <a href="#"
                                                class="inline-flex items-center gap-1 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
                                                বিস্তারিত পড়ুন
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination mt-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ Events Section (green.edu.bd style) ═══ --}}
    <section class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-12 reveal">
                <div>
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">আসন্ন
                        অনুষ্ঠান</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">ইভেন্টসমূহ</h2>
                </div>
                <a href="#"
                    class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
                    সব ইভেন্ট
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            @php
                $events = [
                    [
                        'title' => 'বার্ষিক ক্রীড়া প্রতিযোগিতা ২০২৬',
                        'desc' => 'সকল শ্রেণির ছাত্র-ছাত্রীদের জন্য বার্ষিক ক্রীড়া প্রতিযোগিতার আয়োজন।',
                        'date' => '২৩ আগস্ট, ২০২৬',
                        'badge' => '',
                        'image' => null,
                        'featured' => true,
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                    ],
                    [
                        'title' => 'বিজ্ঞান মেলা ও প্রদর্শনী',
                        'desc' => 'ছাত্রদের বৈজ্ঞানিক গবেষণা ও উদ্ভাবন প্রদর্শনের আয়োজন।',
                        'date' => '১৬ আগস্ট, ২০২৬',
                        'badge' => 'badge-success',
                        'image' => null,
                        'featured' => false,
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />',
                    ],
                    [
                        'title' => 'সাংস্কৃতিক অনুষ্ঠান — আমার সোনার বাংলা',
                        'desc' => 'ভাষা ও সংস্কৃতি উৎসবে ছাত্রদের সাংস্কৃতিক প্রদর্শনী।',
                        'date' => '১০ আগস্ট, ২০২৬',
                        'badge' => 'badge-warning',
                        'image' => null,
                        'featured' => false,
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                    ],
                ];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                @foreach ($events as $event)
                    {{-- Card --}}
                    <div
                        class="event-card {{ $event['featured'] ? 'md:col-span-2 lg:col-span-1' : '' }} bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 group h-full reveal">
                        <div
                            class="relative h-48 bg-linear-to-br from-ris-primary/10 to-ris-primary/5 flex items-center justify-center overflow-hidden">
                            @if ($event['image'])
                                <img src="{{ asset($event['image']) }}" alt="{{ $event['title'] }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <svg class="w-16 h-16 text-ris-primary/15 group-hover:scale-110 transition-transform duration-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $event['icon'] !!}
                                </svg>
                            @endif
                            <div class="absolute top-4 left-4 badge {{ $event['badge'] }}">{{ $event['date'] }}</div>
                        </div>
                        <div class="p-6">
                            <h4
                                class="font-heading font-semibold text-ris-dark leading-snug group-hover:text-ris-primary transition-colors">
                                {{ $event['title'] }}
                            </h4>
                            <p class="mt-2 text-sm text-gray-500">{{ $event['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-8 sm:hidden">
                <a href="#" class="btn-secondary">সব ইভেন্ট দেখুন
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══ Notice Board (improved design) ═══ --}}
    <section class="py-16 sm:py-24 bg-white relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute top-0 left-0 w-80 h-80 bg-ris-primary/5 rounded-full -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-ris-light/5 rounded-full translate-x-1/3 translate-y-1/3 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-14">

                {{-- ═══ Left: Heading + Featured notice ═══ --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-28 reveal-left">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-12 h-12 rounded-2xl gradient-logo flex items-center justify-center shadow-lg shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </span>
                            <div>
                                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">সর্বশেষ ঘোষণা</span>
                                <h2 class="font-heading font-bold text-2xl sm:text-3xl text-ris-dark leading-tight">নোটিশ বোর্ড</h2>
                            </div>
                        </div>
                        <p class="text-gray-500 mt-4 leading-relaxed">
                            স্কুলের সকল গুরুত্বপূর্ণ ঘোষণা, সময়সূচি ও তথ্য নিয়মিতভাবে এখানে প্রকাশ করা হয়।
                        </p>

                        {{-- Featured / Important notice --}}
                        <div class="featured-notice-card mt-8 rounded-2xl overflow-hidden shadow-card group relative">
                            <div class="relative h-40 bg-linear-to-br from-ris-dark to-ris-primary flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/20 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                <div class="absolute top-4 left-4 badge badge-warning">গুরুত্বপূর্ণ</div>
                            </div>
                            <div class="bg-white p-5 border border-t-0 border-gray-100">
                                <h4 class="font-heading font-semibold text-ris-dark leading-snug">২০২৬ শিক্ষাবর্ষের ভর্তি প্রক্রিয়া চলমান</h4>
                                <hr class="my-3 border-gray-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        ১৫ জানুয়ারি, ২০২৬
                                    </span>
                                    <a href="{{ route('admission') }}" class="text-xs font-heading font-semibold text-ris-primary hover:text-ris-dark transition-colors inline-flex items-center gap-1">
                                        বিস্তারিত
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ Right: Notice list with tabs ═══ --}}
                <div class="lg:col-span-8">
                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 sm:p-8 reveal-right shadow-sm"
                        x-data="{ active: 'all' }">
                        {{-- Tabs --}}
                        <div class="flex flex-wrap items-center gap-2 mb-6">
                            @php
                                $noticeTabs = [
                                    ['key' => 'all', 'label' => 'সব নোটিশ'],
                                    ['key' => 'admission', 'label' => 'ভর্তি'],
                                    ['key' => 'exam', 'label' => 'পরীক্ষা'],
                                    ['key' => 'holiday', 'label' => 'ছুটি'],
                                    ['key' => 'general', 'label' => 'সাধারণ'],
                                ];
                            @endphp
                            @foreach ($noticeTabs as $tab)
                                <button type="button" @click="active = '{{ $tab['key'] }}'"
                                    class="px-4 py-2 rounded-xl text-sm font-heading font-medium transition-all duration-200"
                                    :class="active === '{{ $tab['key'] }}'
                                        ? 'bg-ris-primary text-white shadow-lg shadow-ris-primary/25'
                                        : 'bg-white text-gray-600 hover:text-ris-primary border border-gray-200 hover:border-ris-primary/30'">
                                    {{ $tab['label'] }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Notice items --}}
                        <div class="space-y-3.5 reveal-stagger">
                            @php
                                $notices = [
                                    ['title' => '২০২৬ শিক্ষাবর্ষের ভর্তি প্রক্রিয়া চলমান', 'date' => '১৫ জানুয়ারি, ২০২৬', 'category' => 'admission', 'category_label' => 'ভর্তি', 'highlight' => true],
                                    ['title' => 'বার্ষিক পরীক্ষার সময়সূচি প্রকাশিত', 'date' => '১০ জানুয়ারি, ২০২৬', 'category' => 'exam', 'category_label' => 'পরীক্ষা'],
                                    ['title' => 'ঈদুল ফিতরের ছুটি ঘোষণা', 'date' => '৫ জানুয়ারি, ২০২৬', 'category' => 'holiday', 'category_label' => 'ছুটি'],
                                    ['title' => 'অভিভাবক সমাবেশ ২০২৬ — তারিখ নির্ধারিত', 'date' => '২৮ ডিসেম্বর, ২০২৫', 'category' => 'general', 'category_label' => 'সাধারণ'],
                                    ['title' => 'নতুন শিক্ষাবর্ষের ক্লাস শুরু সংক্রান্ত বিজ্ঞপ্তি', 'date' => '২০ ডিসেম্বর, ২০২৫', 'category' => 'general', 'category_label' => 'সাধারণ'],
                                ];
                            @endphp
                            @foreach ($notices as $index => $notice)
                                <div class="notice-item bg-white rounded-2xl p-4 sm:p-5 flex items-center gap-4 shadow-card hover:shadow-card-hover transition-all duration-300 border border-gray-100 hover:border-ris-primary/20 reveal"
                                     x-show="active === 'all' || active === '{{ $notice['category'] }}'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-x-3"
                                     x-transition:enter-end="opacity-100 translate-x-0">
                                    {{-- Calendar-style date block --}}
                                    <div
                                        class="w-16 h-16 shrink-0 rounded-2xl flex flex-col items-center justify-center text-center {{ $notice['highlight'] ?? false ? 'gradient-logo text-white shadow-lg' : 'bg-ris-primary/5 text-ris-primary border border-ris-primary/10' }}">
                                        @php
                                            $dateParts = explode(' ', $notice['date']);
                                            $day = $dateParts[0] ?? '';
                                            $month = $dateParts[1] ?? '';
                                        @endphp
                                        <span class="font-heading font-bold text-xl leading-none">{{ $day }}</span>
                                        <span class="text-[10px] font-heading font-semibold mt-1 uppercase tracking-wide">{{ $month }}</span>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="text-[10px] font-heading font-semibold px-2.5 py-0.5 rounded-full bg-ris-primary/10 text-ris-primary">{{ $notice['category_label'] }}</span>
                                            @if ($notice['highlight'] ?? false)
                                                <span class="text-[10px] font-heading font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700">নতুন</span>
                                            @endif
                                        </div>
                                        <h4 class="mt-1.5 font-heading font-semibold text-sm sm:text-base text-ris-dark leading-snug truncate">
                                            {{ $notice['title'] }}
                                        </h4>
                                        <p class="mt-1 text-xs text-gray-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $notice['date'] }} · প্রকাশিত
                                        </p>
                                    </div>

                                    {{-- Arrow --}}
                                    <a href="{{ route('notices') }}"
                                        class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center transition-all duration-300 {{ $notice['highlight'] ?? false ? 'bg-ris-primary text-white hover:bg-ris-dark' : 'bg-gray-50 text-gray-400 hover:bg-ris-primary hover:text-white' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer button --}}
                        <div class="mt-7 text-center">
                            <a href="{{ route('notices') }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-ris-primary text-white font-heading font-medium text-sm hover:bg-ris-dark shadow-lg shadow-ris-primary/25 transition-all duration-300 hover:-translate-y-0.5">
                                সব নোটিশ দেখুন
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ CTA Section ═══ --}}
    <section class="py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h2 class="font-heading font-bold text-3xl sm:text-4xl text-white">আজই ভর্তি করুন</h2>
            <p class="mt-4 text-white/70 text-lg max-w-2xl mx-auto">আপনার সন্তানের উজ্জ্বল ভবিষ্যতের জন্য রেশমা
                ইন্টারন্যাশনাল স্কুলে ভর্তি প্রক্রিয়া চলমান। আজই যোগাযোগ করুন!</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('admission') }}"
                    class="btn-primary bg-white text-ris-primary hover:bg-gray-100 shadow-lg shadow-black/20 px-8 py-3">ভর্তি
                    ফরম পূরণ করুন</a>
                <a href="{{ route('contact') }}"
                    class="btn-secondary border-white/30 text-white hover:bg-white/10 px-8 py-3">যোগাযোগ করুন</a>
            </div>
        </div>
    </section>

@endsection
