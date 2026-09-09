@extends('layouts.admin')

@section('title', 'উপস্থিতি রিপোর্ট')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">উপস্থিতি রিপোর্ট</h1>
        <p class="text-sm text-gray-500 mt-1">শ্রেণী ও তারিখ অনুযায়ী উপস্থিতির রিপোর্ট দেখুন</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণী</label>
                    <select name="class_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল শ্রেণী</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শুরুর তারিখ</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শেষ তারিখ</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থা</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল অবস্থা</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-4">
                <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                    ফিল্টার করুন
                </button>
                <a href="{{ route('admin.reports.attendance') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    রিসেট
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-2xl font-heading font-bold text-gray-900">{{ $summary['total'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">মোট</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-2xl font-heading font-bold text-emerald-600">{{ $summary['present'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">উপস্থিত</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-2xl font-heading font-bold text-red-600">{{ $summary['absent'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">অনুপস্থিত</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-heading font-bold text-amber-600">{{ $summary['late'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">বিলম্বিত</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-heading font-bold text-purple-600">{{ $summary['excused'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">অবকাশ</p>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">উপস্থিতির তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                        <th class="text-center px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $index => $attendance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') : '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($attendance->student->name_bn ?? '?', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $attendance->student->name_bn ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $attendance->student->class->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $attendance->student->roll_no ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $attendance->status === 'present' ? 'bg-emerald-50 text-emerald-700' : ($attendance->status === 'absent' ? 'bg-red-50 text-red-700' : ($attendance->status === 'late' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700')) }}">
                                    {{ $attendance->status === 'present' ? 'উপস্থিত' : ($attendance->status === 'absent' ? 'অনুপস্থিত' : ($attendance->status === 'late' ? 'বিলম্বিত' : 'অবকাশ')) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <p class="text-gray-500 font-medium">কোনো উপস্থিতির রেকর্ড পাওয়া যায়নি</p>
                                <p class="text-sm text-gray-400 mt-1">ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
