@extends('layouts.website')

@section('title', 'যোগাযোগ — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">যোগাযোগ</h1>
            <p class="mt-3 text-white/70 text-lg">আমাদের সাথে যোগাযোগ করুন</p>
        </div>
    </section>

    {{-- Contact Info + Form --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">

                {{-- Contact Form --}}
                <div class="reveal-left">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">বার্তা
                        পাঠান</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">আমাদের লিখুন</h2>

                    @if (session('success'))
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" class="mt-6 space-y-5">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">নাম</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                    placeholder="আপনার নাম">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                    placeholder="example@email.com">
                            </div>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">ফোন নম্বর</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="+৮৮০-১৬১৯ ০০৭ ০০৬">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">বিষয়</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="বিষয় লিখুন">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">বার্তা</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all resize-none"
                                placeholder="আপনার বার্তা লিখুন...">{{ old('message') }}</textarea>
                        </div>
                        @error('message')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            পাঠান
                        </button>
                    </form>
                </div>

                {{-- Contact Details --}}
                <div class="space-y-8 reveal-right">
                    <div>
                        <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">যোগাযোগের
                            তথ্য</span>
                        <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">আমাদের সাথে সরাসরি যোগাযোগ করুন</h2>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-ris-dark">ঠিকানা</h4>
                                <p class="mt-1 text-gray-600 text-sm leading-relaxed">২১৭, ঘুল্লিবাড়ি মোড়, গোপালগঞ্জ-৮১০০,
                                    বাংলাদেশ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-ris-dark">ফোন</h4>
                                <a href="tel:+8801619007006"
                                    class="mt-1 block text-gray-600 text-sm hover:text-ris-primary transition-colors">+৮৮০-১৬১৯
                                    ০০৭ ০০৬</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-ris-dark">ইমেইল</h4>
                                <a href="mailto:resmaintlschool@gmail.com"
                                    class="mt-1 block text-gray-600 text-sm hover:text-ris-primary transition-colors">resmaintlschool@gmail.com</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-ris-primary" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-heading font-semibold text-ris-dark">ফেসবুক</h4>
                                    <a href="https://www.facebook.com/Resma.International.School" target="_blank"
                                        rel="noopener"
                                        class="mt-1 block text-gray-600 text-sm hover:text-ris-primary transition-colors">Resma
                                        International School</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

@endsection
