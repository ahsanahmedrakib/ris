@extends('layouts.admin')

@section('title', 'বাস সম্পাদনা')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.transport.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বাস সম্পাদনা</h1>
            <p class="text-sm text-gray-500 mt-1">বাসের তথ্য হালনাগাদ করুন</p>
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

    <form method="POST" action="{{ route('admin.transport.update', $bus) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">বাসের তথ্য</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">বাস নম্বর <span class="text-red-500">*</span></label>
                        <input type="text" name="bus_no" value="{{ old('bus_no', $bus->bus_no) }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                               placeholder="যেমন: ঢাকা-১২-৩৪৫৬">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">চালকের নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="driver_name" value="{{ old('driver_name', $bus->driver_name) }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">চালকের ফোন <span class="text-red-500">*</span></label>
                        <input type="text" name="driver_phone" value="{{ old('driver_phone', $bus->driver_phone) }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">আসন সংখ্যা <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', $bus->capacity) }}" required min="1"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">রুট</label>
                        <input type="text" name="route_name" value="{{ old('route_name', $bus->route_name) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.transport.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                বাতিল
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                হালনাগাদ করুন
            </button>
        </div>
    </form>

</div>
@endsection