@extends('layouts.admin')

@section('title', 'ফি কাঠামো')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">ফি কাঠামো</h1>
            <p class="text-sm text-gray-500 mt-1">সকল ফি কাঠামোর তালিকা</p>
        </div>
        <a href="{{ route('admin.fees.structures.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন ফি কাঠামো
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিক্ষাবর্ষ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধরন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরিমাণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শেষ তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">বিবরণ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($structures as $index => $structure)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $structure->classRoom->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $structure->academicYear->name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $structure->fee_type }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">৳{{ number_format($structure->amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $structure->due_date?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-500 max-w-[240px] truncate">{{ $structure->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো ফি কাঠামো নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection