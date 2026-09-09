@extends('layouts.admin')

@section('title', 'রুট ব্যবস্থাপনা')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.transport.show', $bus) }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">রুট ব্যবস্থাপনা</h1>
            <p class="text-sm text-gray-500 mt-1">বাস: {{ $bus->bus_no }} — স্টপ তালিকা</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">স্টপ তালিকা</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">স্টপ</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">সময়</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($routes as $route)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $route->stop_order }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $route->stop_name }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $route->stop_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো স্টপ যোগ করা হয়নি</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">নতুন স্টপ যোগ করুন</h2>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.transport.routes.store', $bus) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">স্টপের নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="stop_name" value="{{ old('stop_name') }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">সময় <span class="text-red-500">*</span></label>
                        <input type="time" name="stop_time" value="{{ old('stop_time') }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ক্রম <span class="text-red-500">*</span></label>
                        <input type="number" name="stop_order" value="{{ old('stop_order', $routes->count() + 1) }}" required min="1"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                        স্টপ যোগ করুন
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection