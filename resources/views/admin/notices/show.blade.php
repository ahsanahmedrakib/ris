@extends('layouts.admin')

@section('title', 'নোটিশ বিস্তারিত')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.notices.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $notice->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">নোটিশ বিস্তারিত তথ্য</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.notices.edit', $notice) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                সম্পাদনা
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                {{ $notice->type === 'event' ? 'bg-blue-50 text-blue-700' : ($notice->type === 'holiday' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                {{ $notice->type === 'event' ? 'অনুষ্ঠান' : ($notice->type === 'holiday' ? 'ছুটি' : 'নোটিশ') }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                {{ $notice->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                {{ $notice->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
            </span>
        </div>
        <div class="p-6 space-y-4">
            <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-line">{{ $notice->content }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 border-t border-gray-100 pt-5">
                <div>
                    <p class="text-xs font-medium text-gray-400">লক্ষ্য</p>
                    <p class="text-sm text-gray-700 mt-1">
                        {{ $notice->target_role === 'all' ? 'সকল' : ($notice->target_role === 'admin' ? 'অ্যাডমিন' : ($notice->target_role === 'teacher' ? 'শিক্ষক' : ($notice->target_role === 'parent' ? 'অভিভাবক' : 'ছাত্র'))) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">প্রকাশক</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $notice->publisher->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">প্রকাশের তারিখ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $notice->published_at ? $notice->published_at->format('d/m/Y h:i A') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মেয়াদোত্তীর্ণ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $notice->expires_at ? $notice->expires_at->format('d/m/Y h:i A') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection