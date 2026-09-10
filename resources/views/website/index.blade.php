@extends('layouts.website')

@section('title', 'রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="swiper hero-swiper animate-fade-in">
            <div class="swiper-wrapper">
                @php
                    $slideImages = [
                        'images/home/hero-1.jpg',
                        'images/home/hero-2.jpg',
                        'images/home/hero-3.jpg',
                        'images/home/hero-4.jpg',
                        'images/home/hero-5.jpg',
                        'images/home/hero-6.jpg',
                        'images/home/hero-7.jpg',
                        'images/home/hero-8.jpg',
                        'images/home/hero-9.jpg',
                        'images/home/hero-10.jpg',
                    ];
                @endphp
                @foreach ($slideImages as $slideImage)
                    <div class="swiper-slide relative">
                        <img src="{{ asset($slideImage) }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                            class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-black/30 via-transparent to-transparent"></div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none"><svg viewBox="0 0 1440 80" fill="none">
                <path d="M0 40C240 80 480 0 720 40C960 80 1200 0 1440 40V80H0V40Z" fill="white" />
            </svg></div>
    </section>

    {{-- Stats --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $stats = [
                    ['value' => 500, 'suffix' => '+', 'label' => 'মোট ছাত্র-ছাত্রী'],
                    ['value' => 30, 'suffix' => '+', 'label' => 'শিক্ষক'],
                    ['value' => 8, 'suffix' => '', 'label' => 'শ্রেণি'],
                    ['value' => 11, 'suffix' => '+', 'label' => 'বছরের অভিজ্ঞতা'],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 reveal-stagger">
                @foreach ($stats as $stat)
                    <div class="text-center p-6 rounded-2xl bg-ris-dark-50/50 border border-ris-dark-100/50 reveal">
                        <div class="font-heading font-bold text-3xl sm:text-4xl text-gradient"><span
                                data-count="{{ $stat['value'] }}">০</span>{{ $stat['suffix'] }}</div>
                        <div class="mt-2 text-sm sm:text-base text-ris-gray font-medium">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- About --}}
    <section class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="reveal-left">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">আমাদের
                        সম্পর্কে</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark leading-tight">জ্ঞান ও
                        মূল্যবোধের সমন্বয়ে গড়ে উঠছে আগামীর প্রজন্ম</h2>
                    <p class="mt-5 text-gray-600 leading-relaxed">রেশমা ইন্টারন্যাশনাল স্কুল গোপালগঞ্জের ঘুল্লিবাড়ি মোড় ৪৩৯
                        নং অবস্থিত
                        একটি আধুনিক শিক্ষাপ্রতিষ্ঠান। আমরা বিশ্বাস করি প্রতিটি শিশু অসাধারণ সম্ভাবনায় পূর্ণ। আমাদের লক্ষ্য
                        শিক্ষার মাধ্যমে এই সম্ভাবনাকে সমৃদ্ধ করা।</p>
                    <p class="mt-4 text-gray-600 leading-relaxed">আমাদের অভিজ্ঞ শিক্ষকমণ্ডলী, আধুনিক শিক্ষা পদ্ধতি এবং
                        সুসজ্জিত পরিবেশ ছাত্রদের সর্বোত্তম শিক্ষা নিশ্চিত করে।</p>
                    <a href="{{ route('about') }}" class="btn-primary mt-8">বিস্তারিত জানুন <svg class="w-4 h-4"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg></a>
                </div>
                <div class="relative reveal-right animate-float-slow">
                    <div
                        class="rounded-2xl overflow-hidden flex items-center justify-center bg-white border border-gray-100 shadow-card p-10">
                        <img src="{{ asset('logo.png') }}" alt="Resma International School"
                            class="h-36 lg:h-44 w-auto object-contain">
                    </div>
                    <div class="absolute -bottom-8 -left-6 bg-white rounded-xl shadow-lg p-4 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center"><svg
                                class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <div class="font-heading font-bold text-ris-dark text-lg">১০০%</div>
                            <div class="text-xs text-gray-500">নিরাপদ পরিবেশ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Programs --}}
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
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'প্লে গ্রুপ',
                        'desc' => 'খেলাধুলা ও আনন্দের মাধ্যমে শিশুর প্রথম পাঠ।',
                        'range' => 'প্লে',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                        'title' => 'নার্সারি',
                        'desc' => 'শিশুর প্রস্তুতি, ভাষা ও মৌলিক দক্ষতা গড়ে তোলা।',
                        'range' => 'নার্সারি',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                        'title' => 'কেজী',
                        'desc' => 'প্রাথমিক পাঠের প্রস্তুতি ও মৌলিক দক্ষতা গড়ে তোলা।',
                        'range' => 'কেজী',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.825-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v9"/>',
                        'title' => 'প্রাথমিক শিক্ষা',
                        'desc' => '১ম থেকে ৫ম শ্রেণি পর্যন্ত মৌলিক শিক্ষা।',
                        'range' => '১ম — ৫ম শ্রেণি',
                    ],
                ];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 reveal-stagger">
                @foreach ($programs as $program)
                    <div class="card p-8 text-center group hover:-translate-y-1 reveal">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-ris-primary/10 flex items-center justify-center mb-5 group-hover:bg-ris-primary transition-all duration-300">
                            <svg class="w-8 h-8 text-ris-primary group-hover:text-white transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">{!! $program['icon'] !!}</svg>
                        </div>
                        <h3 class="font-heading font-bold text-lg text-ris-dark">{{ $program['title'] }}</h3>
                        <p class="mt-2 text-xs font-heading text-ris-primary font-medium">{{ $program['range'] }}</p>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $program['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">কেন আমাদের বেছে
                    নেবেন?</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আমাদের সুবিধাসমূহ</h2>
            </div>
            @php
                $features = [
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                        'title' => 'আধুনিক শিক্ষা পদ্ধতি',
                        'desc' => 'ইন্টারঅ্যাক্টিভ ক্লাসরুম ও ডিজিটাল লার্নিং।',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'title' => 'অভিজ্ঞ শিক্ষকমণ্ডলী',
                        'desc' => 'দক্ষ ও অভিজ্ঞ শিক্ষকগণ যারা শিশুদের ভালোবাসায় শেখান।',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        'title' => 'নিরাপদ পরিবেশ',
                        'desc' => 'সিসিটিভি নিয়ন্ত্রিত ও সম্পূর্ণ নিরাপদ স্কুল ক্যাম্পাস।',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
                        'title' => 'বিজ্ঞান ও প্রযুক্তি',
                        'desc' => 'আধুনিক ল্যাব ও কম্পিউটার ল্যাব।',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'খেলাধুলা ও সহপাঠক্রম',
                        'desc' => 'শারীরিক ও মানসিক বিকাশের জন্য বিভিন্ন ক্রীড়া ও সাংস্কৃতিক কার্যক্রম।',
                    ],
                    [
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'সাশ্রয়ী ফি কাঠামো',
                        'desc' => 'সকল পরিবারের জন্য সাশ্রয়ী মূল্যে মানসম্মত শিক্ষা।',
                    ],
                ];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                @foreach ($features as $feature)
                    <div class="card p-6 flex gap-4 reveal">
                        <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">{!! $feature['icon'] !!}</svg>
                        </div>
                        <div>
                            <h3 class="font-heading font-semibold text-ris-dark">{{ $feature['title'] }}</h3>
                            <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Notices --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">সর্বশেষ
                    ঘোষণা</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">নোটিশ বোর্ড</h2>
            </div>
            <div class="max-w-3xl mx-auto space-y-4 reveal-stagger">
                @forelse(['ভর্তি প্রক্রিয়া চলমান — ২০২৬ শিক্ষাবর্ষ', 'বার্ষিক পরীক্ষার সময়সূচি প্রকাশিত', 'ঈদের ছুটি ঘোষণা'] as $index => $notice)
                    <div class="card p-5 flex items-start gap-4 reveal">
                        <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-heading font-semibold text-ris-dark">{{ $notice }}</h4>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ ['১৫ জানুয়ারি, ২০২৬', '১০ জানুয়ারি, ২০২৬', '৫ জানুয়ারি, ২০২৬'][$index] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <p>কোনো নোটিশ নেই।</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('notices') }}" class="btn-secondary">সব নোটিশ দেখুন <svg class="w-4 h-4"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg></a>
            </div>
        </div>
    </section>

    {{-- CTA --}}
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
