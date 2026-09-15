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
                    <p class="text-gray-500 text-sm mt-1">
                        @if ($loginRole === 'admin')
                            অ্যাডমিন প্যানেল — সিস্টেমে প্রবেশ করুন
                        @elseif ($loginRole === 'teacher')
                            শিক্ষক প্যানেল — সিস্টেমে প্রবেশ করুন
                        @elseif ($loginRole === 'parent')
                            অভিভাবক প্যানেল — সিস্টেমে প্রবেশ করুন
                        @else
                            সিস্টেমে প্রবেশ করুন
                        @endif
                    </p>
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

                    <form method="POST" action="{{ route('login') }}" class="space-y-5"
                        x-data="{ submitting: false, showPassword: false }" @submit="submitting = true">
                        @csrf

                        {{-- Email or Username --}}
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল / ইউজারনেম</label>
                            <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="আপনার ইমেইল বা ইউজারনেম লিখুন">
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">পাসওয়ার্ড</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    :type="showPassword ? 'text' : 'password'"
                                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                    placeholder="আপনার পাসওয়ার্ড লিখুন">
                                <button type="button" @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'পাসওয়ার্ড লুকান' : 'পাসওয়ার্ড দেখুন'"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-ris-primary transition-colors">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
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
                        <button type="submit" :disabled="submitting"
                            :class="submitting && 'opacity-60 cursor-not-allowed'"
                            class="w-full btn-primary py-3 text-center justify-center">
                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                লগইন হচ্ছে...
                            </span>
                            <span x-show="!submitting" class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                লগইন করুন
                            </span>
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
