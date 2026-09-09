@extends('layouts.website')

@section('title', 'মেধাবৃত্তি রেজিস্ট্রেশন — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-14 sm:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white/90 text-xs font-medium mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি ২০২৬
            </span>
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">মেধাবৃত্তি রেজিস্ট্রেশন</h1>
            <p class="mt-3 text-white/70 text-lg max-w-2xl mx-auto">
                শ্রেণি ও রোল নম্বর সহ সঠিকভাবে ফরমটি পূরণ করুন। ফরম জমা দেওয়ার আগে একটি পপ-আপে তথ্য যাচাই করা হবে।
            </p>
        </div>
    </section>

    {{-- Registration Form --}}
    <section class="py-12 sm:py-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="reveal">
                @livewire('scholarship.registration-form')
            </div>

            <div class="mt-8 bg-white rounded-xl border border-gay-200 p-5 text-center reveal">
                <p class="text-sm text-gray-500">
                    কোনো সমস্যা হলে যোগাযোগ করুন
                    <a href="tel:+8801619007006" class="text-ris-primary font-medium hover:underline">+৮৮০-১৬১৯ ০০৭ ০০৬</a>
                </p>
            </div>
        </div>
    </section>

@endsection
