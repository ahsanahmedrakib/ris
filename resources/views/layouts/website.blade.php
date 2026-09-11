<!DOCTYPE html>
<html lang="bn" dir="ltr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'রেশমা ইন্টারন্যাশনাল স্কুল')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire --}}
    @livewireStyles
</head>

<body class="font-body antialiased text-gray-800 bg-white">

    {{-- ═══ Top Bar ═══ --}}
    <div class="bg-ris-dark text-white text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-9">
                <div class="flex items-center gap-4 sm:gap-6">
                    <a href="tel:+8801619007006"
                        class="flex items-center gap-1.5 hover:text-ris-light-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="hidden sm:block">+৮৮০-১৬১৯ ০০৭ ০০৬</span>
                    </a>
                    <a href="mailto:resmaintlschool@gmail.com"
                        class="flex items-center gap-1.5 hover:text-ris-light-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="hidden sm:block">resmaintlschool@gmail.com</span>
                    </a>
                </div>
                <div class="flex gap-2">
                    {{-- Login Dropdown --}}
                    <div class="relative z-9999" x-data="{ loginOpen: false }">
                        <button @click="loginOpen = !loginOpen" @click.outside="loginOpen = false"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-white hover:text-ris-light-200 hover:bg-ris-primary/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden sm:inline">লগইন</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                :class="loginOpen && 'rotate-180'" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="loginOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                            <a target="_blank" href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors">
                                <span
                                    class="w-9 h-9 shrink-0 rounded-lg gradient-logo flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-ris-dark">অ্যাডমিন লগইন</div>
                                    <div class="text-xs text-gray-500">স্কুল পরিচালনা প্যানেল</div>
                                </div>
                            </a>
                            <a href="{{ route('login') }}?role=teacher"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors">
                                <span
                                    class="w-9 h-9 shrink-0 rounded-lg bg-ris-primary/10 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-ris-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.825-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-ris-dark">শিক্ষক লগইন</div>
                                    <div class="text-xs text-gray-500">শ্রেণি ও ক্লাস পরিচালনা</div>
                                </div>
                            </a>
                            <a href="{{ route('login') }}?role=parent"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors">
                                <span
                                    class="w-9 h-9 shrink-0 rounded-lg bg-ris-primary/10 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-ris-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-ris-dark">অভিভাবক লগইন</div>
                                    <div class="text-xs text-gray-500">সন্তানের অগ্রগতি দেখুন</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="https://www.facebook.com/Resma.International.School" target="_blank" rel="noopener"
                            class="hover:text-ris-light-200 transition-colors" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Main Navigation ═══ --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                {{-- Logo & School Name --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo.png') }}" alt="Resma International School"
                        class="h-10 lg:h-14 w-auto object-contain drop-shadow-sm group-hover:opacity-90 transition-opacity">
                    <div class="hidden sm:block">
                        <div class="font-heading font-bold text-ris-primary text-lg leading-tight">রেশমা ইন্টারন্যাশনাল
                            স্কুল</div>
                        <div class="text-[11px] text-ris-gray tracking-wide">৪৩৯, ঘুল্লিবাড়ি মোড়, গোপালগঞ্জ-৮১০০</div>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-1">
                    @php
                        $navItems = [
                            ['label' => 'হোম', 'route' => 'home'],
                            ['label' => 'আমাদের সম্পর্কে', 'route' => 'about'],
                            ['label' => 'ভর্তি প্রক্রিয়া', 'route' => 'admission'],
                            ['label' => 'মেধাবৃত্তি', 'route' => 'scholarship'],
                            ['label' => 'নোটিশ', 'route' => 'notices'],
                            ['label' => 'যোগাযোগ', 'route' => 'contact'],
                        ];
                    @endphp
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                           {{ request()->routeIs($item['route']) ? 'bg-ris-primary/10 text-ris-primary' : 'text-gray-600 hover:text-ris-primary hover:bg-ris-primary/5' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- CTA + Login + Mobile Toggle --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    <a href="{{ route('admission') }}" class="hidden sm:inline-flex btn-primary text-sm py-2 px-4">
                        ভর্তি
                    </a>

                    {{-- Mobile Hamburger --}}
                    <button @click="open = !open"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                        aria-label="Menu">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                        class="block px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                       {{ request()->routeIs($item['route']) ? 'bg-ris-primary/10 text-ris-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-ris-primary' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="pt-3 border-t border-gray-100 mt-3">
                    <a href="{{ route('admission') }}" class="btn-primary w-full text-center text-sm">
                        ভর্তি
                    </a>
                </div>

                {{-- Mobile Login --}}
                <div class="pt-3 border-t border-gray-100 mt-3 space-y-1">
                    <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">লগইন</p>
                    <a target="_blank" href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-ris-primary transition-colors">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        অ্যাডমিন লগইন
                    </a>
                    <a href="{{ route('login') }}?role=teacher"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-ris-primary transition-colors">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.825-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        শিক্ষক লগইন
                    </a>
                    <a href="{{ route('login') }}?role=parent"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-ris-primary transition-colors">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        অভিভাবক লগইন
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══ Main Content ═══ --}}
    <main>
        @yield('content')
    </main>

    {{-- ═══ Footer ═══ --}}
    <footer class="bg-ris-dark-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-12">

                {{-- Column 1: About --}}
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <img src="{{ asset('logo.png') }}" alt="RIS"
                            class="h-12 lg:h-16 w-auto object-contain">
                        <div>
                            <h3 class="font-heading font-bold text-white text-lg leading-tight">রেশমা ইন্টারন্যাশনাল
                                স্কুল</h3>
                            <p class="text-xs text-ris-gray-400">Resma International School</p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-400">
                        রেশমা ইন্টারন্যাশনাল স্কুল শিক্ষার মাধ্যমে আলোকিত ভবিষ্যত গড়ে তোলার প্রতিশ্রুতিবদ্ধ। আমরা
                        আমাদের ছাত্রদের সর্বোত্তম শিক্ষা প্রদান করি যাতে তারা আগামীর নেতৃত্ব হতে পারে।
                    </p>
                </div>

                {{-- Column 2: Quick Links --}}
                <div>
                    <h3 class="font-heading font-semibold text-white text-lg mb-5">দ্রুত লিঙ্ক</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">হোম</a></li>
                        <li><a href="{{ route('about') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">আমাদের
                                সম্পর্কে</a></li>
                        <li><a href="{{ route('admission') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">ভর্তি</a></li>
                        <li><a href="{{ route('scholarship') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">মেধাবৃত্তি</a>
                        </li>
                        <li><a href="{{ route('notices') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">নোটিশ</a></li>
                        <li><a href="{{ route('contact') }}"
                                class="text-sm text-gray-400 hover:text-white hover:pl-1 transition-all">যোগাযোগ</a>
                        </li>
                    </ul>
                </div>

                {{-- Column 3: Contact Info --}}
                <div>
                    <h3 class="font-heading font-semibold text-white text-lg mb-5">যোগাযোগ</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-ris-light shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-400">৪৩৯, ঘুল্লিবাড়ি মোড়, গোপালগঞ্জ-৮১০০, বাংলাদেশ</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-ris-light shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:+8801619007006"
                                class="text-sm text-gray-400 hover:text-white transition-colors">+৮৮০-১৬১৯ ০০৭ ০০৬</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-ris-light shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:resmaintlschool@gmail.com"
                                class="text-sm text-gray-400 hover:text-white transition-colors">resmaintlschool@gmail.com</a>
                        </li>
                    </ul>

                    {{-- Social --}}
                    <div class="flex items-center gap-3 mt-6">
                        <a href="https://www.facebook.com/Resma.International.School" target="_blank" rel="noopener"
                            class="w-9 h-9 rounded-lg bg-white/10 hover:bg-ris-primary flex items-center justify-center transition-colors"
                            aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/10 mt-12 pt-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-white">
                    <p>&copy; {{ date('Y') }} রেশমা ইন্টারন্যশনাল স্কুল। সর্বস্বত্ব সংরক্ষিত।</p>
                    <p>Developed by: <span class="underline"><a href="https://www.facebook.com/rakibahsanahmed"
                                target="_blank" rel="noopener noreferrer">Ahsan</a> <a href="tel:+8801631112475"
                                target="_blank">Ahmed</a> <a
                                href="https://wa.me/8801631112475?text=Hello! I am interested in your services."
                                target="_blank">Rakib</a></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

</body>

</html>
