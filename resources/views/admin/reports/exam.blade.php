@extends('layouts.admin')

@section('title', 'পরীক্ষা রিপোর্ট')

@section('content')
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">পরীক্ষা রিপোর্ট</h1>
            <p class="text-sm text-gray-500 mt-1">পরীক্ষার তালিকা, ফলাফল ও শ্রেণি অনুযায়ী পরীক্ষার সারসংক্ষেপ দেখুন</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল শ্রেণি</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.reports.exams') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            রিসেট
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Exam List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">পরীক্ষার তালিকা</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরীক্ষার নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধরন</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                            <th class="text-center px-5 py-3.5 font-medium text-gray-500">ফলাফল সংখ্যা</th>
                            <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($exams as $index => $exam)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">
                                    {{ ($exams->currentPage() - 1) * $exams->perPage() + $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-gray-900">{{ $exam->name }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $exam->class->name ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $exam->type ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $exam->date ? \Carbon\Carbon::parse($exam->date)->format('d/m/Y') : '-' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        {{ $exam->results_count ?? ($exam->results->count() ?? 0) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.exams.results', $exam) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="ফলাফল দেখুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো পরীক্ষা পাওয়া যায়নি</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($exams->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $exams->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
