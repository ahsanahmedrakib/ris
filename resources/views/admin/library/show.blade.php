@extends('layouts.admin')

@section('title', 'বই বিস্তারিত')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.library.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $book->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $book->author }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.library.edit', $book) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                সম্পাদনা
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">লেখক</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $book->author }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ISBN</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $book->isbn }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">বিভাগ</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-1 bg-blue-50 text-blue-700">{{ $book->category ?? '-' }}</span>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">অবস্থান</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $book->location ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মোট কপি</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">{{ $book->total_copies }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">উপলব্ধ কপি</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $book->available_copies }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মোট ধার</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $book->book_borrowings_count ?? 0 }}টি</p>
                </div>
            </div>
            @if($book->description)
                <div class="mt-5 border-t border-gray-100 pt-5">
                    <p class="text-xs font-medium text-gray-400 mb-2">বিবরণ</p>
                    <p class="text-sm text-gray-700">{{ $book->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">ধার তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্র/ছাত্রী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধারের তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ফেরার তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ফিরিয়ে দিয়েছেন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($book->bookBorrowings as $index => $borrowing)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $borrowing->student->user->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $borrowing->borrowed_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $borrowing->due_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $borrowing->returned_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $borrowing->status === 'returned' ? 'bg-emerald-50 text-emerald-700' : ($borrowing->status === 'overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $borrowing->status === 'returned' ? 'ফেরত' : ($borrowing->status === 'overdue' ? 'মেয়াদোত্তীর্ণ' : 'ধারে') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">এই বইটি এখনো কেউ ধার নেয়নি</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection