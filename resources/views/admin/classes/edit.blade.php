@extends('layouts.admin')

@section('title', 'শ্রেণি সম্পাদনা')

@section('content')
    <div class="space-y-6">

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.classes.index') }}"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">শ্রেণি সম্পাদনা</h1>
                <p class="text-sm text-gray-500 mt-1">শ্রেণির তথ্য হালনাগাদ করুন</p>
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

        <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-semibold text-gray-900">শ্রেণির তথ্য</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণির নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">সেকশন</label>
                            <input type="text" name="section" value="{{ old('section', $class->section) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: ক, খ, গ">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শিক্ষাবর্ষ <span
                                    class="text-red-500">*</span></label>
                            <select name="academic_year_id" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">শিক্ষাবর্ষ নির্বাচন করুন</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ old('academic_year_id', $class->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণি শিক্ষক</label>
                            <select name="class_teacher_id"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">শিক্ষক নির্বাচন করুন</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}"
                                        {{ old('class_teacher_id', $class->class_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.classes.index') }}"
                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    বাতিল
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    হালনাগাদ করুন
                </button>
            </div>
        </form>

    </div>
@endsection
