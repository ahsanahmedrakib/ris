@extends('layouts.admin')

@section('title', 'পরীক্ষা তালিকা')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">পরীক্ষা তালিকা</h1>
                <p class="text-sm text-gray-500 mt-1">সকল পরীক্ষার তথ্য পরিচালনা করুন</p>
            </div>
            <a href="{{ route('admin.exams.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                নতুন পরীক্ষা যোগ করুন
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরীক্ষার নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধরন</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">মোট নম্বর</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">পাস নম্বর</th>
                            <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($exams ?? []) as $index => $exam)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $exam->name }}</td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($exam->type ?? '') === 'midterm' ? 'bg-blue-50 text-blue-700' : (($exam->type ?? '') === 'final' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ($exam->type ?? '') === 'midterm' ? 'অর্ধবার্ষিক' : (($exam->type ?? '') === 'final' ? 'বার্ষিক' : 'বার্ষিকাঞ্চর') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $exam->class->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $exam->total_marks ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $exam->pass_marks ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.exams.results', $exam) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="ফলাফল">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.exams.edit', $exam) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত?')">
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
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো পরীক্ষা নেই</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন পরীক্ষা যোগ করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($exams) && $exams instanceof \Illuminate\Pagination\LengthAwarePaginator && $exams->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $exams->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
