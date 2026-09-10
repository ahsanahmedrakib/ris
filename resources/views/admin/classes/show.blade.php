@extends('layouts.admin')

@section('title', 'শ্রেণি বিস্তারিত')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $class->name }} @if ($class->section)
                            <span class="text-ris-primary text-lg">-{{ $class->section }}</span>
                        @endif
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">শ্রেণির বিস্তারিত তথ্য</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.classes.edit', $class) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    সম্পাদনা
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-400">শিক্ষাবর্ষ</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $class->academicYear->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">শ্রেণি শিক্ষক</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $class->classTeacher->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">মোট ছাত্র/ছাত্রী</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $class->students_count ?? $class->students->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">মোট বিষয়</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $class->subjects->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">ছাত্র/ছাত্রী তালিকা</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ভর্তি নং</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($class->students as $index => $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->roll_no }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                            {{ substr($student->user->name ?? 'ছ', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $student->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->admission_no }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">এই শ্রেণিতে কোনো
                                    ছাত্র/ছাত্রী নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">বিষয় তালিকা</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">বিষয়</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">কোড</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিক্ষক</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($class->subjects as $index => $subject)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $subject->name }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $subject->code }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $subject->teacher->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">এই শ্রেণিতে কোনো
                                    বিষয় নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
