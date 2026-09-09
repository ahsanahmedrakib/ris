<!DOCTYPE html>
<html lang="bn" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'অভিভাবক পোর্টাল') — রেশমা ইন্টারন্যাশনাল স্কুল</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="h-full font-body antialiased bg-gray-50 text-gray-800" x-data="{ mobileSidebar: false }">

    <div class="flex h-full">

        {{-- ═══ Sidebar ═══ --}}
        <aside class="hidden lg:flex lg:flex-col w-64 bg-ris-dark-900 text-white shrink-0">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10">
                <img src="{{ asset('logo.png') }}" alt="RIS" class="h-10 w-auto object-contain">
                <div>
                    <div class="font-heading font-bold text-sm text-white leading-tight">RIS</div>
                    <div class="text-[10px] text-gray-400">অভিভাবক পোর্টাল</div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @php
                    $parentNavItems = [
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                            'label' => 'ড্যাশবোর্ড',
                            'route' => 'parent.dashboard',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'label' => 'আমার সন্তান',
                            'route' => 'parent.children',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                            'label' => 'উপস্থিতি',
                            'route' => 'parent.attendance',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                            'label' => 'ফি',
                            'route' => 'parent.fees',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
                            'label' => 'নোটিশ',
                            'route' => 'parent.notices',
                        ],
                        [
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                            'label' => 'পরীক্ষা',
                            'route' => 'parent.exams',
                        ],
                    ];
                @endphp

                @foreach ($parentNavItems as $item)
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                       {{ request()->routeIs($item['route']) ? 'bg-ris-primary text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($item['route']) ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Back to Website --}}
            <div class="border-t border-white/10 p-3">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>ওয়েবসাইটে ফিরুন</span>
                </a>
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
            x-transition:leave="transition-transform ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 w-64 bg-ris-dark-900 text-white z-50 lg:hidden flex flex-col">
            <div class="flex items-center justify-between px-4 h-16 border-b border-white/10 shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="RIS" class="h-10 w-auto object-contain">
                    <div class="font-heading font-bold text-sm">RIS</div>
                </div>
                <button @click="mobileSidebar = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-white/10 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @foreach ($parentNavItems as $item)
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                       {{ request()->routeIs($item['route']) ? 'bg-ris-primary text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($item['route']) ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- ═══ Main Area ═══ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header
                class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="mobileSidebar = !mobileSidebar"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="font-heading font-semibold text-lg text-ris-dark">@yield('page-title', 'ড্যাশবোর্ড')</h1>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Notifications --}}
                    <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-ris-light rounded-full"></span>
                    </button>

                    {{-- User --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <div
                                class="w-8 h-8 rounded-full gradient-logo flex items-center justify-center text-white text-sm font-heading font-semibold">
                                {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                            </div>
                            <span
                                class="hidden sm:block text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'অভিভাবক' }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">প্রোফাইল</a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">লগ
                                    আউট</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
