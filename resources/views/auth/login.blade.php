@extends('layouts.website')

@section('title', 'লগইন — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-50 border-b border-gray-100 px-8 py-8 text-center">
                    <img src="{{ asset('logo.png') }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                        class="h-14 lg:h-16 w-auto mx-auto mb-4 object-contain">
                    <h1 class="font-heading font-bold text-xl text-ris-primary">রেশমা ইন্টারন্যাশনাল স্কুল</h1>
                    <p class="text-gray-500 text-sm mt-1">সিস্টেমে প্রবেশ করুন</p>
                </div>

                {{-- Form --}}
                <div class="px-8 py-8">

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="আপনার ইমেইল লিখুন">
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">পাসওয়ার্ড</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="আপনার পাসওয়ার্ড লিখুন">
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember"
                                    class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary/30">
                                <span class="text-sm text-gray-600">মনে রাখুন</span>
                            </label>
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-ris-primary hover:text-ris-dark transition-colors">
                                পাসওয়ার্ড ভুলে গেছেন?
                            </a>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full btn-primary py-3 text-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            লগইন করুন
                        </button>
                    </form>
                </div>
            </div>

            {{-- Back to Home --}}
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-sm text-ris-gray hover:text-ris-primary transition-colors">
                    ← হোমপেজে ফিরুন
                </a>
            </div>
        </div>
    </div>
@endsection
