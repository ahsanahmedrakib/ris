@extends('layouts.admin')

@section('title', 'নতুন পরীক্ষা যোগ করুন')

@section('content')
    <div class="space-y-6">

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.exams.index') }}"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">নতুন পরীক্ষা যোগ করুন</h1>
                <p class="text-sm text-gray-500 mt-1">নতুন পরীক্ষার তথ্য ফর্ম পূরণ করুন</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</h3>
                        <ul class="mt-2 space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        পরীক্ষার তথ্য
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">পরীক্ষার নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: অর্ধবার্ষিক পরীক্ষা">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ধরন <span
                                    class="text-red-500">*</span></label>
                            <select name="type" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">ধরন নির্বাচন করুন</option>
                                <option value="midterm" {{ old('type') === 'midterm' ? 'selected' : '' }}>অর্ধবার্ষিক
                                </option>
                                <option value="final" {{ old('type') === 'final' ? 'selected' : '' }}>বার্ষিক</option>
                                <option value="quarterly" {{ old('type') === 'quarterly' ? 'selected' : '' }}>ত্রৈমাসিক
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <select name="class_id" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">শ্রেণি নির্বাচন করুন</option>
                                @foreach ($classes ?? [] as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শুরুর তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শেষের তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">মোট নম্বর <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_marks" value="{{ old('total_marks') }}" required
                                min="1"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: 100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">পাস নম্বর <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="pass_marks" value="{{ old('pass_marks') }}" required min="1"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: 33">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.exams.index') }}"
                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    বাতিল
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    সংরক্ষণ করুন
                </button>
            </div>
        </form>

    </div>
@endsection
