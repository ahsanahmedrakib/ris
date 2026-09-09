@extends('layouts.admin')

@section('title', 'উপস্থিতি সম্পাদনা')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.attendance.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">উপস্থিতি সম্পাদনা</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $attendance->student->user->name ?? '' }} — {{ $attendance->date?->format('d/m/Y') }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</h3>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">উপস্থিতির তথ্য</h2>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">অবস্থা <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status', $attendance->status) === $status->value ? 'selected' : '' }}>
                                {{ $status->value === 'present' ? 'উপস্থিত' : ($status->value === 'late' ? 'দেরিতে' : ($status->value === 'excused' ? 'ছাড়পত্র' : 'অনুপস্থিত')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">মন্তব্য</label>
                    <textarea name="remarks" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('remarks', $attendance->remarks) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.attendance.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                বাতিল
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                হালনাগাদ করুন
            </button>
        </div>
    </form>

</div>
@endsection