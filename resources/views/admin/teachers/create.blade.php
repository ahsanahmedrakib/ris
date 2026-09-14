@extends('layouts.admin')

@section('title', 'নতুন শিক্ষক যোগ')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.teachers.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">নতুন শিক্ষক যোগ</h1>
            <p class="text-sm text-gray-500 mt-1">শিক্ষকের তথ্য পূরণ করুন</p>
        </div>
    </div>

    <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 p-6">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Basic Info --}}
            <div class="sm:col-span-2">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">মৌলিক তথ্য</h3>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ফোন</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>
                <input type="file" name="photo" accept="image/*" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>

            {{-- Professional Info --}}
            <div class="sm:col-span-2 pt-4">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">পেশাদার তথ্য</h3>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">পদবি</label>
                <select name="designation" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                    <option value="">-- নির্বাচন করুন --</option>
                    @foreach($designations as $value)
                        <option value="{{ $value }}" {{ old('designation') === $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">বিষয়</label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="যেমন: গণিত, ইংরেজি" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">সর্বোচ্চ শিক্ষাগত যোগ্যতা</label>
                <input type="text" name="qualification" value="{{ old('qualification') }}" placeholder="যেমন: এম.এ., বি.এড." class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাপ্রতিষ্ঠান</label>
                <input type="text" name="institute" value="{{ old('institute') }}" placeholder="যেমন: ঢাকা বিশ্ববিদ্যালয়" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RIS-এ যোগদানের তারিখ</label>
                <input type="text" data-date-mask name="joining_date" value="{{ old('joining_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ফিচার্ড</label>
                <select name="is_featured" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                    <option value="0">না</option>
                    <option value="1" {{ old('is_featured') ? 'selected' : '' }}>হ্যাঁ</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ববর্তী প্রতিষ্ঠানসমূহ</label>
                <textarea name="previous_institutions" rows="3" placeholder="প্রতিটি প্রতিষ্ঠান নতুন লাইনে লিখুন" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('previous_institutions') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">পরিচিতি</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('bio') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">অভিজ্ঞতা</label>
                <textarea name="experience" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('experience') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">অর্জনসমূহ</label>
                <textarea name="achievements" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('achievements') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('admin.teachers.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</a>
            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">সংরক্ষণ করুন</button>
        </div>
    </form>
</div>
@endsection
