@extends('layouts.admin')

@section('title', 'বই ধার তালিকা')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বই ধার তালিকা</h1>
            <p class="text-sm text-gray-500 mt-1">সকল বই ধার ও ফেরতের তথ্য</p>
        </div>
        <a href="{{ route('admin.library.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন ধার
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">বই</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্র/ছাত্রী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধারের তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ফেরার তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($borrowings as $index => $borrowing)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $borrowings->firstItem() + $index }}</td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-900">{{ $borrowing->book->title ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $borrowing->book->author ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $borrowing->student->user->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $borrowing->borrowed_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $borrowing->due_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $borrowing->status === 'returned' ? 'bg-emerald-50 text-emerald-700' : ($borrowing->status === 'overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $borrowing->status === 'returned' ? 'ফেরত' : ($borrowing->status === 'overdue' ? 'মেয়াদোত্তীর্ণ' : 'ধারে') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($borrowing->returned_at === null)
                                    <form method="POST" action="{{ route('admin.library.return', $borrowing) }}" onsubmit="return confirm('বইটি ফেরত নিশ্চিত করছেন?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            ফেরত দিন
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">ফিরে এসেছে</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো বই ধার নেওয়া হয়নি</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($borrowings->hasPages())
        <div class="mt-4">
            {{ $borrowings->links() }}
        </div>
    @endif

</div>
@endsection