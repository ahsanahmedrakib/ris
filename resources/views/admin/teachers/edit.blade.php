@extends('layouts.admin')

@section('title', 'শিক্ষক সম্পাদনা')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('admin.teachers.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        শিক্ষক তালিকায় ফিরুন
    </a>

    <div class="card p-6">
        <h1 class="text-xl font-heading font-bold text-ris-dark mb-1">শিক্ষকের তথ্য সম্পাদনা</h1>
        <p class="text-sm text-gray-500 mb-6">শিক্ষকের তথ্য আপডেট করুন</p>

        <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">নাম <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">মোবাইল</label>
                <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-ris-primary border-gray-300 rounded focus:ring-ris-primary"
                       {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-gray-700">সক্রিয়</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.teachers.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    বাতিল
                </a>
                <button type="submit" class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>

</div>
@endsection