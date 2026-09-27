@extends('layouts.website')

@section('title', 'পাসওয়ার্ড ভুলে গেছেন — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-50 border-b border-gray-100 px-8 py-8 text-center">
                    <h1 class="font-heading font-bold text-xl text-ris-primary">পাসওয়ার্ড রিসেট</h1>
                    <p class="text-gray-500 text-sm mt-1">
                        আপনার অ্যাকাউন্টের ইমেইল দিন। আমরা একটি রিসেট লিংক পাঠাবো।
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

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল ঠিকানা</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                                placeholder="আপনার ইমেইল ঠিকানা লিখুন">
                        </div>

                        <button type="submit" class="w-full btn-primary py-3 text-center justify-center cursor-pointer">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l18 7-18 7V8z" />
                                </svg>
                                রিসেট লিংক পাঠান
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Back to login --}}
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-ris-gray hover:text-ris-primary transition-colors">
                    ← লগইন পেজে ফিরুন
                </a>
            </div>
        </div>
    </div>
@endsection
