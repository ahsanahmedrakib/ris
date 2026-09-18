@extends('layouts.website')

@section('title', 'রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- ═══ Hero Banner (green.edu.bd style) ═══ --}}
    <section class="hero-banner-section relative overflow-hidden">
        <div class="swiper hero-swiper animate-fade-in">
            <div class="swiper-wrapper">
                @foreach ($heroSlides as $slide)
                    <div class="swiper-slide relative">
                        <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}"
                            class="absolute inset-0 w-full h-full object-cover">
                        <div class="hero-overlay absolute inset-0 flex items-end sm:pb-12 md:pb-16 lg:pb-24 pb-0">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-4 sm:pb-0">
                                <div class="max-w-xl">
                                    <h2
                                        class="font-heading font-bold text-2xl sm:text-3xl lg:text-4xl text-white leading-tight drop-shadow-lg">
                                        {{ $slide['title'] }}
                                    </h2>
                                    <p class="text-white/90 text-sm sm:text-lg leading-relaxed drop-shadow">
                                        {{ $slide['subtitle'] }}
                                    </p>
                                    @if ($slide['btn_text'] && $slide['link'])
                                        <a href="{{ route($slide['link']) }}"
                                            class="inline-flex items-center gap-2 mt-4 px-3 py-2 bg-white text-ris-primary font-heading font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg">
                                            {{ $slide['btn_text'] }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    @endif
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
        {{-- Decorative shapes --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-ris-primary/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 w-48 h-48 bg-ris-primary/5 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none">
        </div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-ris-primary/2 rounded-full pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            {{-- Section header --}}
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-ris-primary/10 text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    পরিসংখ্যান
                </span>
                <h2 class="mt-4 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আমাদের স্কুল এক নজরে</h2>
                <p class="mt-3 text-gray-500">প্রতি বছর বেড়ে চলা আমাদের পরিবার — সংখ্যায় প্রকাশিত</p>
            </div>

            @php
                $foundingYear = 2015;

                $statItems = [
                    [
                        'value' => (int) ($stats['total_students'] ?? 500),
                        'suffix' => '+',
                        'label' => 'মোট ছাত্র-ছাত্রী',
                        'theme' => 'from-sky-500 via-indigo-600 to-blue-700',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    ],
                    [
                        'value' => (int) ($stats['total_teachers'] ?? 20),
                        'suffix' => '+',
                        'label' => 'শিক্ষকমণ্ডলী',
                        'theme' => 'from-emerald-500 via-teal-600 to-cyan-700',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                    ],
                    [
                        'value' => (int) ($stats['total_classes'] ?? 8),
                        'suffix' => '',
                        'label' => 'শ্রেণি',
                        'theme' => 'from-amber-500 via-orange-600 to-rose-600',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                    ],
                    [
                        'value' => (int) ($stats['total_staff'] ?? 10),
                        'suffix' => '+',
                        'label' => 'কর্মচারী',
                        'theme' => 'from-fuchsia-500 via-purple-600 to-violet-700',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    ],
                    [
                        'value' => max((int) date('Y') - $foundingYear, 1),
                        'suffix' => '+',
                        'label' => 'বছরের অভিজ্ঞতা',
                        'theme' => 'from-rose-500 via-red-600 to-pink-700',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 reveal-stagger">
                @foreach ($statItems as $stat)
                    <div
                        class="group relative overflow-hidden rounded-3xl bg-linear-to-br {{ $stat['theme'] }} p-6 sm:p-8 text-white shadow-lg shadow-ris-dark/5 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 reveal">
                        {{-- Decorative glows --}}
                        <div
                            class="absolute -top-10 -right-10 w-28 h-28 bg-white/10 rounded-full transition-transform duration-500 group-hover:scale-150 pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-12 -left-6 w-32 h-32 bg-black/10 rounded-full transition-transform duration-500 group-hover:scale-125 pointer-events-none">
                        </div>

                        <div class="relative">
                            <div
                                class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center ring-1 ring-white/25 group-hover:bg-white/25 group-hover:rotate-6 transition-all duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $stat['icon'] !!}
                                </svg>
                            </div>
                            <div class="mt-6 font-heading font-bold text-3xl sm:text-4xl leading-tight">
                                <span data-count="{{ $stat['value'] }}">০</span><span
                                    class="text-white/80">{{ $stat['suffix'] }}</span>
                            </div>
                            <div
                                class="mt-3 h-1 w-10 rounded-full bg-white/40 transition-all duration-500 group-hover:w-16 group-hover:bg-white/70">
                            </div>
                            <div class="mt-3 text-sm sm:text-base font-medium text-white/90">{{ $stat['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ Message Section (dynamic — DB data first, seed fallback on web only) ═══ --}}
    @php
        $fallbackMessages = [
            [
                'title' => 'চেয়ারম্যানের বাণী',
                'name' => 'রেফাউল হক',
                'designation' => 'চেয়ারম্যান, রেশমা ইন্টারন্যাশনাল স্কুল',
                'photo' => 'images/home/rhm.png',
                'message' =>
                    'প্রিয় অভিভাবকগণ, আমরা বিশ্বাস করি প্রতিটি শিশু অসাধারণ সম্ভাবনায় পূর্ণ। রেশমা ইন্টারন্যাশনাল স্কুল আপনার সন্তানের এই সম্ভাবনাকে সমৃদ্ধ করতে প্রতিশ্রুতিবদ্ধ।',
            ],
            [
                'title' => 'চেয়ারপার্সনের বাণী',
                'name' => 'রেশমা আকতার',
                'designation' => 'চেয়ারপার্সন, রেশমা ইন্টারন্যাশনাল স্কুল',
                'photo' => 'images/home/ra.jpg',
                'message' =>
                    'শিক্ষার মাধ্যমেই একটি জাতি সত্যিকার অর্থে এগিয়ে যেতে পারে। রেশমা ইন্টারন্যাশনাল স্কুল আমাদের সন্তানদের জন্য মানসম্মত ও আধুনিক শিক্ষার পরিবেশ নিশ্চিত করার প্রতি প্রতিশ্রুতিবদ্ধ।',
            ],
            [
                'title' => 'প্রতিষ্ঠাতার বাণী',
                'name' => 'কুদরত-ই-ইবতিহাজ জয়',
                'designation' => 'প্রতিষ্ঠাতা, রেশমা ইন্টারন্যাশনাল স্কুল',
                'photo' => 'images/home/kej.png',
                'message' =>
                    'শিক্ষাই জাতির মেরুদণ্ড। আমাদের সন্তানদের মানসম্মত শিক্ষা দান এবং তাদের সুনাগরিক হিসাবে গড়ে তুলতে আমি রেশমা ইন্টারন্যাশনাল স্কুলকে প্রতিষ্ঠা করেছি।',
            ],
            [
                'title' => 'প্রধান শিক্ষকের বাণী',
                'name' => 'সাইফুল ইসলাম',
                'designation' => 'প্রধান শিক্ষক, রেশমা ইন্টারন্যাশনাল স্কুল',
                'photo' => 'images/home/si.jpg',
                'message' =>
                    'প্রিয় শিক্ষার্থী ও অভিভাবকগণ, শিক্ষা হলো আলোর পথ যা জীবনকে আলোকিত করে। আমাদের স্কুলে আমরা আধুনিক শিক্ষা পদ্ধতির পাশাপাশি নৈতিক মূল্যবোধ ও সুশিক্ষার পরিবেশ তৈরি করেছি।',
            ],
        ];

        $displayMessages = $messages->count()
            ? $messages->map(
                fn($m) => (object) [
                    'title' => $m->title,
                    'name' => $m->name,
                    'designation' => $m->designation,
                    'message' => $m->message,
                    'photo_url' => $m->photo ? Storage::url($m->photo) : null,
                ],
            )
            : collect($fallbackMessages)->map(
                fn($m) => (object) [
                    'title' => $m['title'],
                    'name' => $m['name'],
                    'designation' => $m['designation'],
                    'message' => $m['message'],
                    'photo_url' => asset($m['photo']),
                ],
            );
    @endphp
    <section class="py-16 sm:py-20 bg-ris-primary-50/40 section-pattern-grid relative overflow-hidden">
        {{-- Decorative shapes --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-56 h-56 bg-ris-accent/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">বার্তা</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আলোকিত মানুষ গড়ায় আমরা বিশ্বাসী
                </h2>
            </div>

            <div class="swiper message-swiper reveal">
                <div class="swiper-wrapper">
                    @foreach ($displayMessages as $msg)
                        <div class="swiper-slide">
                            <div
                                class="message-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-full">
                                <div class="flex flex-col sm:flex-row h-full">
                                    <div class="sm:w-1/3 bg-ris-primary/5 flex items-center justify-center p-8">
                                        @if ($msg->photo_url)
                                            <img src="{{ $msg->photo_url }}" alt="{{ $msg->name }}"
                                                class="w-32 h-32 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-32 h-32 rounded-full gradient-logo flex items-center justify-center font-heading font-bold text-4xl text-white shadow-lg">
                                                {{ mb_substr($msg->name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div class="sm:w-2/3 p-6 sm:p-8">
                                        <div class="flex items-center gap-2 mb-3">
                                            <svg class="w-5 h-5 text-ris-primary" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                                            </svg>
                                            <h3 class="font-heading font-bold text-lg text-ris-dark">{{ $msg->title }}
                                            </h3>
                                        </div>
                                        <p class="text-gray-600 leading-relaxed text-base italic">"{{ $msg->message }}"</p>
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            <p class="font-heading font-bold text-ris-dark">{{ $msg->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $msg->designation }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-6"></div>
            </div>
        </div>
    </section>

    {{-- ═══ Teachers Section ═══ --}}
    @php
        $homeTeachers = \App\Models\User::where('role', 'teacher')
            ->where('is_active', true)
            ->with('teacherProfile')
            ->whereHas('teacherProfile')
            ->latest()
            ->get();
    @endphp
    @if ($homeTeachers->count())
        <section
            class="py-16 sm:py-20 bg-linear-to-b from-white to-ris-primary-50/30 section-pattern-diagonal relative overflow-hidden">
            {{-- Decorative shapes --}}
            <div class="absolute top-20 right-20 w-32 h-32 bg-ris-light/10 rounded-2xl rotate-45 pointer-events-none">
            </div>
            <div class="absolute bottom-20 left-10 w-24 h-24 bg-ris-primary/5 rounded-full pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">আমাদের
                        দল</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">শিক্ষকবৃন্দ</h2>
                    <p class="mt-3 text-gray-500">অভিজ্ঞ ও নিবেদিত শিক্ষকদের দল যারা আপনার সন্তানের উজ্জ্বল ভবিষ্যত গড়ে
                        তুলছেন।</p>
                </div>

                <div class="swiper teacher-swiper reveal">
                    <div class="swiper-wrapper">
                        @foreach ($homeTeachers as $teacher)
                            <div class="swiper-slide">
                                @include('website.partials.teacher-card', ['teacher' => $teacher])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination mt-6"></div>
                </div>

                <div class="mt-8 text-center reveal">
                    <a href="{{ route('teachers') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-ris-primary text-ris-primary font-heading font-medium text-sm hover:bg-ris-primary hover:text-white transition-all duration-300">
                        সকল শিক্ষক দেখুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ Programs / Departments (green.edu.bd faculty flip card style) ═══ --}}
    <section class="py-16 sm:py-20 bg-ris-gray-50 relative overflow-hidden">
        {{-- Decorative shapes --}}
        <div class="absolute top-10 left-10 w-20 h-20 border-2 border-ris-primary/10 rounded-full pointer-events-none">
        </div>
        <div
            class="absolute bottom-10 right-10 w-32 h-32 border border-ris-primary/5 rounded-2xl rotate-12 pointer-events-none">
        </div>
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
                            <div class="absolute inset-0 bg-black/10 transition-opacity duration-300 group-hover:opacity-0">
                            </div>
                            <svg class="w-16 h-16 text-white relative z-10 drop-shadow-lg transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $program['icon'] !!}
                            </svg>
                            <div class="absolute bottom-0 left-0 right-0 h-16 bg-linear-to-t from-white to-transparent">
                            </div>
                        </div>
                        <div class="p-6 text-center">
                            <h3
                                class="font-heading font-bold text-lg text-ris-dark transition-colors duration-300 group-hover:text-ris-primary">
                                {{ $program['title'] }}</h3>
                            <p class="text-xs font-heading text-ris-primary font-semibold mt-1">{{ $program['range'] }}
                            </p>
                            <p class="mt-3 text-sm text-gray-500 leading-relaxed transition-colors duration-300 group-hover:text-gray-600">
                                {{ $program['desc'] }}</p>
                            <a href="{{ route('admission') }}"
                                class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
                                বিস্তারিত
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-video group"
                        x-data="{ playing: false }">
                        <video x-ref="vid" @play="playing = true" @pause="playing = false" class="w-full h-full"
                            controls preload="metadata">
                            <source src="{{ asset('videos/video.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div x-show="!playing" x-transition
                            class="absolute inset-0 flex items-center justify-center bg-black/30 cursor-pointer"
                            @click="$refs.vid.play()">
                            <div
                                class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:bg-ris-primary/80 transition-all duration-300">
                                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
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
                    <div class="mb-8 reveal">
                        <span
                            class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ক্যাম্পাস
                            কার্যক্রম</span>
                        <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">ক্যাম্পাস লাইফ</h2>
                    </div>

            <div class="grid lg:grid-cols-12 gap-6 md:gap-10 items-stretch">
                {{-- Left: Campus image --}}
                <div class="lg:col-span-5 min-w-0">
                    <div class="relative h-64 sm:h-80 lg:h-full rounded-2xl overflow-hidden shadow-card group">
                        <img src="{{ asset('images/home/campus.jpg') }}" alt="Campus Life"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-linear-to-t from-ris-dark/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <span
                                class="inline-block px-3 py-1 bg-ris-primary text-white text-xs font-medium rounded-full">ক্যাম্পাস
                                প্রাঙ্গণ</span>
                        </div>
                    </div>
                </div>

                {{-- Right: News carousel --}}
                <div class="lg:col-span-7 min-w-0">
                    <div class="swiper campus-life-swiper reveal">
                        <div class="swiper-wrapper">
                            @foreach ($campusNews as $news)
                                <div class="swiper-slide">
                                    <div
                                        class="campus-life-card bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-full">
                                        <div class="h-48 relative flex items-center justify-center">
                                            @if ($news['image'])
                                                <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}"
                                                    class="absolute inset-0 w-full h-full object-cover">
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
                                            @if ($news['date'])
                                                <div class="absolute top-4 left-4 badge">{{ $news['date'] }}</div>
                                            @endif
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

    {{-- ═══ Notice Board (improved design) ═══ --}}
    <section class="py-16 sm:py-24 bg-white section-pattern-grid relative overflow-hidden">
        {{-- Decorative shapes --}}
        <div
            class="absolute top-0 left-0 w-80 h-80 bg-ris-primary/5 rounded-full -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-ris-light/5 rounded-full translate-x-1/3 translate-y-1/3 pointer-events-none">
        </div>
        <div class="absolute top-1/3 right-1/3 w-20 h-20 bg-ris-primary/4 rounded-xl rotate-45 pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-14">

                {{-- ═══ Left: Heading + Featured notice ═══ --}}
                <div class="lg:col-span-4 min-w-0">
                    <div class="sticky top-28 reveal-left">
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="w-12 h-12 rounded-2xl gradient-logo flex items-center justify-center shadow-lg shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </span>
                            <div>
                                <span
                                    class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">সর্বশেষ
                                    ঘোষণা</span>
                                <h2 class="font-heading font-bold text-2xl sm:text-3xl text-ris-dark leading-tight">নোটিশ
                                    বোর্ড</h2>
                            </div>
                        </div>
                        <p class="text-gray-500 mt-4 leading-relaxed">
                            স্কুলের সকল গুরুত্বপূর্ণ ঘোষণা, সময়সূচি ও তথ্য নিয়মিতভাবে এখানে প্রকাশ করা হয়।
                        </p>

                        {{-- Featured / Important notice --}}
                        @if (! empty($notices))
                            <div class="featured-notice-card mt-8 rounded-2xl overflow-hidden shadow-card group relative">
                                <div
                                    class="relative h-40 bg-linear-to-br from-ris-dark to-ris-primary flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/20 group-hover:scale-110 transition-transform duration-500"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <div class="absolute top-4 left-4 badge badge-warning">গুরুত্বপূর্ণ</div>
                                </div>
                                <div class="bg-white p-5 border border-t-0 border-gray-100">
                                    <h4 class="font-heading font-semibold text-ris-dark leading-snug">{{ $notices[0]['title'] }}</h4>
                                    <hr class="my-3 border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $notices[0]['date'] }}
                                        </span>
                                        <a href="{{ route('notices') }}"
                                            class="text-xs font-heading font-semibold text-ris-primary hover:text-ris-dark transition-colors inline-flex items-center gap-1">
                                            বিস্তারিত
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ═══ Right: Notice list with tabs ═══ --}}
                <div class="lg:col-span-8 min-w-0">
                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 sm:p-8 reveal-right shadow-sm"
                        x-data="{ active: 'all' }">
                        @if (empty($notices))
                            {{-- Empty state --}}
                            <div class="text-center py-12">
                                <div
                                    class="w-20 h-20 mx-auto rounded-full bg-white flex items-center justify-center mb-6 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <h4 class="font-heading font-bold text-xl text-ris-dark">কোনো নোটিশ যোগ করা হয়নি</h4>
                                <p class="mt-2 text-gray-500">শীঘ্রই স্কুলের গুরুত্বপূর্ণ ঘোষণা এখানে প্রকাশ করা হবে।</p>
                            </div>
                        @else
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
                                            ?
                                            'bg-ris-primary text-white shadow-lg shadow-ris-primary/25' :
                                            'bg-white text-gray-600 hover:text-ris-primary border border-gray-200 hover:border-ris-primary/30'">
                                        {{ $tab['label'] }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Notice items --}}
                            <div class="space-y-3.5 reveal-stagger">
                                @foreach ($notices as $notice)
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
                                        <span
                                            class="font-heading font-bold text-xl leading-none">{{ $day }}</span>
                                        <span
                                            class="text-[10px] font-heading font-semibold mt-1 uppercase tracking-wide">{{ $month }}</span>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="text-[10px] font-heading font-semibold px-2.5 py-0.5 rounded-full bg-ris-primary/10 text-ris-primary">{{ $notice['category_label'] }}</span>
                                            @if ($notice['highlight'] ?? false)
                                                <span
                                                    class="text-[10px] font-heading font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700">নতুন</span>
                                            @endif
                                        </div>
                                        <h4
                                            class="mt-1.5 font-heading font-semibold text-sm sm:text-base text-ris-dark leading-snug truncate">
                                            {{ $notice['title'] }}
                                        </h4>
                                        <p class="mt-1 text-xs text-gray-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $notice['date'] }} · প্রকাশিত
                                        </p>
                                    </div>

                                    {{-- Arrow --}}
                                    <a href="{{ route('notices') }}"
                                        class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center transition-all duration-300 {{ $notice['highlight'] ?? false ? 'bg-ris-primary text-white hover:bg-ris-dark' : 'bg-gray-50 text-gray-400 hover:bg-ris-primary hover:text-white' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                            </div>
                        @endif

                        {{-- Footer button --}}
                        <div class="mt-7 text-center">
                            <a href="{{ route('notices') }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-ris-primary text-white font-heading font-medium text-sm hover:bg-ris-dark shadow-lg shadow-ris-primary/25 transition-all duration-300 hover:-translate-y-0.5">
                                সব নোটিশ দেখুন
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ Gallery Section (swiper) ═══ --}}
    @if ($galleryItems->count())
        <section class="py-16 sm:py-20 bg-ris-gray-50 section-pattern-waves relative overflow-hidden">
            <div class="absolute top-10 left-10 w-24 h-24 bg-ris-primary/5 rounded-full pointer-events-none"></div>
            <div
                class="absolute bottom-10 right-10 w-32 h-32 border border-ris-primary/10 rounded-2xl rotate-12 pointer-events-none">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                    <span
                        class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">গ্যালারি</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আমাদের ক্যাম্পাসের মুহূর্ত
                    </h2>
                    <p class="mt-3 text-gray-500">স্কুলের কার্যক্রম ও উৎসবের স্মরণীয় মুহূর্তগুলো দেখুন।</p>
                </div>

                <div class="swiper gallery-swiper reveal">
                    <div class="swiper-wrapper">
                        @foreach ($galleryItems as $item)
                            <div class="swiper-slide">
                                <div
                                    class="gallery-card lightbox-trigger group relative rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-64 cursor-pointer"
                                    data-title="{{ $item->title }}"
                                    data-category="{{ $item->category ?? '' }}"
                                    data-caption="{{ $item->description ?? '' }}">
                                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div
                                        class="absolute inset-0 bg-linear-to-t from-black/75 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                                        <div class="p-5">
                                            @if ($item->category)
                                                <span
                                                    class="inline-block px-2.5 py-0.5 bg-ris-primary text-white text-xs font-medium rounded-full mb-2">{{ $item->category }}</span>
                                            @endif
                                            <h4 class="font-heading font-bold text-white text-base">{{ $item->title }}
                                            </h4>
                                            @if ($item->description)
                                                <p class="text-white/80 text-xs mt-1 line-clamp-2">
                                                    {{ $item->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-center gap-4 ris-swiper-nav">
                    <button type="button" class="gallery-btn-prev ris-swiper-btn" aria-label="পূর্ববর্তী">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="rotate-180">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                    <div class="swiper-pagination"></div>
                    <button type="button" class="gallery-btn-next ris-swiper-btn" aria-label="পরবর্তী">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>

                <div class="mt-8 text-center reveal">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-ris-primary text-ris-primary font-heading font-medium text-sm hover:bg-ris-primary hover:text-white transition-all duration-300">
                        সব গ্যালারি দেখুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ Testimonials Section (swiper) ═══ --}}
    <section
        class="py-16 sm:py-20 testimonial-bg section-pattern-grid relative overflow-hidden">
            @include('website.partials.decorative-shapes')
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                    <span
                        class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">মতামত</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">শুভকামনা ও মতামত</h2>
                    <p class="mt-3 text-gray-500">আমাদের অভিভাবক ও শিক্ষার্থীদের মূল্যবান অনুভূতি।</p>
                </div>

                @if ($testimonials->count())
                    {{-- Rating summary --}}
                    <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mb-10 reveal">
                    <div
                        class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-white border border-amber-100 shadow-card">
                        <div
                            class="w-11 h-11 rounded-xl bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-heading font-bold text-2xl leading-none text-amber-600">★ {{ $testimonialStats['bangla_average'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">গড় রেটিং</p>
                        </div>
                    </div>
                </div>

                <div class="swiper testimonial-swiper reveal">
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <div class="swiper-slide">
                                @include('website.partials.testimonial-card', ['testimonial' => $testimonial])
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-center gap-4 ris-swiper-nav">
                    <button type="button" class="testimonial-btn-prev ris-swiper-btn" aria-label="পূর্ববর্তী">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="rotate-180">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                    <div class="swiper-pagination"></div>
                    <button type="button" class="testimonial-btn-next ris-swiper-btn" aria-label="পরবর্তী">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>

                @else
                    {{-- Empty state --}}
                    <div class="text-center py-16">
                        <div
                            class="w-20 h-20 mx-auto rounded-full bg-white flex items-center justify-center mb-6 shadow-inner">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                            </svg>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো মতামত নেই</h3>
                        <p class="mt-2 text-gray-500">শীঘ্রই আমাদের অভিভাবকদের মতামত যোগ করা হবে। আপনি আপনার অভিজ্ঞতা
                            শেয়ার করুন।</p>
                    </div>
                @endif

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @if ($testimonials->count())
                        <a href="{{ route('testimonials') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-ris-primary text-ris-primary font-heading font-medium text-sm hover:bg-ris-primary hover:text-white transition-all duration-300">
                            সব টেস্টিমোনিয়াল দেখুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    @endif
                </div>
                @include('website.partials.testimonial-submit')
            </div>
        </section>

    {{-- ═══ FAQ Section (dynamic — admin managed) ═══ --}}
    @if ($faqs->count())
        <section class="py-16 sm:py-20 bg-ris-gray-50 section-pattern-grid relative overflow-hidden">
            <div class="absolute top-10 right-10 w-24 h-24 bg-ris-primary/5 rounded-full pointer-events-none"></div>
            <div
                class="absolute bottom-10 left-10 w-32 h-32 border border-ris-primary/10 rounded-2xl rotate-12 pointer-events-none">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">সাধারণ
                        জিজ্ঞাসা</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">প্রায়শই জিজ্ঞাসিত প্রশ্ন
                    </h2>
                    <p class="mt-3 text-gray-500">আপনার মনে প্রশ্ন আছে? নিচে সাধারণ প্রশ্নগুলোর উত্তর দেওয়া হলো।</p>
                </div>

                <div class="space-y-4 reveal-stagger">
                    @foreach ($faqs as $faq)
                        <div class="faq-item bg-white rounded-2xl border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden reveal"
                            x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between gap-4 px-5 sm:px-6 py-4 sm:py-5 text-left cursor-pointer group">
                                <span
                                    class="font-heading font-semibold text-ris-dark group-hover:text-ris-primary transition-colors flex-1">{{ $faq->question }}</span>
                                <span
                                    class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all duration-300 {{ $loop->iteration === 1 ? 'gradient-logo text-white' : 'bg-ris-primary/10 text-ris-primary' }}"
                                    :class="open ? 'rotate-180' : ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </button>
                            <div x-show="open" x-transition class="px-5 sm:px-6 pb-5 sm:pb-6">
                                <p class="text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
