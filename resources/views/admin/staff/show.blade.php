@extends('layouts.admin')

@section('title', 'কর্মচারী বিস্তারিত')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.staff.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $staff->user->name ?? '-' }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $staff->employee_id }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.staff.edit', $staff) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                সম্পাদনা
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">নাম</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ইমেইল</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ফোন</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->user->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">কর্মচারী আইডি</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->employee_id }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">পদবি</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->designation ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">বিভাগ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->department ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">যোগদানের তারিখ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->joining_date?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মাসিক বেতন</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">৳{{ number_format($staff->salary, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">শিক্ষাগত যোগ্যতা</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $staff->qualification ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">বেতন তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মাস</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মূল বেতন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ভাতা</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাড়</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">নিট</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($staff->payrolls as $payroll)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-600">{{ $payroll->month }}/{{ $payroll->year }}</td>
                            <td class="px-5 py-3.5 text-gray-600">৳{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">৳{{ number_format($payroll->allowances, 2) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">৳{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">৳{{ number_format($payroll->net_salary, 2) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $payroll->status === 'paid' ? 'পরিশোধিত' : 'মুলতুবি' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">এখনো কোনো বেতন স্লিপ নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">ছুটির আবেদন</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধরন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শুরুর তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শেষ তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">কারণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($staff->leaveRequests as $leave)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-600">{{ $leave->type ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $leave->start_date?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $leave->end_date?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $leave->reason ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($leave->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $leave->status === 'approved' ? 'অনুমোদিত' : ($leave->status === 'rejected' ? 'বাতিল' : 'মুলতুবি') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো ছুটির আবেদন নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection