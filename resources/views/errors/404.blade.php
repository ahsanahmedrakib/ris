<!DOCTYPE html>
<html lang="bn" dir="ltr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>পৃষ্ঠা পাওয়া যায়নি — রেশমা ইন্টারন্যাশনাল স্কুল</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-body antialiased text-gray-800 bg-white min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-ris-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg bg-ris-primary flex items-center justify-center">
                        <span class="text-white font-heading font-bold text-sm">রেশমা</span>
                    </div>
                    <span class="font-heading font-semibold text-sm sm:text-base">রেশমা ইন্টারন্যাশনাল স্কুল</span>
                </a>
                <a href="{{ url('/') }}"
                    class="text-sm text-gray-300 hover:text-white transition-colors">
                    হোম পেজ
                </a>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="text-center max-w-md">
            <div class="mb-8">
                <span class="text-8xl font-heading font-bold text-ris-primary/20">৪০৪</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-ris-dark mb-4">
                পৃষ্ঠা পাওয়া যায়নি
            </h1>
            <p class="text-gray-500 leading-relaxed mb-8">
                আপনি যে পৃষ্ঠাটি খুঁজছেন সেটি বিদ্যমান নেই বা সরানো হয়েছে।
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-ris-primary text-white font-heading font-semibold rounded-xl hover:bg-ris-primary/90 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    হোম পেজে ফিরুন
                </a>
                <button onclick="history.back()"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-heading font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    আগের পৃষ্ঠায় ফিরুন
                </button>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-ris-dark text-gray-400 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} রেশমা ইন্টারন্যাশনাল স্কুল। সর্বস্বত্ব সংরক্ষিত।</p>
        </div>
    </footer>

</body>

</html>
