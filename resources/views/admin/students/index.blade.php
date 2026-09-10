@extends('layouts.admin')

@section('title', 'ছাত্র/ছাত্রী তালিকা')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">ছাত্র/ছাত্রী তালিকা</h1>
                <p class="text-sm text-gray-500 mt-1">সকল ছাত্র/ছাত্রীদের তথ্য পরিচালনা করুন</p>
            </div>
            <a href="{{ route('admin.students.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                নতুন ছাত্র যোগ করুন
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.students.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="নাম বা ভর্তি নং দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল শ্রেণি</option>
                            @foreach ($classes ?? [] as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থা</label>
                        <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল অবস্থা</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>সক্রিয়</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয়
                            </option>
                            <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>
                                স্থানান্তরিত</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.students.index') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            রিসেট
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ভর্তি নং</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">অভিভাবক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">মোবাইল</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                            <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($students ?? []) as $index => $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $student->admission_no }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                            {{ substr($student->name_bn, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $student->name_bn }}</div>
                                            <div class="text-xs text-gray-400">{{ $student->name_en }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->class->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->roll_no }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->guardian_name }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->guardian_phone }}</td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $student->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($student->status === 'inactive' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ $student->status === 'active' ? 'সক্রিয়' : ($student->status === 'inactive' ? 'নিষ্ক্রিয়' : 'স্থানান্তরিত') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.students.show', $student) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="দেখুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই ছাত্রটিকে মুছে ফেলতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="মুছুন">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো ছাত্র পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন ছাত্র যোগ করুন বা ফিল্টার পরিবর্তন করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if (isset($students) && $students->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
