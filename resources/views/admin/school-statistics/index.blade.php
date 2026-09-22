@extends('layouts.admin')

@section('title', 'পরিসংখ্যান')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">আমাদের স্কুল এক নজরে</h1>
                <p class="text-sm text-gray-500 mt-1">হোমপেজের পরিসংখ্যান সেকশন পরিচালনা করুন</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm font-medium">
                {{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="mb-5">
                <h2 class="font-heading font-bold text-lg text-gray-900">পরিসংখ্যান</h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if ($statistic)
                        প্রথমবার যোগ করা হয়েছে। সংখ্যা পরিবর্তন করে সংরক্ষণ করুন — ওয়েবসাইটে সরাসরি প্রতিফলিত হবে।
                    @else
                        প্রথমবার যোগ করুন, পরে সম্পাদনা করুন। এখানে যা থাকবে তাই ওয়েবসাইটে দেখানো হবে।
                    @endif
                </p>
            </div>

            @if ($statistic)
                <form action="{{ route('admin.school-statistics.update', $statistic->id) }}" method="POST"
                    class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">মোট ছাত্র-ছাত্রী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_students" min="0" max="100000"
                                value="{{ old('total_students', $statistic->total_students) }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_students')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">শিক্ষকমণ্ডলী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_teachers" min="0" max="10000"
                                value="{{ old('total_teachers', $statistic->total_teachers) }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_teachers')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_classes" min="0" max="100"
                                value="{{ old('total_classes', $statistic->total_classes) }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_classes')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">কর্মচারী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_staff" min="0" max="10000"
                                value="{{ old('total_staff', $statistic->total_staff) }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_staff')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">প্রতিষ্ঠাকাল (সাল) <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="founding_year" min="1900" max="{{ (int) date('Y') }}"
                                value="{{ old('founding_year', $statistic->founding_year) }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            <p class="mt-1 text-xs text-gray-400">বর্তমান সাল থেকে বাদ দিয়ে
                                "বছরের অভিজ্ঞতা" হিসাব করা হবে।</p>
                            @error('founding_year')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">সংরক্ষণ
                            করুন</button>
                    </div>
                </form>
            @else
                <form action="{{ route('admin.school-statistics.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">মোট ছাত্র-ছাত্রী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_students" min="0" max="100000"
                                value="{{ old('total_students') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_students')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">শিক্ষকমণ্ডলী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_teachers" min="0" max="10000"
                                value="{{ old('total_teachers') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_teachers')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_classes" min="0" max="100"
                                value="{{ old('total_classes') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_classes')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">কর্মচারী <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_staff" min="0" max="10000"
                                value="{{ old('total_staff') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('total_staff')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">প্রতিষ্ঠাকাল (সাল) <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="founding_year" min="1900" max="{{ (int) date('Y') }}"
                                value="{{ old('founding_year') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            <p class="mt-1 text-xs text-gray-400">বর্তমান সাল থেকে বাদ দিয়ে
                                "বছরের অভিজ্ঞতা" হিসাব করা হবে।</p>
                            @error('founding_year')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">যোগ
                            করুন</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
