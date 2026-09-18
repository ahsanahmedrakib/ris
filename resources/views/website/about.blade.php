@extends('layouts.website')

@section('title', 'আমাদের সম্পর্কে — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের সম্পর্কে</h1>
            <p class="mt-3 text-white/70 text-lg">জ্ঞান ও মূল্যবোধের সমন্বয়ে গড়ে উঠছে আগামীর প্রজন্ম</p>
        </div>
    </section>

    {{-- School History --}}
    <section class="py-16 sm:py-20 bg-white relative overflow-hidden">
        <div class="absolute top-10 right-10 w-32 h-32 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="reveal-left">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">আমাদের
                        ইতিহাস</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark leading-tight">
                        যাত্রা শুরু হয়েছিল একটি স্বপ্ন থেকে
                    </h2>
                    <p class="mt-5 text-gray-600 leading-relaxed">
                        রেশমা ইন্টারন্যাশনাল স্কুল ২০১৫ সালে গোপালগঞ্জের ঘুল্লিবাড়ি মোড় ৪৩৯ নং ঠিকানায় প্রতিষ্ঠিত হয়।
                        প্রতিষ্ঠাকালে মাত্র ৫০
                        জন ছাত্র নিয়ে যাত্রা শুরু করলেও আজ ৫০০-র বেশি ছাত্র আমাদের পরিবারের অংশ।
                    </p>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        আমাদের প্রতিষ্ঠাতাদের স্বপ্ন ছিল একটি এমন শিক্ষাপ্রতিষ্ঠান গড়ে তোলা যেখানে প্রতিটি শিশু তার সুষ্ঠু
                        বিকাশ ঘটাতে পারবে। আজ আমরা সেই স্বপ্নকে বাস্তবায়নে সফল হচ্ছি।
                    </p>
                </div>
                <div
                    class="rounded-2xl flex items-center justify-center reveal-right animate-float-slow">
                    <img src="{{ asset('images/about/ris.png') }}" alt="Resma International School"
                        class="w-auto object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- Mission & Vision --}}
    @if ($mission || $vision)
        <section class="py-16 sm:py-20 bg-ris-primary-50/40 section-pattern-grid relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-ris-accent/5 rounded-full pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid {{ $mission && $vision ? 'md:grid-cols-2 gap-8' : 'md:grid-cols-1 max-w-2xl mx-auto gap-8' }} reveal-stagger">
                    @if ($mission)
                        <div class="card p-8 reveal">
                            <div class="w-14 h-14 rounded-2xl bg-ris-primary/10 flex items-center justify-center mb-5">
                                <svg class="w-7 h-7 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="font-heading font-bold text-xl text-ris-dark">{{ $mission->title }}</h3>
                            <p class="mt-4 text-gray-600 leading-relaxed">{{ $mission->content }}</p>
                        </div>
                    @endif
                    @if ($vision)
                        <div class="card p-8 reveal">
                            <div class="w-14 h-14 rounded-2xl bg-ris-primary/10 flex items-center justify-center mb-5">
                                <svg class="w-7 h-7 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <h3 class="font-heading font-bold text-xl text-ris-dark">{{ $vision->title }}</h3>
                            <p class="mt-4 text-gray-600 leading-relaxed">{{ $vision->content }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Core Values --}}
    @if ($coreValues->count())
        <section class="py-16 sm:py-20 bg-ris-gray-50 section-pattern-diagonal relative overflow-hidden">
            <div class="absolute top-10 left-10 w-20 h-20 border-2 border-ris-primary/10 rounded-full pointer-events-none"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">মূল্যবোধ</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">আমাদের মূল নীতিসমূহ</h2>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                    @foreach ($coreValues as $value)
                        <div class="card p-6 text-center reveal">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-ris-primary/10 flex items-center justify-center mb-4">
                                <span class="font-heading font-bold text-xl text-ris-primary">{{ $loop->iteration }}</span>
                            </div>
                            <h3 class="font-heading font-bold text-ris-dark">{{ $value->title }}</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $value->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
