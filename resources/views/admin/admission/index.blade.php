@extends('layouts.admin')

@section('title', 'ভর্তি আবেদন তালিকা')

@section('content')
<div class="space-y-6" x-data="admissionApp()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">ভর্তি আবেদন</h1>
            <p class="text-sm text-gray-500 mt-1">সকল ভর্তি আবেদন পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.admission.download', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel ডাউনলোড
            </a>
            <button @click="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                নতুন ভর্তি আবেদন
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.admission.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-1.5">অনুসন্ধান</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম বা ভর্তি নং দিয়ে খুঁজুন..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-1.5">স্ট্যাটাস</label>
                    <select name="status" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল স্ট্যাটাস</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                        ফিল্টার করুন
                    </button>
                    <a href="{{ route('admin.admission.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="gradient-logo">
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ভর্তি নং</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষার্থীর নাম</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ব্যাচ</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">রোল নং</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ফোন</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">তারিখ</th>
                        <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($admissions as $index => $admission)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ ($admissions->currentPage() - 1) * $admissions->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button @click="openViewModal({{ $admission->id }})" class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $admission->admission_no }}</button>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ mb_substr($admission->student_name_bn, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $admission->student_name_bn }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $admission->class_label }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $admission->batch_label }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $admission->roll_no ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $admission->phone ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.admission.status', $admission) }}" x-data="{ status: '{{ $admission->status }}' }">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" x-model="status" @change="$el.form.submit()" class="text-xs font-medium rounded-full px-2.5 py-1 border-0 cursor-pointer focus:ring-2 focus:ring-ris-primary/20 {{ $admission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $admission->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }} {{ $admission->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        <option value="pending" {{ $admission->status === 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                                        <option value="approved" {{ $admission->status === 'approved' ? 'selected' : '' }}>অনুমোদিত</option>
                                        <option value="rejected" {{ $admission->status === 'rejected' ? 'selected' : '' }}>প্রত্যাখ্যাত</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $admission->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openViewModal({{ $admission->id }})" class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer" title="দেখুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @if($admission->status === 'approved' && ! in_array($admission->admission_no, $admittedNos))
                                        <button @click="openAdmitModal({{ $admission->id }})" class="p-1.5 rounded-lg text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors cursor-pointer" title="ভর্তি করুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    @endif
                                    <button @click="openEditModal({{ $admission->id }})" class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer" title="সম্পাদনা">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <a href="{{ route('admin.admission.pdf', $admission) }}" target="_blank" class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors" title="PDF প্রিন্ট">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    @php
                                        $student = \App\Models\Student::where('admission_no', $admission->admission_no)->first();
                                    @endphp
                                    @if($student)
                                        <a href="{{ route('student.id-card', $student) }}" target="_blank" class="p-1.5 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors" title="ID কার্ড">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5l-2-2z"/></svg>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.admission.destroy', $admission) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই ভর্তি আবেদনটি মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer" title="মুছুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <p class="text-gray-500 font-medium">কোনো ভর্তি আবেদন পাওয়া যায়নি</p>
                                <p class="text-sm text-gray-400 mt-1">নতুন ভর্তি আবেদন করুন বা ফিল্টার পরিবর্তন করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('vendor.pagination.custom', ['paginator' => $admissions])
    </div>

</div>
@endsection
