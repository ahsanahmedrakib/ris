@extends('layouts.admin')

@section('title', 'মেধাবৃত্তি রেজিস্ট্রেশন')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.scholarship.index') }}"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">মেধাবৃত্তি রেজিস্ট্রেশন</h1>
                <p class="text-sm text-gray-500 mt-1">রেজিস্ট্রেশনের সম্পূর্ণ তথ্য</p>
            </div>
        </div>

        <div class="max-w-3xl bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Header Band --}}
            <div class="gradient-logo px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-heading font-bold text-white text-lg">আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি
                            ২০২৬</h2>
                        <p class="text-white/80 text-sm">রেজিস্ট্রেশন ফরম</p>
                    </div>
                    <span
                        class="inline-flex self-start items-center px-4 py-2 bg-white/15 text-white font-heading font-bold rounded-lg tracking-wider">{{ $registration->registration_no }}</span>
                </div>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                    <div>
                        <dt class="text-gray-500 mb-1">শিক্ষার্থীর নাম</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->student_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">পিতার নাম</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->father_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">মাতার নাম</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->mother_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">স্কুলের নাম</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->school_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                        <dd class="font-medium text-gray-900">{{ $classes[$registration->class_no] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">রোল নং</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->roll_no ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">মোবাইল নং</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->mobile_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">পেমেন্ট মাধ্যম</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $registration->payment_method === 'cash' ? 'ক্যাশ' : 'বিকাশ' }}</dd>
                    </div>
                    @if ($registration->payment_method !== 'cash')
                        <div>
                            <dt class="text-gray-500 mb-1">বিকাশ (টাকা পাঠানো হয়েছে)</dt>
                            <dd class="font-medium text-gray-900">{{ $registration->bkash_no }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                        <dd class="font-medium">
                            @if ($registration->status === 'pending')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    পেন্ডিং
                                </span>
                            @elseif($registration->status === 'approved')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    অ্যাকসেপ্ট
                                </span>
                            @elseif($registration->status === 'rejected')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    রিজেক্টেড
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $registration->status }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">রেজিস্ট্রেশন তারিখ</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->created_at->format('d/m/Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">তৈরি করেছেন</dt>
                        <dd class="font-medium text-gray-900">{{ $registration->creator?->name ?? 'অনলাইন (শিক্ষার্থী)' }}
                        </dd>
                    </div>
                </dl>

                {{-- Status Update Section --}}
                <div class="mt-6 pt-5 border-t border-gray-100">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">স্ট্যাটাস পরিবর্তন করুন</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($registration->status !== 'approved')
                            <form method="POST" action="{{ route('admin.scholarship.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-500 text-white text-sm font-medium rounded-lg hover:bg-emerald-600 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    অনুমোদন করুন
                                </button>
                            </form>
                        @endif

                        @if ($registration->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.scholarship.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    প্রত্যাখ্যান করুন
                                </button>
                            </form>
                        @endif

                        @if ($registration->status !== 'pending')
                            <form method="POST" action="{{ route('admin.scholarship.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="pending">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    পেন্ডিং করুন
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div
                    class="mt-6 pt-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-400">বিকাশ পার্সোনাল নম্বর: <span
                            class="font-semibold text-gray-600">০১৬১৮১৯৭৯৭২</span></p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.scholarship.edit', $registration) }}"
                            class="px-5 py-2.5 bg-amber-50 text-amber-600 text-sm font-medium rounded-lg hover:bg-amber-100 transition-colors">
                            সম্পাদনা
                        </a>
                        <a href="{{ route('admin.scholarship.pdf', $registration) }}" target="_blank"
                            class="px-5 py-2.5 bg-emerald-50 text-emerald-600 text-sm font-medium rounded-lg hover:bg-emerald-100 transition-colors">
                            PDF প্রিন্ট
                        </a>
                        <a href="{{ route('admin.scholarship.index') }}"
                            class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            তালিকায় ফিরুন
                        </a>
                        <form method="POST" action="{{ route('admin.scholarship.destroy', $registration) }}"
                            onsubmit="return confirm('আপনি কি নিশ্চিত এই রেজিস্ট্রেশনটি মুছে ফেলতে চান?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-5 py-2.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                মুছে ফেলুন
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
