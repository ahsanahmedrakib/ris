@extends('layouts.admin')

@section('title', 'নোটিশ সম্পাদনা')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.notices.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">নোটিশ সম্পাদনা</h1>
            <p class="text-sm text-gray-500 mt-1">নোটিশের তথ্য হালনাগাদ করুন</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</h3>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notices.update', $notice) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">নোটিশের তথ্য</h2>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">শিরোনাম <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $notice->title) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">বিবরণ <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="5" required
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('content', $notice->content) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ধরন <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="notice" {{ old('type', $notice->type) === 'notice' ? 'selected' : '' }}>নোটিশ</option>
                            <option value="event" {{ old('type', $notice->type) === 'event' ? 'selected' : '' }}>অনুষ্ঠান</option>
                            <option value="holiday" {{ old('type', $notice->type) === 'holiday' ? 'selected' : '' }}>ছুটি</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">লক্ষ্য</label>
                        <select name="target_role" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="all" {{ old('target_role', $notice->target_role) === 'all' ? 'selected' : '' }}>সকল</option>
                            <option value="admin" {{ old('target_role', $notice->target_role) === 'admin' ? 'selected' : '' }}>অ্যাডমিন</option>
                            <option value="teacher" {{ old('target_role', $notice->target_role) === 'teacher' ? 'selected' : '' }}>শিক্ষক</option>
                            <option value="parent" {{ old('target_role', $notice->target_role) === 'parent' ? 'selected' : '' }}>অভিভাবক</option>
                            <option value="student" {{ old('target_role', $notice->target_role) === 'student' ? 'selected' : '' }}>ছাত্র</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">প্রকাশের তারিখ</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at', $notice->published_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">মেয়াদোত্তীর্ণ</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $notice->expires_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $notice->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary">
                        সক্রিয়
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.notices.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                বাতিল
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                হালনাগাদ করুন
            </button>
        </div>
    </form>

</div>
@endsection