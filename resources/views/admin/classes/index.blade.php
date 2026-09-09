@extends('layouts.admin')

@section('title', 'শ্রেণী তালিকা')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">শ্রেণী তালিকা</h1>
            <p class="text-sm text-gray-500 mt-1">সকল শ্রেণীর তথ্য পরিচালনা করুন</p>
        </div>
        <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন শ্রেণী যোগ করুন
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণীর নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">সেকশন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিক্ষাবর্ষ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণী শিক্ষক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্র সংখ্যা</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($classes ?? []) as $index => $class)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $class->name }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $class->section ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $class->academicYear->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $class->classTeacher->name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $class->students_count ?? 0 }} জন
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="সম্পাদনা">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="মুছুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="text-gray-500 font-medium">কোনো শ্রেণী নেই</p>
                                <p class="text-sm text-gray-400 mt-1">নতুন শ্রেণী যোগ করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator && $classes->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection