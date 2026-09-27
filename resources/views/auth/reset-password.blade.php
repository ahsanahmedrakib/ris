@extends('layouts.website')

@section('title', 'নতুন পাসওয়ার্ড — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-50 border-b border-gray-100 px-8 py-8 text-center">
                    <h1 class="font-heading font-bold text-xl text-ris-primary">নতুন পাসওয়ার্ড নির্ধারণ করুন</h1>
                    <p class="text-gray-500 text-sm mt-1">
                        পাসওয়ার্ড পরিবর্তনের পর অন্য সব ডিভাইস থেকে লগআউট হয়ে যাবে।
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

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5"
                        x-data="{ submitting: false, showPassword: false }" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল ঠিকানা</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                        </div>

                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 mb-1.5">নতুন পাসওয়ার্ড</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    :type="showPassword ? 'text' : 'password'"
                                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                    placeholder="কমপক্ষে ৮ অক্ষর">
                                <button type="button" @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'পাসওয়ার্ড লুকান' : 'পাসওয়ার্ড দেখুন'"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-ris-primary transition-colors cursor-pointer">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 013 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1.5">নতুন পাসওয়ার্ড নিশ্চিত করুন</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="আবার লিখুন">
                        </div>

                        <button type="submit" :disabled="submitting"
                            :class="submitting && 'opacity-60 cursor-not-allowed'"
                            class="w-full btn-primary py-3 text-center justify-center cursor-pointer">
                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                সংরক্ষণ হচ্ছে...
                            </span>
                            <span x-show="!submitting" class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                পাসওয়ার্ড পরিবর্তন করুন
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-ris-gray hover:text-ris-primary transition-colors">
                    ← লগইন পেজে ফিরুন
                </a>
            </div>
        </div>
    </div>
@endsection
