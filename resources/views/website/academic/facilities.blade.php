@extends('layouts.website')

@section('title', 'স্কুলের সুবিধা — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">স্কুলের সুবিধা</h1>
            <p class="mt-3 text-white/70 text-lg">আমাদের আধুনিক সুবিধাসমূহ</p>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 right-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @php
                $facilities = [
                    [
                        'title' => 'ডিজিটাল ক্লাসরুম',
                        'desc' => 'আধুনিক মাল্টিমিডিয়া প্রজেক্টর, ইন্টারঅ্যাক্টিভ স্মার্ট বোর্ড এবং পর্যাপ্ত কম্পিউটারে সজ্জিত ক্লাসরুম।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                        'color' => 'from-blue-500 to-indigo-500',
                    ],
                    [
                        'title' => 'লাইব্রেরি',
                        'desc' => 'হাজার হাজার বই ও রিসোর্স নিয়ে সমৃদ্ধ গ্রন্থাগার, পড়ার জন্য শান্ত ও আরামদায়ক পরিবেশ।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                        'color' => 'from-emerald-500 to-teal-500',
                    ],
                    [
                        'title' => 'বিজ্ঞানাগার',
                        'desc' => 'পদার্থবিজ্ঞান, রসায়ন ও জীববিজ্ঞান — তিনটি সুসজ্জিত ল্যাব যাতে হাতে-কলমে শিক্ষার সুযোগ।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
                        'color' => 'from-amber-500 to-orange-500',
                    ],
                    [
                        'title' => 'কম্পিউটার ল্যাব',
                        'desc' => 'আধুনিক কম্পিউটার ও দ্রুতগতির ইন্টারনেট সংযোগে সমৃদ্ধ আইটি ল্যাব।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>',
                        'color' => 'from-cyan-500 to-blue-500',
                    ],
                    [
                        'title' => 'ক্রীড়াঙ্গন',
                        'desc' => 'বড় খেলার মাঠ, ক্রিকেট, ফুটবল ও অন্যান্য ক্রীড়া সুবিধা।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'color' => 'from-rose-500 to-pink-500',
                    ],
                    [
                        'title' => 'পরিবহন',
                        'desc' => 'নিরাপদ ও আরামদায়ক স্কুল বাস সার্ভিস, প্রতিটি রুটে সুপারভাইজার নিয়োজিত।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8m-8 5h4m5-6H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>',
                        'color' => 'from-violet-500 to-purple-500',
                    ],
                    [
                        'title' => 'নিরাপত্তা',
                        'desc' => '২৪ ঘণ্টা সিসিটিভি নিগরানী, ফায়ার সেফটি ও সুসংগঠিত নিরাপত্তা ব্যবস্থা।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        'color' => 'from-green-500 to-emerald-500',
                    ],
                    [
                        'title' => 'চিকিৎসা সুবিধা',
                        'desc' => 'স্কুল ক্যাম্পাসে স্বাস্থ্যসেবা কেন্দ্র, প্রতিকারের জন্য প্রশিক্ষিত কর্মী।',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                        'color' => 'from-red-400 to-rose-500',
                    ],
                ];
            @endphp

            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <h2 class="font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আমাদের সুবিধাসমূহ</h2>
                <p class="mt-3 text-gray-500">শিক্ষার্থীদের সর্বোত্তম অভিজ্ঞতা নিশ্চিত করার জন্য আমরা বিভিন্ন আধুনিক সুবিধা প্রদান করি।</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                @foreach ($facilities as $facility)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1 group reveal">
                        <div class="relative h-36 bg-linear-to-br {{ $facility['color'] }} flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <svg class="w-14 h-14 text-white relative z-10 drop-shadow-lg group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $facility['icon'] !!}
                            </svg>
                        </div>
                        <div class="p-5">
                            <h3 class="font-heading font-bold text-base text-ris-dark group-hover:text-ris-primary transition-colors">{{ $facility['title'] }}</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $facility['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection