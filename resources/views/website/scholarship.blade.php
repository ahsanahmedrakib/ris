@extends('layouts.website')

@section('content')

    {{-- Hero --}}
    <section class="relative bg-cover bg-center overflow-hidden py-14 sm:py-18"
        style="background-image: url('{{ asset('assets/banner.png') }}')">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light opacity-90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white/90 text-xs font-medium mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি {{ \App\Support\NumberConverter::toBangla($year) }}
            </span>
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">মেধাবৃত্তি রেজিস্ট্রেশন</h1>
            <p class="mt-3 text-white/70 text-lg max-w-2xl mx-auto">
                শ্রেণি ও রোল নম্বর সহ সঠিকভাবে ফরমটি পূরণ করুন। ফরম জমা দেওয়ার আগে একটি পপ-আপে তথ্য যাচাই করা হবে।
            </p>
        </div>
    </section>

    {{-- Registration Form --}}
    <section class="py-12 sm:py-16 bg-ris-gray-50 relative overflow-hidden">
        <div class="absolute top-10 right-10 w-32 h-32 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($setting->isOpenToday())
                <div>
                    @livewire('scholarship.registration-form')
                </div>

                <div class="mt-8 bg-white rounded-xl border border-gay-200 p-5 text-center reveal">
                    <p class="text-xl text-gray-500">
                        কোনো সমস্যা হলে যোগাযোগ করুন
                        <a href="tel:+8801619007006"
                            class="text-ris-primary font-medium hover:underline">+৮৮০-১৬১৯ ০০৭ ০০৬</a>
                    </p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 p-10 text-center reveal">
                    <div
                        class="mx-auto w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="font-heading font-bold text-2xl text-gray-900 mb-2">রেজিস্ট্রেশন বন্ধ আছে</h2>
                    <p class="text-gray-500 max-w-xl mx-auto text-lg">
                        মেধাবৃত্তি রেজিস্ট্রেশনের সময়সীমা শেষ হয়ে গেছে। নতুন সময়সূচি ঘোষণা হলে আমরা আবারও জানাব।
                    </p>
                </div>
            @endif
        </div>
    </section>

@endsection
