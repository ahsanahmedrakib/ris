@extends('layouts.admin')

@section('title', 'পরীক্ষার ফলাফল প্রবেশ করুন')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.exams.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">পরীক্ষার ফলাফল প্রবেশ করুন</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $exam->name ?? '' }} -এর ফলাফল প্রবেশ করুন</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.exams.results.store', $exam) }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-heading font-semibold text-gray-900">ছাত্রদের নম্বর প্রবেশ করুন</h2>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    ফলাফল সংরক্ষণ করুন
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                            @foreach(($subjects ?? []) as $subject)
                                <th class="text-center px-3 py-3.5 font-medium text-gray-500 min-w-[100px]">
                                    {{ $subject->name }}
                                    <span class="block text-xs font-normal text-gray-400">(০-{{ $subject->total_marks ?? 100 }})</span>
                                </th>
                            @endforeach
                            <th class="text-center px-3 py-3.5 font-medium text-gray-500">মোট</th>
                            <th class="text-center px-3 py-3.5 font-medium text-gray-500">গ্রেড</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($students ?? []) as $index => $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                            {{ substr($student->name_bn, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $student->name_bn }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $student->roll_no }}</td>
                                @foreach(($subjects ?? []) as $subject)
                                    <td class="px-3 py-3 text-center">
                                        <input type="number" name="marks[{{ $student->id }}][{{ $subject->id }}]"
                                               value="{{ old("marks.{$student->id}.{$subject->id}", '') }}"
                                               min="0" max="{{ $subject->total_marks ?? 100 }}"
                                               class="w-20 px-2 py-1.5 border border-gray-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                               placeholder="0">
                                    </td>
                                @endforeach
                                <td class="px-3 py-3 text-center font-medium text-gray-900">-</td>
                                <td class="px-3 py-3 text-center">-</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + count($subjects ?? []) }}" class="px-5 py-12 text-center text-gray-400 text-sm">
                                    এই পরীক্ষায় কোনো ছাত্র নেই
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

</div>
@endsection