@extends('layouts.admin')

@section('title', 'বাস বিস্তারিত')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transport.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">বাস: {{ $bus->bus_no }}</h1>
                <p class="text-sm text-gray-500 mt-1">বাসের বিস্তারিত তথ্য</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.transport.routes', $bus) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                রুট ব্যবস্থাপনা
            </a>
            <a href="{{ route('admin.transport.edit', $bus) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                সম্পাদনা
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">বাস নম্বর</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">{{ $bus->bus_no }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">চালক</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $bus->driver_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">চালকের ফোন</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $bus->driver_phone }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">রুট</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $bus->route_name ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">আসন</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">{{ $bus->capacity }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">নির্ধারিত ছাত্র</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $bus->student_transports_count ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মোট স্টপ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $bus->bus_routes_count ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">রুট তালিকা</h2>
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
                    @forelse($bus->busRoutes as $route)
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

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">নির্ধারিত ছাত্র/ছাত্রী তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পিকআপ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ড্রপঅফ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bus->studentTransports as $index => $transport)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $transport->student->user->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $transport->student->roll_no ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $transport->pickup_stop ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $transport->dropoff_stop ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">এই বাসে কোনো ছাত্র/ছাত্রী নির্ধারিত নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection