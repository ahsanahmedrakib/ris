<!DOCTYPE html>
<html lang="bn" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'অ্যাডমিন প্যানেল') — রেশমা ইন্টারন্যাশনাল স্কুল</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .ris-quill .ql-editor {
            min-height: 160px;
        }

        .ris-quill .ql-toolbar.ql-snow {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .ris-quill .ql-container.ql-snow {
            border-radius: 0 0 0.5rem 0.5rem;
            font-size: 0.875rem;
            height: auto;
            overflow: hidden;
        }
    </style>

    @livewireStyles
</head>

<body class="h-full font-body antialiased bg-gray-50 text-gray-800" x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
    mobileSidebar: false,
    tooltip: { show: false, text: '', x: 0, y: 0 },
    showTooltip(el, text) {
        const rect = el.getBoundingClientRect();
        this.tooltip = { show: true, text, x: rect.right + 10, y: rect.top + rect.height / 2 };
    },
    hideTooltip() {
        this.tooltip.show = false;
    }
}"
    x-effect="localStorage.setItem('sidebarOpen', sidebarOpen)">

    <div class="flex h-full">

        {{-- ═══ Sidebar ═══ --}}
        {{-- Desktop --}}
        <aside id="desktop-sidebar"
            class="hidden lg:flex lg:flex-col bg-ris-dark-900 text-white transition-all duration-300 ease-in-out py-2"
            :style="sidebarOpen ? 'width:16rem' : 'width:5rem'" style="width:16rem">
            {{-- Logo --}}
            <a href={{ route('home') }} target="_blank">
                <div class="flex items-center justify-center gap-3 px-4 h-16 border-b border-white/10 shrink-0">
                    <img :src="sidebarOpen ? '{{ asset('logo-white.png') }}' : '{{ asset('logo-small.png') }}'"
                        alt="RIS" class="h-15 w-auto object-contain shrink-0">
                </div>
            </a>

            {{-- Navigation --}}
            @php
                $isNavActive = function (string $route): bool {
                    return request()->routeIs($route) || request()->routeIs($route . '.*');
                };

                $dashboardItem = [
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'label' => 'ড্যাশবোর্ড',
                    'route' => 'admin.dashboard',
                ];

                $sidebarGroups = [
                    'ব্যবস্থাপনা' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                            'label' => 'নোটিফিকেশন',
                            'route' => 'admin.notifications.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-9 9h6m-6 4h6"/>',
                            'label' => 'অ্যাক্টিভিটি লগ',
                            'route' => 'admin.activity-logs.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>',
                            'label' => 'ট্র্যাশ',
                            'route' => 'admin.trash.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                            'label' => 'ইউজার',
                            'route' => 'admin.users.index',
                            'adminOnly' => true,
                        ],
                    ],
                    'একাডেমিক' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>',
                            'label' => 'শ্রেণি',
                            'route' => 'admin.classes.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>',
                            'label' => 'বিষয়',
                            'route' => 'admin.subjects.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                            'label' => 'পরীক্ষা',
                            'route' => 'admin.exams.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                            'label' => 'ফলাফল',
                            'route' => 'admin.results.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                            'label' => 'ক্লাশ রুটিন',
                            'route' => 'admin.class-routines.index',
                        ],
                    ],
                    'হোমপেজ সেকশন' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>',
                            'label' => 'হিরো স্লাইডার',
                            'route' => 'admin.hero-slides.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                            'label' => 'পরিসংখ্যান',
                            'route' => 'admin.school-statistics.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>',
                            'label' => 'বার্তা',
                            'route' => 'admin.messages.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                            'label' => 'শিক্ষকবৃন্দ',
                            'route' => 'admin.teachers.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                            'label' => 'ক্যাম্পাস লাইফ',
                            'route' => 'admin.campus-news.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
                            'label' => 'নোটিশ',
                            'route' => 'admin.notices.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                            'label' => 'গ্যালারি',
                            'route' => 'admin.gallery.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                            'label' => 'শুভকামনা ও মতামত',
                            'route' => 'admin.testimonials.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'প্রশ্নোত্তর',
                            'route' => 'admin.faqs.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'আমাদের সম্পর্কে',
                            'route' => 'admin.about.index',
                        ],
                    ],
                    'ছাত্র ও ভর্তি' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                            'label' => 'ছাত্র/ছাত্রী',
                            'route' => 'admin.students.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'ভর্তি আবেদন',
                            'route' => 'admin.admission.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                            'label' => 'মেধাবৃত্তি',
                            'route' => 'admin.scholarship.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                            'label' => 'একাডেমিক ক্যালেন্ডার',
                            'route' => 'admin.academic-calendars.index',
                        ],
                    ],
                    'হিসাব ও ফি' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-6m-3 3h.01M9 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'ফি কাঠামো',
                            'route' => 'admin.fees.structures',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'ফি চালান',
                            'route' => 'admin.fees.invoices',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                            'label' => 'পেমেন্ট ও আদায়',
                            'route' => 'admin.fees.payments',
                        ],
                    ],
                    'যোগাযোগ ও অন্যান্য' => [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                            'label' => 'কনটাক্ট মেসেজ',
                            'route' => 'admin.contact-messages.index',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h3v3h-3v-3zM17 14h3v3h-3v-3zM14 17h3v3h-3v-3zM20 14h1v3h-3v-1h2v-2zM17 20h1v1h-1v-1z"/>',
                            'label' => 'QR কোড জেনারেটর',
                            'route' => 'admin.qrcode',
                        ],
                    ],
                ];
                $activeGroup = '';
                if (!$isNavActive($dashboardItem['route'])) {
                    foreach ($sidebarGroups as $group => $groupItems) {
                        foreach ($groupItems as $groupItem) {
                            if (!empty($groupItem['adminOnly']) && auth()->user()->role !== 'admin') {
                                continue;
                            }
                            if ($isNavActive($groupItem['route'])) {
                                $activeGroup = $group;
                                break 2;
                            }
                        }
                    }
                }
            @endphp

            <nav class="flex-1 sidebar-scroll overflow-y-auto py-4 px-3 space-y-1" x-data="navGroups({{ json_encode($activeGroup) }})">
                <a href="{{ route($dashboardItem['route']) }}"
                    @mouseenter="sidebarOpen || showTooltip($el, '{{ $dashboardItem['label'] }}')"
                    @mouseleave="hideTooltip()"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group mb-2
                           {{ $isNavActive($dashboardItem['route']) ? 'bg-ris-primary text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ $isNavActive($dashboardItem['route']) ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $dashboardItem['icon'] !!}
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="whitespace-nowrap">{{ $dashboardItem['label'] }}</span>
                </a>
                @foreach ($sidebarGroups as $groupTitle => $items)
                    <div class="pt-4 first:pt-0">
                        <button @click="toggleGroup({{ json_encode($groupTitle) }})"
                            @mouseenter="sidebarOpen || showTooltip($el, '{{ $groupTitle }}')"
                            @mouseleave="hideTooltip()" :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                            class="w-full flex items-center gap-3 px-3 py-2 mb-1 rounded-lg text-lg font-bold uppercase tracking-wider text-gray-500 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak class="truncate flex-1">{{ $groupTitle }}</span>
                            <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 transition-transform duration-200 shrink-0"
                                :class="isGroupOpen({{ json_encode($groupTitle) }}) ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="isGroupOpen({{ json_encode($groupTitle) }})" x-cloak class="space-y-1">
                            @foreach ($items as $item)
                                @if (!empty($item['adminOnly']) && auth()->user()->role !== 'admin')
                                    @continue
                                @endif
                                <a href="{{ route($item['route']) }}"
                                    @mouseenter="sidebarOpen || showTooltip($el, '{{ $item['label'] }}')"
                                    @mouseleave="hideTooltip()"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
                                           {{ $isNavActive($item['route']) ? 'bg-ris-primary text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="w-5 h-5 shrink-0 {{ $isNavActive($item['route']) ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span x-show="sidebarOpen" x-cloak
                                        class="whitespace-nowrap">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            {{-- Sidebar Toggle --}}
            <div class="border-t border-white/10 p-3 shrink-0">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-gray-400 hover:bg-white/10 hover:text-white transition-colors text-sm cursor-pointer">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="!sidebarOpen && 'rotate-180'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>সংকুচিত করুন</span>
                </button>
            </div>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="mobileSidebar" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            @click="mobileSidebar = false">
        </div>

        {{-- Mobile Sidebar Drawer --}}
        <aside x-show="mobileSidebar" x-cloak x-transition:enter="transition-transform ease-in-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform ease-in-out duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 w-64 bg-ris-dark-900 text-white z-50 lg:hidden flex flex-col">
            <div class="flex items-center justify-between px-4 h-16 border-b border-white/10 shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="RIS" class="h-10 w-auto object-contain">
                    <div class="font-heading font-bold text-sm text-white">RIS অ্যাডমিন</div>
                </div>
                <button @click="mobileSidebar = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-white/10 hover:text-white cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 sidebar-scroll overflow-y-auto py-4 px-3 space-y-1" x-data="navGroups({{ json_encode($activeGroup) }})">
                <a href="{{ route($dashboardItem['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all mb-2
                       {{ $isNavActive($dashboardItem['route']) ? 'bg-ris-primary text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ $isNavActive($dashboardItem['route']) ? 'text-white' : 'text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $dashboardItem['icon'] !!}
                    </svg>
                    <span class="whitespace-nowrap">{{ $dashboardItem['label'] }}</span>
                </a>
                @foreach ($sidebarGroups as $groupTitle => $items)
                    <div class="pt-4 first:pt-0">
                        <button @click="toggleGroup({{ json_encode($groupTitle) }})"
                            class="w-full flex items-center gap-3 px-3 py-2 mb-1 rounded-lg text-[11px] font-bold uppercase tracking-wider text-gray-500 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                            </svg>
                            <span class="truncate flex-1 text-sm">{{ $groupTitle }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200 shrink-0"
                                :class="isGroupOpen({{ json_encode($groupTitle) }}) ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="isGroupOpen({{ json_encode($groupTitle) }})" x-cloak class="space-y-1">
                            @foreach ($items as $item)
                                @if (!empty($item['adminOnly']) && auth()->user()->role !== 'admin')
                                    @continue
                                @endif
                                <a href="{{ route($item['route']) }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                                       {{ $isNavActive($item['route']) ? 'bg-ris-primary text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="w-5 h-5 shrink-0 {{ $isNavActive($item['route']) ? 'text-white' : 'text-gray-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- ═══ Main Area ═══ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header
                class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-3">
                    {{-- Mobile hamburger --}}
                    <button @click="mobileSidebar = !mobileSidebar"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Search --}}
                    <div class="hidden sm:flex items-center bg-gray-100 rounded-lg px-3 py-2 w-64 lg:w-80">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="অনুসন্ধান করুন..."
                            class="bg-transparent border-none outline-none text-sm ml-2 w-full placeholder-gray-400">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $unreadCount = Auth::user()->unreadNotifications()->count();
                        $recentNotifications = Auth::user()->notifications()->latest()->take(7)->get();
                    @endphp

                    {{-- Notifications --}}
                    <div x-data="{ notifOpen: false, unread: {{ $unreadCount }} }" @click.outside="notifOpen = false" class="relative">
                        <button @click="notifOpen = !notifOpen"
                            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <template x-if="unread > 0">
                                <span
                                    class="absolute -top-0.5 -right-0.5 min-w-5 h-5 px-1 rounded-full bg-ris-light text-white text-[11px] font-semibold flex items-center justify-center"
                                    x-text="unread"></span>
                            </template>
                        </button>

                        <div x-show="notifOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <div class="text-sm font-semibold text-ris-dark">নোটিফিকেশন</div>
                                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-ris-primary hover:underline cursor-pointer">সব পড়া
                                        হয়েছে</button>
                                </form>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse ($recentNotifications as $notification)
                                    <a href="{{ route('admin.notifications.read', $notification) }}"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-ris-primary/5' }}">
                                        <span
                                            class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $notification->read_at ? 'bg-gray-200' : 'bg-ris-light' }}"></span>
                                        <span class="flex-1 min-w-0">
                                            <span
                                                class="block text-sm font-medium text-gray-800">{{ $notification->data['title'] ?? 'নোটিফিকেশন' }}</span>
                                            <span
                                                class="block text-xs text-gray-500 mt-0.5 truncate">{{ $notification->data['message'] ?? '' }}</span>
                                            <span
                                                class="block text-[11px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <div class="px-4 py-10 text-center text-sm text-gray-400">কোনো নোটিফিকেশন নেই।
                                    </div>
                                @endforelse
                            </div>
                            <a href="{{ route('admin.notifications.index') }}"
                                class="block text-center text-sm font-medium text-ris-primary py-2.5 border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                সব নোটিফিকেশন দেখুন
                            </a>
                        </div>
                    </div>

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div
                                class="w-8 h-8 rounded-full gradient-logo flex items-center justify-center text-white text-sm font-heading font-semibold overflow-hidden shrink-0">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}"
                                        alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                                @endif
                            </div>
                            <span
                                class="hidden sm:block text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'অ্যাডমিন' }}</span>
                            <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('admin.profile.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">প্রোফাইল</a>
                            <a href="{{ route('admin.profile.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">সেটিংস</a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer">লগ
                                    আউট</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 lg:p-8 max-w-8xl mx-auto w-full">

                    {{-- Breadcrumb --}}
                    @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                            <a href="{{ route('admin.dashboard') }}"
                                class="hover:text-ris-primary transition-colors">হোম</a>
                            @foreach ($breadcrumbs as $label => $url)
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                @if ($url)
                                    <a href="{{ $url }}"
                                        class="hover:text-ris-primary transition-colors">{{ $label }}</a>
                                @else
                                    <span class="text-gray-800 font-medium">{{ $label }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Tooltip --}}
    <div x-show="tooltip.show" x-cloak :style="'top:' + tooltip.y + 'px; left:' + tooltip.x + 'px'"
        class="fixed z-9999 -translate-y-1/2 px-2.5 py-1.5 rounded-md bg-gray-900 text-white text-xs font-medium whitespace-nowrap shadow-lg pointer-events-none"
        x-text="tooltip.text">
    </div>

    @livewireScripts

    {{-- Toast --}}
    <div x-data="{ toasts: [], show: false }" x-init="@if (session('success')) toasts.push({ type: 'success', message: '{{ session('success') }}' });
                show = true;
                setTimeout(() => { toasts.shift(); if(!toasts.length) show = false; }, 3000); @endif
    @if (session('error')) toasts.push({ type: 'error', message: '{{ session('error') }}' });
                show = true;
                setTimeout(() => { toasts.shift(); if(!toasts.length) show = false; }, 3000); @endif" class="fixed top-5 right-5 z-9999 space-y-3">
        <template x-for="(toast, index) in toasts" :key="index">
            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' :
                    'bg-red-50 border-red-200 text-red-700'"
                class="flex items-center gap-3 px-5 py-3 rounded-xl border shadow-lg min-w-75 max-w-112.5">
                <svg x-show="toast.type === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium" x-text="toast.message"></span>
                <button @click="toasts.splice(index, 1); if(!toasts.length) show = false;"
                    class="ml-auto shrink-0 opacity-60 hover:opacity-100 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @yield('scripts')

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
        window.navGroups = function(activeGroup) {
            return {
                openGroup: localStorage.getItem('risNavOpenGroup') || activeGroup,
                isGroupOpen: function(group) {
                    return this.openGroup === group;
                },
                toggleGroup: function(group) {
                    this.openGroup = this.openGroup === group ? null : group;
                    if (this.openGroup) {
                        localStorage.setItem('risNavOpenGroup', this.openGroup);
                    } else {
                        localStorage.removeItem('risNavOpenGroup');
                    }
                },
            };
        };
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) location.reload();
        });
    </script>
</body>

</html>
