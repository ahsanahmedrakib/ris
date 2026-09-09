@extends('layouts.website')

@section('title', 'ভর্তি — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">ভর্তি তথ্য</h1>
            <p class="mt-3 text-white/70 text-lg">২০২৬ শিক্ষাবর্ষের ভর্তি প্রক্রিয়া সম্পর্কে জানুন</p>
        </div>
    </section>

    {{-- Admission Process --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ভর্তি
                    প্রক্রিয়া</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">কিভাবে ভর্তি হবেন</h2>
            </div>
            @php
                $steps = [
                    [
                        'num' => '০১',
                        'title' => 'ফরম সংগ্রহ',
                        'desc' => 'স্কুল ক্যাম্পাস থেকে বা অনলাইনে ভর্তি ফরম সংগ্রহ করুন।',
                    ],
                    [
                        'num' => '০২',
                        'title' => 'ফরম পূরণ',
                        'desc' => 'সঠিকভাবে ফরম পূরণ করে প্রয়োজনীয় কাগজপত্র সংযুক্ত করুন।',
                    ],
                    [
                        'num' => '০৩',
                        'title' => 'ভর্তি পরীক্ষা',
                        'desc' => 'নির্ধারিত সময়ে ভর্তি পরীক্ষায় অংশগ্রহণ করুন।',
                    ],
                    [
                        'num' => '০৪',
                        'title' => 'ফলাফল ও ভর্তি',
                        'desc' => 'পরীক্ষার ফলাফল ঘোষণার পর ভর্তি ফি পরিশোধ করে ভর্তি নিশ্চিত করুন।',
                    ],
                ];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                @foreach ($steps as $step)
                    <div class="card p-6 text-center relative reveal">
                        <div class="w-14 h-14 mx-auto rounded-2xl gradient-logo flex items-center justify-center mb-4">
                            <span class="font-heading font-bold text-lg text-white">{{ $step['num'] }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-ris-dark">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Requirements --}}
    <section class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="reveal-left">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">প্রয়োজনীয়
                        কাগজপত্র</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">ভর্তির জন্য প্রয়োজন</h2>
                    <ul class="mt-6 space-y-3">
                        @foreach (['জন্ম নিবন্ধন পত্রের ছবি', 'অভিভাবকের জাতীয় পরিচয়পত্রের ছবি', 'পূর্ববর্তী শিক্ষাপ্রতিষ্ঠানের সনদপত্র', '৪ কপি পাসপোর্ট সাইজ ছবি', 'ভর্তি ফরম (সঠিকভাবে পূরণ করা)'] as $req)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="reveal-right">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ফি
                        কাঠামো</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">বার্ষিক ফি</h2>
                    <div class="mt-6 space-y-4">
                        @php
                            $fees = [
                                ['class' => 'প্লে', 'amount' => '১০,০০০ টাকা'],
                                ['class' => 'নার্সারি', 'amount' => '১১,০০০ টাকা'],
                                ['class' => 'প্রাথমিক (১ম-৫ম)', 'amount' => '১২,০০০ টাকা'],
                            ];
                        @endphp
                        @foreach ($fees as $fee)
                            <div class="card p-4 flex items-center justify-between">
                                <span class="font-heading font-medium text-ris-dark">{{ $fee['class'] }}</span>
                                <span class="badge">{{ $fee['amount'] }}</span>
                            </div>
                        @endforeach
                        <p class="text-sm text-gray-500 mt-4">* ফি কাঠামো পরিবর্তনযোগ্য। বিস্তারিত জানতে যোগাযোগ করুন।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h2 class="font-heading font-bold text-3xl sm:text-4xl text-white">এখনই ভর্তি করুন</h2>
            <p class="mt-4 text-white/70 text-lg">আজই যোগাযোগ করুন বা স্কুল ক্যাম্পাসে আসুন</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('contact') }}"
                    class="btn-primary bg-white text-ris-primary hover:bg-gray-100 shadow-lg shadow-black/20 px-8 py-3">যোগাযোগ
                    করুন</a>
            </div>
        </div>
    </section>

@endsection
