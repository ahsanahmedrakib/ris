@extends('layouts.admin')

@section('title', 'মেধাবৃত্তি রেজিস্ট্রেশন তালিকা')

@section('content')
<div class="space-y-6" x-data="scholarshipApp()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">মেধাবৃত্তি রেজিস্ট্রেশন</h1>
            <p class="text-sm text-gray-500 mt-1">সকল মেধাবৃত্তি রেজিস্ট্রেশন পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.scholarship.download', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Word ডাউনলোড
            </a>
            <button @click="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                নতুন রেজিস্ট্রেশন
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.scholarship.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম বা রেজি নং দিয়ে খুঁজুন..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                    <select name="class_no" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল শ্রেণি</option>
                        @foreach($classes as $value => $label)
                            <option value="{{ $value }}" {{ request('class_no') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল স্ট্যাটাস</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                        ফিল্টার করুন
                    </button>
                    <a href="{{ route('admin.scholarship.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
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
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">রেজি নং</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষার্থীর নাম</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">রোল নং</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মোবাইল</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পেমেন্ট</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">তারিখ</th>
                        <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($registrations as $index => $registration)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ ($registrations->currentPage() - 1) * $registrations->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button @click="openViewModal({{ $registration->id }})" class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $registration->registration_no }}</button>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ mb_substr($registration->student_name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $registration->student_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $classes[$registration->class_no] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $registration->roll_no ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $registration->mobile_no }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($registration->payment_method === 'cash')
                                    <span class="text-emerald-600 font-medium">ক্যাশ</span>
                                @else
                                    <span class="text-gray-600">{{ $registration->bkash_no }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.scholarship.status', $registration) }}" x-data="{ status: '{{ $registration->status }}' }">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" x-model="status" @change="$el.form.submit()" class="text-xs font-medium rounded-full px-2.5 py-1 border-0 cursor-pointer focus:ring-2 focus:ring-ris-primary/20 {{ $registration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $registration->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }} {{ $registration->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        <option value="pending" {{ $registration->status === 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                                        <option value="approved" {{ $registration->status === 'approved' ? 'selected' : '' }}>অ্যাকসেপ্টেড</option>
                                        <option value="rejected" {{ $registration->status === 'rejected' ? 'selected' : '' }}>রিজেক্টেড</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $registration->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openViewModal({{ $registration->id }})" class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors" title="দেখুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button @click="openEditModal({{ $registration->id }})" class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors" title="সম্পাদনা">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <a href="{{ route('admin.scholarship.pdf', $registration) }}" target="_blank" class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors" title="PDF প্রিন্ট">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.scholarship.destroy', $registration) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই রেজিস্ট্রেশনটি মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors" title="মুছুন">
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
                                <p class="text-gray-500 font-medium">কোনো রেজিস্ট্রেশন পাওয়া যায়নি</p>
                                <p class="text-sm text-gray-400 mt-1">নতুন রেজিস্ট্রেশন করুন বা ফিল্টার পরিবর্তন করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('vendor.pagination.custom', ['paginator' => $registrations])
    </div>

    {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">নতুন রেজিস্ট্রেশন</h3>
                <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.scholarship.store') }}" method="POST" class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষার্থীর নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="student_name" x-model="createForm.student_name" @blur="validateCreateField('student_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.student_name || (createAttempted && !createForm.student_name)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.student_name || (createAttempted && !createForm.student_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.student_name || 'শিক্ষার্থীর নাম আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">পিতার নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name" x-model="createForm.father_name" @blur="validateCreateField('father_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.father_name || (createAttempted && !createForm.father_name)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.father_name || (createAttempted && !createForm.father_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.father_name || 'পিতার নাম আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মাতার নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="mother_name" x-model="createForm.mother_name" @blur="validateCreateField('mother_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.mother_name || (createAttempted && !createForm.mother_name)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.mother_name || (createAttempted && !createForm.mother_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.mother_name || 'মাতার নাম আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">স্কুলের নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="school_name" x-model="createForm.school_name" @blur="validateCreateField('school_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.school_name || (createAttempted && !createForm.school_name)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.school_name || (createAttempted && !createForm.school_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.school_name || 'স্কুলের নাম আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span class="text-red-500">*</span></label>
                        <select name="class_no" x-model="createForm.class_no" @change="validateCreateField('class_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="(createErrors.class_no || (createAttempted && !createForm.class_no)) ? 'border-red-400' : 'border-gray-200'">
                            <option value="">-- শ্রেণি নির্বাচন --</option>
                            @foreach($classes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <template x-if="createErrors.class_no || (createAttempted && !createForm.class_no)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.class_no || 'শ্রেণি আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">রোল নং <span class="text-red-500">*</span></label>
                        <input type="text" name="roll_no" x-model="createForm.roll_no" @blur="validateCreateField('roll_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.roll_no || (createAttempted && !createForm.roll_no)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.roll_no || (createAttempted && !createForm.roll_no)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.roll_no || 'রোল নং আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল নং <span class="text-red-500">*</span></label>
                        <input type="tel" name="mobile_no" x-model="createForm.mobile_no" @blur="validateCreateField('mobile_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.mobile_no || (createAttempted && !createForm.mobile_no)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.mobile_no || (createAttempted && !createForm.mobile_no)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.mobile_no || 'মোবাইল নং আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">পেমেন্ট মাধ্যম <span class="text-red-500">*</span></label>
                        <select name="payment_method" x-model="createForm.payment_method" @change="validateCreateField('payment_method'); validateCreateField('bkash_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="createErrors.payment_method ? 'border-red-400' : 'border-gray-200'">
                            <option value="bkash">বিকাশ</option>
                            <option value="cash">ক্যাশ</option>
                        </select>
                        <template x-if="createErrors.payment_method"><p class="mt-1 text-xs text-red-600" x-text="createErrors.payment_method"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">বিকাশ নম্বর <template x-if="createForm.payment_method === 'bkash'"><span class="text-red-500">*</span></template></label>
                        <input type="tel" name="bkash_no" x-model="createForm.bkash_no" @blur="validateCreateField('bkash_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.bkash_no || (createAttempted && createForm.payment_method === 'bkash' && !createForm.bkash_no)) ? 'border-red-400' : 'border-gray-200'">
                        <template x-if="createErrors.bkash_no || (createAttempted && createForm.payment_method === 'bkash' && !createForm.bkash_no)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.bkash_no || 'বিকাশ নম্বর আবশ্যক'"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস <span class="text-red-500">*</span></label>
                        <select name="status" x-model="createForm.status" @change="validateCreateField('status')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="createErrors.status ? 'border-red-400' : 'border-gray-200'">
                                    <option value="pending">পেন্ডিং</option>
                                    <option value="approved">অ্যাকসেপ্টেড</option>
                            <option value="rejected">রিজেক্টেড</option>
                        </select>
                        <template x-if="createErrors.status"><p class="mt-1 text-xs text-red-600" x-text="createErrors.status"></p></template>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                    <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">তৈরি করুন</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showViewModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">রেজিস্ট্রেশন তথ্য</h3>
                <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="viewData">
                    <div>
                        <div class="text-center mb-5">
                            <span class="inline-flex items-center px-4 py-2 bg-ris-primary/10 border border-ris-primary/20 rounded-lg font-heading font-bold text-ris-primary tracking-wider" x-text="viewData.registration_no"></span>
                        </div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 mb-1">শিক্ষার্থীর নাম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.student_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পিতার নাম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.father_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মাতার নাম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.mother_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">স্কুলের নাম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.school_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.class_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">রোল নং</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.roll_no || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মোবাইল নং</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.mobile_no"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পেমেন্ট মাধ্যম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.payment_method === 'cash' ? 'ক্যাশ' : 'বিকাশ'"></dd>
                            </div>
                            <template x-if="viewData.payment_method !== 'cash'">
                                <div>
                                    <dt class="text-gray-500 mb-1">বিকাশ নম্বর</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.bkash_no || '-'"></dd>
                                </div>
                            </template>
                            <div>
                                <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': viewData.status === 'pending',
                                            'bg-emerald-100 text-emerald-800': viewData.status === 'approved',
                                            'bg-red-100 text-red-800': viewData.status === 'rejected'
                                        }" x-text="viewData.status_label"></span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">রেজিস্ট্রেশন তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.created_at"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">তৈরি করেছেন</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.creator_name"></dd>
                            </div>
                        </dl>
                        <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end gap-3">
                            <button @click="showViewModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বন্ধ করুন</button>
                            <button @click="showViewModal = false; openEditModal(viewData.id)" class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">সম্পাদনা</button>
                        </div>
                    </div>
                </template>
                <template x-if="viewLoading">
                    <div class="py-12 text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ═══════════════ EDIT MODAL ═══════════════ --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showEditModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">রেজিস্ট্রেশন সম্পাদনা</h3>
                <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="editData">
                    <form :action="'{{ url('admin/scholarship') }}/' + editData.id" method="POST" class="space-y-5" @submit.prevent="validateEditForm($el)">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষার্থীর নাম <span class="text-red-500">*</span></label>
                                <input type="text" name="student_name" x-model="editData.student_name" @blur="validateEditField('student_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.student_name || (editAttempted && !editData.student_name)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.student_name || (editAttempted && !editData.student_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.student_name || 'শিক্ষার্থীর নাম আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">পিতার নাম <span class="text-red-500">*</span></label>
                                <input type="text" name="father_name" x-model="editData.father_name" @blur="validateEditField('father_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.father_name || (editAttempted && !editData.father_name)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.father_name || (editAttempted && !editData.father_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.father_name || 'পিতার নাম আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">মাতার নাম <span class="text-red-500">*</span></label>
                                <input type="text" name="mother_name" x-model="editData.mother_name" @blur="validateEditField('mother_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.mother_name || (editAttempted && !editData.mother_name)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.mother_name || (editAttempted && !editData.mother_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.mother_name || 'মাতার নাম আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">স্কুলের নাম <span class="text-red-500">*</span></label>
                                <input type="text" name="school_name" x-model="editData.school_name" @blur="validateEditField('school_name')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.school_name || (editAttempted && !editData.school_name)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.school_name || (editAttempted && !editData.school_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.school_name || 'স্কুলের নাম আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span class="text-red-500">*</span></label>
                                <select name="class_no" x-model="editData.class_no" @change="validateEditField('class_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="(editErrors.class_no || (editAttempted && !editData.class_no)) ? 'border-red-400' : 'border-gray-200'">
                                    <option value="">-- শ্রেণি নির্বাচন --</option>
                                    @foreach($classes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <template x-if="editErrors.class_no || (editAttempted && !editData.class_no)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.class_no || 'শ্রেণি আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">রোল নং <span class="text-red-500">*</span></label>
                                <input type="text" name="roll_no" x-model="editData.roll_no" @blur="validateEditField('roll_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.roll_no || (editAttempted && !editData.roll_no)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.roll_no || (editAttempted && !editData.roll_no)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.roll_no || 'রোল নং আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল নং <span class="text-red-500">*</span></label>
                                <input type="tel" name="mobile_no" x-model="editData.mobile_no" @blur="validateEditField('mobile_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.mobile_no || (editAttempted && !editData.mobile_no)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.mobile_no || (editAttempted && !editData.mobile_no)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.mobile_no || 'মোবাইল নং আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">পেমেন্ট মাধ্যম <span class="text-red-500">*</span></label>
                                <select name="payment_method" x-model="editData.payment_method" @change="validateEditField('payment_method'); validateEditField('bkash_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="editErrors.payment_method ? 'border-red-400' : 'border-gray-200'">
                                    <option value="bkash">বিকাশ</option>
                                    <option value="cash">ক্যাশ</option>
                                </select>
                                <template x-if="editErrors.payment_method"><p class="mt-1 text-xs text-red-600" x-text="editErrors.payment_method"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">বিকাশ নম্বর <template x-if="editData.payment_method === 'bkash'"><span class="text-red-500">*</span></template></label>
                                <input type="tel" name="bkash_no" x-model="editData.bkash_no" @blur="validateEditField('bkash_no')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.bkash_no || (editAttempted && editData.payment_method === 'bkash' && !editData.bkash_no)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="editErrors.bkash_no || (editAttempted && editData.payment_method === 'bkash' && !editData.bkash_no)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.bkash_no || 'বিকাশ নম্বর আবশ্যক'"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস <span class="text-red-500">*</span></label>
                                <select name="status" x-model="editData.status" @change="validateEditField('status')" class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="editErrors.status ? 'border-red-400' : 'border-gray-200'">
                                    <option value="pending">পেন্ডিং</option>
                                    <option value="approved">অ্যাকসেপ্টেড</option>
                                    <option value="rejected">রিজেক্টেড</option>
                                </select>
                                <template x-if="editErrors.status"><p class="mt-1 text-xs text-red-600" x-text="editErrors.status"></p></template>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">আপডেট করুন</button>
                        </div>
                    </form>
                </template>
                <template x-if="editLoading">
                    <div class="py-12 text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>

@section('scripts')
<script>
function scholarshipApp() {
    return {
        showCreateModal: false,
        showViewModal: false,
        showEditModal: false,
        viewData: null,
        editData: null,
        viewLoading: false,
        editLoading: false,

        createForm: { student_name: '', father_name: '', mother_name: '', school_name: '', class_no: '', roll_no: '', mobile_no: '', bkash_no: '', payment_method: 'bkash', status: 'pending' },
        createErrors: {},
        createAttempted: false,

        editErrors: {},
        editAttempted: false,

        openCreateModal() {
            this.createForm = { student_name: '', father_name: '', mother_name: '', school_name: '', class_no: '', roll_no: '', mobile_no: '', bkash_no: '', payment_method: 'bkash', status: 'pending' };
            this.createErrors = {};
            this.createAttempted = false;
            this.showCreateModal = true;
        },

        async openViewModal(id) {
            this.showViewModal = true;
            this.viewLoading = true;
            this.viewData = null;
            try {
                const res = await fetch(`{{ url('admin/scholarship') }}/${id}/show`);
                this.viewData = await res.json();
            } catch (e) {
                this.showViewModal = false;
                alert('তথ্য লোড করতে সমস্যা হয়েছে।');
            } finally {
                this.viewLoading = false;
            }
        },

        async openEditModal(id) {
            this.showEditModal = true;
            this.editLoading = true;
            this.editData = null;
            this.editErrors = {};
            this.editAttempted = false;
            try {
                const res = await fetch(`{{ url('admin/scholarship') }}/${id}/edit`);
                this.editData = await res.json();
            } catch (e) {
                this.showEditModal = false;
                alert('তথ্য লোড করতে সমস্যা হয়েছে।');
            } finally {
                this.editLoading = false;
            }
        },

        validateField(field, data, errors) {
            delete errors[field];
            const val = data[field];

            if (['student_name','father_name','mother_name','school_name','class_no','roll_no','mobile_no','payment_method','status'].includes(field)) {
                if (!val || val.trim() === '') {
                    const msgs = { student_name:'শিক্ষার্থীর নাম আবশ্যক।', father_name:'পিতার নাম আবশ্যক।', mother_name:'মাতার নাম আবশ্যক।', school_name:'স্কুলের নাম আবশ্যক।', class_no:'শ্রেণি নির্বাচন আবশ্যক।', roll_no:'রোল নং আবশ্যক।', mobile_no:'মোবাইল নম্বর আবশ্যক।', payment_method:'পেমেন্ট মাধ্যম নির্বাচন করুন।', status:'স্ট্যাটাস নির্বাচন করুন।' };
                    errors[field] = msgs[field];
                    return false;
                }
            }

            if (field === 'mobile_no' && val && !/^01[0-9]{9}$/.test(val)) {
                errors[field] = 'সঠিক মোবাইল নম্বর দিন (০১ দিয়ে শুরু ১১ সংখ্যা)।';
                return false;
            }

            if (field === 'bkash_no' && data.payment_method === 'bkash') {
                if (!val || val.trim() === '') {
                    errors[field] = 'বিকাশ নম্বর আবশ্যক।';
                    return false;
                }
                if (!/^01[0-9]{9}$/.test(val)) {
                    errors[field] = 'সঠিক বিকাশ নম্বর দিন।';
                    return false;
                }
            }

            return true;
        },

        validateCreateField(field) {
            return this.validateField(field, this.createForm, this.createErrors);
        },

        validateEditField(field) {
            return this.validateField(field, this.editData, this.editErrors);
        },

        validateCreateForm(el) {
            this.createAttempted = true;
            this.createErrors = {};
            let valid = true;
            ['student_name','father_name','mother_name','school_name','class_no','roll_no','mobile_no','payment_method','bkash_no','status'].forEach(f => {
                if (!this.validateCreateField(f)) valid = false;
            });
            if (valid) el.submit();
        },

        validateEditForm(el) {
            this.editAttempted = true;
            this.editErrors = {};
            let valid = true;
            ['student_name','father_name','mother_name','school_name','class_no','roll_no','mobile_no','payment_method','bkash_no','status'].forEach(f => {
                if (!this.validateEditField(f)) valid = false;
            });
            if (valid) el.submit();
        }
    }
}
</script>
@endsection
@endsection