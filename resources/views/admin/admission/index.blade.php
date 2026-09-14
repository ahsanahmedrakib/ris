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
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
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
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
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
                                    <button @click="openViewModal({{ $admission->id }})" class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors" title="দেখুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button @click="openEditModal({{ $admission->id }})" class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors" title="সম্পাদনা">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <a href="{{ route('admin.admission.pdf', $admission) }}" target="_blank" class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors" title="PDF প্রিন্ট">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.admission.destroy', $admission) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই ভর্তি আবেদনটি মুছে ফেলতে চান?')">
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

    {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">নতুন ভর্তি আবেদন</h3>
                <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.admission.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" @submit.prevent="validateCreateForm($el)">
                @csrf

                {{-- অফিস পূরণ করবে --}}
                <div>
                    <div class="section-title">অফিস পূরণ করবে</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">শিক্ষাবর্ষ <span class="text-red-500">*</span></label>
                            <input type="text" name="academic_year" x-model="createForm.academic_year" maxlength="2" @blur="validateCreateField('academic_year')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.academic_year || (createAttempted && !createForm.academic_year)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.academic_year || (createAttempted && !createForm.academic_year)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.academic_year || 'শিক্ষাবর্ষ আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">রোল নং/ID No <span class="text-red-500">*</span></label>
                            <input type="text" name="roll_no" x-model="createForm.roll_no" @blur="validateCreateField('roll_no')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.roll_no || (createAttempted && !createForm.roll_no)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.roll_no || (createAttempted && !createForm.roll_no)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.roll_no || 'রোল নং আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">সেকশন <span class="text-red-500">*</span></label>
                            <input type="text" name="section" x-model="createForm.section" @blur="validateCreateField('section')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.section || (createAttempted && !createForm.section)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.section || (createAttempted && !createForm.section)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.section || 'সেকশন আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ভর্তির তারিখ <span class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="admission_date" x-model="createForm.admission_date" @blur="validateCreateField('admission_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.admission_date || (createAttempted && !createForm.admission_date)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.admission_date || (createAttempted && !createForm.admission_date)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.admission_date || 'ভর্তির তারিখ আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ফরম সংগ্রহের তারিখ <span class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="form_collect_date" x-model="createForm.form_collect_date" @blur="validateCreateField('form_collect_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.form_collect_date || (createAttempted && !createForm.form_collect_date)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.form_collect_date || (createAttempted && !createForm.form_collect_date)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.form_collect_date || 'ফরম সংগ্রহের তারিখ আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ফরম জমা দেয়ার তারিখ <span class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="form_submit_date" x-model="createForm.form_submit_date" @blur="validateCreateField('form_submit_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.form_submit_date || (createAttempted && !createForm.form_submit_date)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.form_submit_date || (createAttempted && !createForm.form_submit_date)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.form_submit_date || 'ফরম জমা দেয়ার তারিখ আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ব্যাচ <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-4">
                                @foreach($batches as $batch)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="batch" value="{{ $batch }}" x-model="createForm.batch" class="accent-ris-primary">
                                        <span class="text-lg text-gray-700">{{ $batch }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <template x-if="createErrors.batch || (createAttempted && !createForm.batch)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.batch || 'ব্যাচ নির্বাচন করুন।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">শ্রেণি <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-x-4 gap-y-2">
                                @foreach($classes as $class)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="class_level" value="{{ $class }}" x-model="createForm.class_level" class="accent-ris-primary">
                                        <span class="text-lg text-gray-700">{{ $class }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <template x-if="createErrors.class_level || (createAttempted && !createForm.class_level)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.class_level || 'শ্রেণি নির্বাচন করুন।'"></p></template>
                        </div>
                    </div>
                </div>

                {{-- শিক্ষার্থী ও পিতা-মাতার বিবরণ --}}
                <div>
                    <div class="section-title">ছাত্র/ছাত্রীর ও পিতা-মাতার বিবরণ</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর নাম (বাংলায়) <span class="text-red-500">*</span></label>
                            <input type="text" name="student_name_bn" x-model="createForm.student_name_bn" @blur="validateCreateField('student_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.student_name_bn || (createAttempted && !createForm.student_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.student_name_bn || (createAttempted && !createForm.student_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.student_name_bn || 'ছাত্র/ছাত্রীর নাম আবশ্যক'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ইংরেজিতে বড় অক্ষরে <span class="text-red-500">*</span></label>
                            <input type="text" name="student_name_en" x-model="createForm.student_name_en" @blur="validateCreateField('student_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(createErrors.student_name_en || (createAttempted && !createForm.student_name_en)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.student_name_en || (createAttempted && !createForm.student_name_en)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.student_name_en || 'ইংরেজিতে নাম আবশ্যক'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">জন্ম তারিখ <span class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="dob" x-model="createForm.dob" @blur="validateCreateField('dob')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.dob || (createAttempted && !createForm.dob)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.dob || (createAttempted && !createForm.dob)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.dob || 'জন্ম তারিখ আবশ্যক'"></p></template>
                        </div>
<div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">বয়স <span class="text-red-500">*</span></label>
                                    <input type="text" name="age" x-model="createForm.age" @blur="validateCreateField('age')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.age || (createAttempted && !createForm.age)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="createErrors.age || (createAttempted && !createForm.age)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.age || 'বয়স আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ব্লাড গ্রুপ <span class="text-red-500">*</span></label>
                                    <input type="text" name="blood_group" x-model="createForm.blood_group" @blur="validateCreateField('blood_group')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.blood_group || (createAttempted && !createForm.blood_group)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="createErrors.blood_group || (createAttempted && !createForm.blood_group)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.blood_group || 'ব্লাড গ্রুপ আবশ্যক।'"></p></template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-1.5">জাতীয়তা <span class="text-red-500">*</span></label>
                                <input type="text" name="nationality" x-model="createForm.nationality" @blur="validateCreateField('nationality')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.nationality || (createAttempted && !createForm.nationality)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="createErrors.nationality || (createAttempted && !createForm.nationality)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.nationality || 'জাতীয়তা আবশ্যক।'"></p></template>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-1.5">ধর্ম <span class="text-red-500">*</span></label>
                                <input type="text" name="religion" x-model="createForm.religion" @blur="validateCreateField('religion')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.religion || (createAttempted && !createForm.religion)) ? 'border-red-400' : 'border-gray-200'">
                                <template x-if="createErrors.religion || (createAttempted && !createForm.religion)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.religion || 'ধর্ম আবশ্যক।'"></p></template>
                            </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার নাম (বাংলায়) <span class="text-red-500">*</span></label>
                            <input type="text" name="father_name_bn" x-model="createForm.father_name_bn" @blur="validateCreateField('father_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.father_name_bn || (createAttempted && !createForm.father_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.father_name_bn || (createAttempted && !createForm.father_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.father_name_bn || 'পিতার নাম আবশ্যক'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার নাম (ইংরেজি) <span class="text-red-500">*</span></label>
                            <input type="text" name="father_name_en" x-model="createForm.father_name_en" @blur="validateCreateField('father_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(createErrors.father_name_en || (createAttempted && !createForm.father_name_en)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.father_name_en || (createAttempted && !createForm.father_name_en)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.father_name_en || 'পিতার নাম (ইংরেজি) আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার পেশা ও পদবী <span class="text-red-500">*</span></label>
                            <input type="text" name="father_occupation" x-model="createForm.father_occupation" @blur="validateCreateField('father_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.father_occupation || (createAttempted && !createForm.father_occupation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.father_occupation || (createAttempted && !createForm.father_occupation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.father_occupation || 'পিতার পেশা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার নাম (বাংলায়) <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name_bn" x-model="createForm.mother_name_bn" @blur="validateCreateField('mother_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.mother_name_bn || (createAttempted && !createForm.mother_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.mother_name_bn || (createAttempted && !createForm.mother_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.mother_name_bn || 'মাতার নাম আবশ্যক'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার নাম (ইংরেজি) <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name_en" x-model="createForm.mother_name_en" @blur="validateCreateField('mother_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(createErrors.mother_name_en || (createAttempted && !createForm.mother_name_en)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.mother_name_en || (createAttempted && !createForm.mother_name_en)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.mother_name_en || 'মাতার নাম (ইংরেজি) আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার পেশা ও পদবী <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_occupation" x-model="createForm.mother_occupation" @blur="validateCreateField('mother_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.mother_occupation || (createAttempted && !createForm.mother_occupation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.mother_occupation || (createAttempted && !createForm.mother_occupation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.mother_occupation || 'মাতার পেশা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">বর্তমান ঠিকানা <span class="text-red-500">*</span></label>
                            <textarea name="present_address" x-model="createForm.present_address" rows="2" @blur="validateCreateField('present_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none" :class="(createErrors.present_address || (createAttempted && !createForm.present_address)) ? 'border-red-400' : 'border-gray-200'"></textarea>
                            <template x-if="createErrors.present_address || (createAttempted && !createForm.present_address)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.present_address || 'বর্তমান ঠিকানা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থায়ী ঠিকানা <span class="text-red-500">*</span></label>
                            <textarea name="permanent_address" x-model="createForm.permanent_address" rows="2" @blur="validateCreateField('permanent_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none" :class="(createErrors.permanent_address || (createAttempted && !createForm.permanent_address)) ? 'border-red-400' : 'border-gray-200'"></textarea>
                            <template x-if="createErrors.permanent_address || (createAttempted && !createForm.permanent_address)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.permanent_address || 'স্থায়ী ঠিকানা আবশ্যক।'"></p></template>
                        </div>
                    </div>
                </div>

                {{-- যোগাযোগ --}}
                <div>
                    <div class="section-title">যোগাযোগ</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" x-model="createForm.phone" @blur="validateCreateField('phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.phone || (createAttempted && !createForm.phone)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.phone || (createAttempted && !createForm.phone)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.phone || 'ফোন/মোবাইল আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ই-মেইল</label>
                            <input type="email" name="email" x-model="createForm.email" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.email) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.email"><p class="mt-1 text-xs text-red-600" x-text="createErrors.email || 'সঠিক ইমেইল দিন।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">জরুরি প্রয়োজনে <span class="text-red-500">*</span></label>
                            <input type="text" name="emergency_contact" x-model="createForm.emergency_contact" @blur="validateCreateField('emergency_contact')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.emergency_contact || (createAttempted && !createForm.emergency_contact)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.emergency_contact || (createAttempted && !createForm.emergency_contact)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.emergency_contact || 'জরুরি প্রয়োজনে ফোন আবশ্যক।'"></p></template>
                        </div>
                    </div>
                </div>

                {{-- অভিভাবকের বিবরণ --}}
                <div>
                    <div class="section-title">অভিভাবকের বিবরণ</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">আইনানুগ অভিভাবকের নাম <span class="text-red-500">*</span> <span class="text-xs font-normal text-gray-400">(পিতা-মাতার অবর্তমানে)</span></label>
                            <input type="text" name="legal_guardian_name" x-model="createForm.legal_guardian_name" @blur="validateCreateField('legal_guardian_name')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.legal_guardian_name || (createAttempted && !createForm.legal_guardian_name)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.legal_guardian_name || (createAttempted && !createForm.legal_guardian_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.legal_guardian_name || 'আইনানুগ অভিভাবকের নাম আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">আইনানুগ অভিভাবকের পেশা <span class="text-red-500">*</span></label>
                            <input type="text" name="legal_guardian_occupation" x-model="createForm.legal_guardian_occupation" @blur="validateCreateField('legal_guardian_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.legal_guardian_occupation || (createAttempted && !createForm.legal_guardian_occupation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.legal_guardian_occupation || (createAttempted && !createForm.legal_guardian_occupation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.legal_guardian_occupation || 'আইনানুগ অভিভাবকের পেশা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর সাথে সম্পর্ক <span class="text-red-500">*</span></label>
                            <input type="text" name="legal_guardian_relation" x-model="createForm.legal_guardian_relation" @blur="validateCreateField('legal_guardian_relation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.legal_guardian_relation || (createAttempted && !createForm.legal_guardian_relation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.legal_guardian_relation || (createAttempted && !createForm.legal_guardian_relation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.legal_guardian_relation || 'আইনানুগ অভিভাবকের সম্পর্ক আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ঠিকানা <span class="text-red-500">*</span></label>
                            <input type="text" name="legal_guardian_address" x-model="createForm.legal_guardian_address" @blur="validateCreateField('legal_guardian_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.legal_guardian_address || (createAttempted && !createForm.legal_guardian_address)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.legal_guardian_address || (createAttempted && !createForm.legal_guardian_address)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.legal_guardian_address || 'আইনানুগ অভিভাবকের ঠিকানা আবশ্যক।'"></p></template>
                        </div>
                        <div class="sm:col-span-2"><hr class="border-gray-200"></div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থানীয় অভিভাবকের নাম <span class="text-red-500">*</span></label>
                            <input type="text" name="local_guardian_name" x-model="createForm.local_guardian_name" @blur="validateCreateField('local_guardian_name')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.local_guardian_name || (createAttempted && !createForm.local_guardian_name)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.local_guardian_name || (createAttempted && !createForm.local_guardian_name)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.local_guardian_name || 'স্থানীয় অভিভাবকের নাম আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থানীয় অভিভাবকের পেশা <span class="text-red-500">*</span></label>
                            <input type="text" name="local_guardian_occupation" x-model="createForm.local_guardian_occupation" @blur="validateCreateField('local_guardian_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.local_guardian_occupation || (createAttempted && !createForm.local_guardian_occupation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.local_guardian_occupation || (createAttempted && !createForm.local_guardian_occupation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.local_guardian_occupation || 'স্থানীয় অভিভাবকের পেশা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর সাথে সম্পর্ক <span class="text-red-500">*</span></label>
                            <input type="text" name="local_guardian_relation" x-model="createForm.local_guardian_relation" @blur="validateCreateField('local_guardian_relation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.local_guardian_relation || (createAttempted && !createForm.local_guardian_relation)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.local_guardian_relation || (createAttempted && !createForm.local_guardian_relation)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.local_guardian_relation || 'স্থানীয় অভিভাবকের সম্পর্ক আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ঠিকানা <span class="text-red-500">*</span></label>
                            <input type="text" name="local_guardian_address" x-model="createForm.local_guardian_address" @blur="validateCreateField('local_guardian_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.local_guardian_address || (createAttempted && !createForm.local_guardian_address)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.local_guardian_address || (createAttempted && !createForm.local_guardian_address)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.local_guardian_address || 'স্থানীয় অভিভাবকের ঠিকানা আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                            <input type="text" name="local_guardian_phone" x-model="createForm.local_guardian_phone" @blur="validateCreateField('local_guardian_phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.local_guardian_phone || (createAttempted && !createForm.local_guardian_phone)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.local_guardian_phone || (createAttempted && !createForm.local_guardian_phone)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.local_guardian_phone || 'স্থানীয় অভিভাবকের ফোন আবশ্যক।'"></p></template>
                        </div>
                    </div>
                </div>

                {{-- পূর্ববর্তী শ্রেণির বিবরণ --}}
                <div>
                    <div class="section-title">পূর্ববর্তী শ্রেণির বিবরণ</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">শিক্ষা প্রতিষ্ঠানের নাম</label>
                            <input type="text" name="prev_school_name" x-model="createForm.prev_school_name" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">প্রতিষ্ঠানের ঠিকানা</label>
                            <input type="text" name="prev_school_address" x-model="createForm.prev_school_address" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">শ্রেণির রোল নং</label>
                            <input type="text" name="prev_roll_no" x-model="createForm.prev_roll_no" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">বার্ষিক পরীক্ষার প্রাপ্ত নম্বর</label>
                            <input type="text" name="prev_marks" x-model="createForm.prev_marks" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                </div>

                {{-- রেফারেন্স --}}
                <div>
                    <div class="section-title">রেফারেন্স ও ফোন</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">রেফারেন্স <span class="text-red-500">*</span></label>
                            <input type="text" name="reference" x-model="createForm.reference" @blur="validateCreateField('reference')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.reference || (createAttempted && !createForm.reference)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.reference || (createAttempted && !createForm.reference)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.reference || 'রেফারেন্স আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                            <input type="text" name="reference_phone" x-model="createForm.reference_phone" @blur="validateCreateField('reference_phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(createErrors.reference_phone || (createAttempted && !createForm.reference_phone)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.reference_phone || (createAttempted && !createForm.reference_phone)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.reference_phone || 'রেফারেন্সের ফোন আবশ্যক।'"></p></template>
                        </div>
                    </div>
                </div>

                {{-- ছবি আপলোড --}}
                <label class="block border-2 border-dashed border-ris-primary/40 rounded-lg p-5 text-center cursor-pointer hover:bg-ris-primary/5 transition-colors">
                    <span class="block text-lg font-medium text-gray-700 mb-2">শিক্ষার্থীর পাসপোর্ট সাইজের রঙিন ছবি <span class="text-red-500">*</span></span>
                    <img x-show="createPhotoPreview" :src="createPhotoPreview" alt="ছবি প্রিভিউ" class="w-20 h-24 object-cover rounded-lg border border-gray-300 mx-auto mb-3">
                    <svg class="w-10 h-10 mx-auto mb-2 text-ris-primary/50 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
<span class="block text-lg text-gray-600 mb-1">ছবি আপলোড করতে ক্লিক করুন</span>
                        <span class="block text-sm text-gray-400">JPG, JPEG বা PNG (সর্বোচ্চ ২MB)</span>
                        <p x-show="createErrors.student_photo" x-text="createErrors.student_photo" class="block text-sm text-red-600 mt-1"></p>
                    <input type="file" name="student_photo" accept="image/*" @change="previewCreatePhoto($event)" class="hidden">
                </label>

                {{-- স্ট্যাটাস --}}
                <div>
                    <div class="section-title">স্ট্যাটাস</div>
                    <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-1.5">স্ট্যাটাস <span class="text-red-500">*</span></label>
                            <select name="status" x-model="createForm.status" @change="validateCreateField('status')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="createErrors.status ? 'border-red-400' : 'border-gray-200'">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.status"><p class="mt-1 text-xs text-red-600" x-text="createErrors.status"></p></template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-6 py-3 bg-gray-100 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                    <button type="submit" class="px-7 py-3 bg-ris-primary text-white text-base font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">তৈরি করুন</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showViewModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">ভর্তি আবেদনের তথ্য</h3>
                <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="viewData">
                    <div>
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <span class="inline-flex items-center px-4 py-2 bg-ris-primary/10 border border-ris-primary/20 rounded-lg font-heading font-bold text-ris-primary tracking-wider" x-text="viewData.admission_no || '—'"></span>
                            <img x-show="viewData.student_photo" :src="viewData.student_photo" alt="শিক্ষার্থীর ছবি" class="w-20 h-24 object-cover rounded-lg border border-gray-200">
                        </div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 mb-1">শিক্ষার্থীর নাম (বাংলা)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.student_name_bn || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">শিক্ষার্থীর নাম (ইংরেজি)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.student_name_en || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">জন্ম তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.dob || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">বয়স</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.age || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.class_level || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ব্যাচ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.batch || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">রোল নং</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.roll_no || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">সেকশন</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.section || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">জাতীয়তা</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.nationality || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ধর্ম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.religion || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ব্লাড গ্রুপ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.blood_group || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ভর্তির তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.admission_date || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ফরম সংগ্রহের তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.form_collect_date || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ফরম জমা দেয়ার তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.form_submit_date || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পিতার নাম (বাংলা)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.father_name_bn || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পিতার নাম (ইংরেজি)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.father_name_en || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পিতার পেশা</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.father_occupation || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মাতার নাম (বাংলা)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.mother_name_bn || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মাতার নাম (ইংরেজি)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.mother_name_en || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মাতার পেশা</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.mother_occupation || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">বর্তমান ঠিকানা</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.present_address || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">স্থায়ী ঠিকানা</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.permanent_address || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ফোন/মোবাইল</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.phone || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ই-মেইল</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.email || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">জরুরি প্রয়োজনে</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.emergency_contact || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">অভিভাবক (আইনানুগ)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.legal_guardian_name || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">অভিভাবক (স্থানীয়)</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.local_guardian_name || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">পূর্ববর্তী প্রতিষ্ঠান</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.prev_school_name || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">রেফারেন্স</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.reference || '-'"></dd>
                            </div>
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
                                <dt class="text-gray-500 mb-1">আবেদনের তারিখ</dt>
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
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">ভর্তি আবেদন সম্পাদনা</h3>
                <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="editData">
                    <form id="admissionEditForm" :action="'{{ url('admin/admission') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-6" @submit.prevent="validateEditForm($el)">
                        @csrf
                        @method('PUT')

                        {{-- অফিস পূরণ করবে --}}
                        <div>
                            <div class="section-title">অফিস পূরণ করবে</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">শিক্ষাবর্ষ <span class="text-red-500">*</span></label>
                                    <input type="text" name="academic_year" x-model="editData.academic_year" maxlength="2" @blur="validateEditField('academic_year')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.academic_year || (editAttempted && !editData.academic_year)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.academic_year || (editAttempted && !editData.academic_year)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.academic_year || 'শিক্ষাবর্ষ আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">রোল নং/ID No <span class="text-red-500">*</span></label>
                                    <input type="text" name="roll_no" x-model="editData.roll_no" @blur="validateEditField('roll_no')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.roll_no || (editAttempted && !editData.roll_no)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.roll_no || (editAttempted && !editData.roll_no)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.roll_no || 'রোল নং আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">সেকশন <span class="text-red-500">*</span></label>
                                    <input type="text" name="section" x-model="editData.section" @blur="validateEditField('section')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.section || (editAttempted && !editData.section)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.section || (editAttempted && !editData.section)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.section || 'সেকশন আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ভর্তির তারিখ <span class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="admission_date" x-model="editData.admission_date" @blur="validateEditField('admission_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.admission_date || (editAttempted && !editData.admission_date)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.admission_date || (editAttempted && !editData.admission_date)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.admission_date || 'ভর্তির তারিখ আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ফরম সংগ্রহের তারিখ <span class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="form_collect_date" x-model="editData.form_collect_date" @blur="validateEditField('form_collect_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.form_collect_date || (editAttempted && !editData.form_collect_date)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.form_collect_date || (editAttempted && !editData.form_collect_date)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.form_collect_date || 'ফরম সংগ্রহের তারিখ আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ফরম জমা দেয়ার তারিখ <span class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="form_submit_date" x-model="editData.form_submit_date" @blur="validateEditField('form_submit_date')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.form_submit_date || (editAttempted && !editData.form_submit_date)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.form_submit_date || (editAttempted && !editData.form_submit_date)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.form_submit_date || 'ফরম জমা দেয়ার তারিখ আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ব্যাচ <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-4">
                                        @foreach($batches as $batch)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="batch" value="{{ $batch }}" x-model="editData.batch" class="accent-ris-primary">
                                                <span class="text-lg text-gray-700">{{ $batch }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <template x-if="editErrors.batch || (editAttempted && !editData.batch)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.batch || 'ব্যাচ নির্বাচন করুন।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">শ্রেণি <span class="text-red-500">*</span></label>
                                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                                        @foreach($classes as $class)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="class_level" value="{{ $class }}" x-model="editData.class_level" class="accent-ris-primary">
                                                <span class="text-lg text-gray-700">{{ $class }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <template x-if="editErrors.class_level || (editAttempted && !editData.class_level)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.class_level || 'শ্রেণি নির্বাচন করুন।'"></p></template>
                                </div>
                            </div>
                        </div>

                        {{-- শিক্ষার্থী ও পিতা-মাতার বিবরণ --}}
                        <div>
                            <div class="section-title">ছাত্র/ছাত্রীর ও পিতা-মাতার বিবরণ</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর নাম (বাংলায়) <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_name_bn" x-model="editData.student_name_bn" @blur="validateEditField('student_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.student_name_bn || (editAttempted && !editData.student_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.student_name_bn || (editAttempted && !editData.student_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.student_name_bn || 'ছাত্র/ছাত্রীর নাম আবশ্যক'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ইংরেজিতে বড় অক্ষরে <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_name_en" x-model="editData.student_name_en" @blur="validateEditField('student_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(editErrors.student_name_en || (editAttempted && !editData.student_name_en)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.student_name_en || (editAttempted && !editData.student_name_en)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.student_name_en || 'ইংরেজিতে নাম আবশ্যক'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">জন্ম তারিখ <span class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="dob" x-model="editData.dob" @blur="validateEditField('dob')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.dob || (editAttempted && !editData.dob)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.dob || (editAttempted && !editData.dob)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.dob || 'জন্ম তারিখ আবশ্যক'"></p></template>
                                </div>
<div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-lg font-medium text-gray-700 mb-1.5">বয়স <span class="text-red-500">*</span></label>
                                            <input type="text" name="age" x-model="editData.age" @blur="validateEditField('age')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.age || (editAttempted && !editData.age)) ? 'border-red-400' : 'border-gray-200'">
                                            <template x-if="editErrors.age || (editAttempted && !editData.age)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.age || 'বয়স আবশ্যক।'"></p></template>
                                        </div>
                                        <div>
                                            <label class="block text-lg font-medium text-gray-700 mb-1.5">ব্লাড গ্রুপ <span class="text-red-500">*</span></label>
                                            <input type="text" name="blood_group" x-model="editData.blood_group" @blur="validateEditField('blood_group')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.blood_group || (editAttempted && !editData.blood_group)) ? 'border-red-400' : 'border-gray-200'">
                                            <template x-if="editErrors.blood_group || (editAttempted && !editData.blood_group)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.blood_group || 'ব্লাড গ্রুপ আবশ্যক।'"></p></template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-lg font-medium text-gray-700 mb-1.5">জাতীয়তা <span class="text-red-500">*</span></label>
                                        <input type="text" name="nationality" x-model="editData.nationality" @blur="validateEditField('nationality')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.nationality || (editAttempted && !editData.nationality)) ? 'border-red-400' : 'border-gray-200'">
                                        <template x-if="editErrors.nationality || (editAttempted && !editData.nationality)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.nationality || 'জাতীয়তা আবশ্যক।'"></p></template>
                                    </div>
                                    <div>
                                        <label class="block text-lg font-medium text-gray-700 mb-1.5">ধর্ম <span class="text-red-500">*</span></label>
                                        <input type="text" name="religion" x-model="editData.religion" @blur="validateEditField('religion')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.religion || (editAttempted && !editData.religion)) ? 'border-red-400' : 'border-gray-200'">
                                        <template x-if="editErrors.religion || (editAttempted && !editData.religion)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.religion || 'ধর্ম আবশ্যক।'"></p></template>
                                    </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার নাম (বাংলায়) <span class="text-red-500">*</span></label>
                                    <input type="text" name="father_name_bn" x-model="editData.father_name_bn" @blur="validateEditField('father_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.father_name_bn || (editAttempted && !editData.father_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.father_name_bn || (editAttempted && !editData.father_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.father_name_bn || 'পিতার নাম আবশ্যক'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার নাম (ইংরেজি) <span class="text-red-500">*</span></label>
                                    <input type="text" name="father_name_en" x-model="editData.father_name_en" @blur="validateEditField('father_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(editErrors.father_name_en || (editAttempted && !editData.father_name_en)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.father_name_en || (editAttempted && !editData.father_name_en)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.father_name_en || 'পিতার নাম (ইংরেজি) আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">পিতার পেশা ও পদবী <span class="text-red-500">*</span></label>
                                    <input type="text" name="father_occupation" x-model="editData.father_occupation" @blur="validateEditField('father_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.father_occupation || (editAttempted && !editData.father_occupation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.father_occupation || (editAttempted && !editData.father_occupation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.father_occupation || 'পিতার পেশা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার নাম (বাংলায়) <span class="text-red-500">*</span></label>
                                    <input type="text" name="mother_name_bn" x-model="editData.mother_name_bn" @blur="validateEditField('mother_name_bn')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.mother_name_bn || (editAttempted && !editData.mother_name_bn)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.mother_name_bn || (editAttempted && !editData.mother_name_bn)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.mother_name_bn || 'মাতার নাম আবশ্যক'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার নাম (ইংরেজি) <span class="text-red-500">*</span></label>
                                    <input type="text" name="mother_name_en" x-model="editData.mother_name_en" @blur="validateEditField('mother_name_en')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors uppercase" :class="(editErrors.mother_name_en || (editAttempted && !editData.mother_name_en)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.mother_name_en || (editAttempted && !editData.mother_name_en)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.mother_name_en || 'মাতার নাম (ইংরেজি) আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">মাতার পেশা ও পদবী <span class="text-red-500">*</span></label>
                                    <input type="text" name="mother_occupation" x-model="editData.mother_occupation" @blur="validateEditField('mother_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.mother_occupation || (editAttempted && !editData.mother_occupation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.mother_occupation || (editAttempted && !editData.mother_occupation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.mother_occupation || 'মাতার পেশা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">বর্তমান ঠিকানা <span class="text-red-500">*</span></label>
                                    <textarea name="present_address" x-model="editData.present_address" rows="2" @blur="validateEditField('present_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none" :class="(editErrors.present_address || (editAttempted && !editData.present_address)) ? 'border-red-400' : 'border-gray-200'"></textarea>
                                    <template x-if="editErrors.present_address || (editAttempted && !editData.present_address)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.present_address || 'বর্তমান ঠিকানা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থায়ী ঠিকানা <span class="text-red-500">*</span></label>
                                    <textarea name="permanent_address" x-model="editData.permanent_address" rows="2" @blur="validateEditField('permanent_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none" :class="(editErrors.permanent_address || (editAttempted && !editData.permanent_address)) ? 'border-red-400' : 'border-gray-200'"></textarea>
                                    <template x-if="editErrors.permanent_address || (editAttempted && !editData.permanent_address)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.permanent_address || 'স্থায়ী ঠিকানা আবশ্যক।'"></p></template>
                                </div>
                            </div>
                        </div>

                        {{-- যোগাযোগ --}}
                        <div>
                            <div class="section-title">যোগাযোগ</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" x-model="editData.phone" @blur="validateEditField('phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.phone || (editAttempted && !editData.phone)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.phone || (editAttempted && !editData.phone)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.phone || 'ফোন/মোবাইল আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ই-মেইল</label>
                                    <input type="email" name="email" x-model="editData.email" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.email) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.email"><p class="mt-1 text-xs text-red-600" x-text="editErrors.email || 'সঠিক ইমেইল দিন।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">জরুরি প্রয়োজনে <span class="text-red-500">*</span></label>
                                    <input type="text" name="emergency_contact" x-model="editData.emergency_contact" @blur="validateEditField('emergency_contact')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.emergency_contact || (editAttempted && !editData.emergency_contact)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.emergency_contact || (editAttempted && !editData.emergency_contact)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.emergency_contact || 'জরুরি প্রয়োজনে ফোন আবশ্যক।'"></p></template>
                                </div>
                            </div>
                        </div>

                        {{-- অভিভাবকের বিবরণ --}}
                        <div>
                            <div class="section-title">অভিভাবকের বিবরণ</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">আইনানুগ অভিভাবকের নাম <span class="text-red-500">*</span> <span class="text-xs font-normal text-gray-400">(পিতা-মাতার অবর্তমানে)</span></label>
                                    <input type="text" name="legal_guardian_name" x-model="editData.legal_guardian_name" @blur="validateEditField('legal_guardian_name')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.legal_guardian_name || (editAttempted && !editData.legal_guardian_name)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.legal_guardian_name || (editAttempted && !editData.legal_guardian_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.legal_guardian_name || 'আইনানুগ অভিভাবকের নাম আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">আইনানুগ অভিভাবকের পেশা <span class="text-red-500">*</span></label>
                                    <input type="text" name="legal_guardian_occupation" x-model="editData.legal_guardian_occupation" @blur="validateEditField('legal_guardian_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.legal_guardian_occupation || (editAttempted && !editData.legal_guardian_occupation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.legal_guardian_occupation || (editAttempted && !editData.legal_guardian_occupation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.legal_guardian_occupation || 'আইনানুগ অভিভাবকের পেশা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর সাথে সম্পর্ক <span class="text-red-500">*</span></label>
                                    <input type="text" name="legal_guardian_relation" x-model="editData.legal_guardian_relation" @blur="validateEditField('legal_guardian_relation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.legal_guardian_relation || (editAttempted && !editData.legal_guardian_relation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.legal_guardian_relation || (editAttempted && !editData.legal_guardian_relation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.legal_guardian_relation || 'আইনানুগ অভিভাবকের সম্পর্ক আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ঠিকানা <span class="text-red-500">*</span></label>
                                    <input type="text" name="legal_guardian_address" x-model="editData.legal_guardian_address" @blur="validateEditField('legal_guardian_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.legal_guardian_address || (editAttempted && !editData.legal_guardian_address)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.legal_guardian_address || (editAttempted && !editData.legal_guardian_address)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.legal_guardian_address || 'আইনানুগ অভিভাবকের ঠিকানা আবশ্যক।'"></p></template>
                                </div>
                                <div class="sm:col-span-2"><hr class="border-gray-200"></div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থানীয় অভিভাবকের নাম <span class="text-red-500">*</span></label>
                                    <input type="text" name="local_guardian_name" x-model="editData.local_guardian_name" @blur="validateEditField('local_guardian_name')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.local_guardian_name || (editAttempted && !editData.local_guardian_name)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.local_guardian_name || (editAttempted && !editData.local_guardian_name)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.local_guardian_name || 'স্থানীয় অভিভাবকের নাম আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">স্থানীয় অভিভাবকের পেশা <span class="text-red-500">*</span></label>
                                    <input type="text" name="local_guardian_occupation" x-model="editData.local_guardian_occupation" @blur="validateEditField('local_guardian_occupation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.local_guardian_occupation || (editAttempted && !editData.local_guardian_occupation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.local_guardian_occupation || (editAttempted && !editData.local_guardian_occupation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.local_guardian_occupation || 'স্থানীয় অভিভাবকের পেশা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ছাত্র/ছাত্রীর সাথে সম্পর্ক <span class="text-red-500">*</span></label>
                                    <input type="text" name="local_guardian_relation" x-model="editData.local_guardian_relation" @blur="validateEditField('local_guardian_relation')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.local_guardian_relation || (editAttempted && !editData.local_guardian_relation)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.local_guardian_relation || (editAttempted && !editData.local_guardian_relation)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.local_guardian_relation || 'স্থানীয় অভিভাবকের সম্পর্ক আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ঠিকানা <span class="text-red-500">*</span></label>
                                    <input type="text" name="local_guardian_address" x-model="editData.local_guardian_address" @blur="validateEditField('local_guardian_address')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.local_guardian_address || (editAttempted && !editData.local_guardian_address)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.local_guardian_address || (editAttempted && !editData.local_guardian_address)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.local_guardian_address || 'স্থানীয় অভিভাবকের ঠিকানা আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                                    <input type="text" name="local_guardian_phone" x-model="editData.local_guardian_phone" @blur="validateEditField('local_guardian_phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.local_guardian_phone || (editAttempted && !editData.local_guardian_phone)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.local_guardian_phone || (editAttempted && !editData.local_guardian_phone)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.local_guardian_phone || 'স্থানীয় অভিভাবকের ফোন আবশ্যক।'"></p></template>
                                </div>
                            </div>
                        </div>

                        {{-- পূর্ববর্তী শ্রেণির বিবরণ --}}
                        <div>
                            <div class="section-title">পূর্ববর্তী শ্রেণির বিবরণ</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">শিক্ষা প্রতিষ্ঠানের নাম</label>
                                    <input type="text" name="prev_school_name" x-model="editData.prev_school_name" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">প্রতিষ্ঠানের ঠিকানা</label>
                                    <input type="text" name="prev_school_address" x-model="editData.prev_school_address" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">শ্রেণির রোল নং</label>
                                    <input type="text" name="prev_roll_no" x-model="editData.prev_roll_no" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">বার্ষিক পরীক্ষার প্রাপ্ত নম্বর</label>
                                    <input type="text" name="prev_marks" x-model="editData.prev_marks" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                            </div>
                        </div>

                        {{-- রেফারেন্স --}}
                        <div>
                            <div class="section-title">রেফারেন্স ও ফোন</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">রেফারেন্স <span class="text-red-500">*</span></label>
                                    <input type="text" name="reference" x-model="editData.reference" @blur="validateEditField('reference')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.reference || (editAttempted && !editData.reference)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.reference || (editAttempted && !editData.reference)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.reference || 'রেফারেন্স আবশ্যক।'"></p></template>
                                </div>
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">ফোন/মোবাইল <span class="text-red-500">*</span></label>
                                    <input type="text" name="reference_phone" x-model="editData.reference_phone" @blur="validateEditField('reference_phone')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors" :class="(editErrors.reference_phone || (editAttempted && !editData.reference_phone)) ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.reference_phone || (editAttempted && !editData.reference_phone)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.reference_phone || 'রেফারেন্সের ফোন আবশ্যক।'"></p></template>
                                </div>
                            </div>
                        </div>

                        {{-- ছবি আপলোড --}}
                        <label class="block border-2 border-dashed border-ris-primary/40 rounded-lg p-5 text-center cursor-pointer hover:bg-ris-primary/5 transition-colors">
                            <span class="block text-lg font-medium text-gray-700 mb-2">শিক্ষার্থীর পাসপোর্ট সাইজের রঙিন ছবি <span class="text-red-500">*</span> <span class="text-sm font-normal text-gray-400">(নতুন ছবি দিলে পুরনোটি প্রতিস্থাপিত হবে)</span></span>
                            <img x-show="editPhotoPreview" :src="editPhotoPreview" alt="ছবি প্রিভিউ" class="w-20 h-24 object-cover rounded-lg border border-gray-300 mx-auto mb-3">
                            <svg class="w-10 h-10 mx-auto mb-2 text-ris-primary/50 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="block text-lg text-gray-600 mb-1">নতুন ছবি আপলোড করতে ক্লিক করুন</span>
                            <span class="block text-sm text-gray-400">JPG, JPEG বা PNG (সর্বোচ্চ ২MB)</span>
                            <p x-show="editErrors.student_photo" x-text="editErrors.student_photo" class="block text-sm text-red-600 mt-1"></p>
                            <input type="file" name="student_photo" accept="image/*" @change="previewEditPhoto($event)" class="hidden">
                        </label>

                        {{-- স্ট্যাটাস --}}
                        <div>
                            <div class="section-title">স্ট্যাটাস</div>
                            <div class="border border-t-0 border-gray-300 rounded-b-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-1.5">স্ট্যাটাস <span class="text-red-500">*</span></label>
                                    <select name="status" x-model="editData.status" @change="validateEditField('status')" class="w-full px-4 py-3.5 border rounded-lg text-lg focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white" :class="editErrors.status ? 'border-red-400' : 'border-gray-200'">
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.status"><p class="mt-1 text-xs text-red-600" x-text="editErrors.status"></p></template>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showEditModal = false" class="px-6 py-3 bg-gray-100 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                            <button type="submit" class="px-7 py-3 bg-ris-primary text-white text-base font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">আপডেট করুন</button>
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
function admissionApp() {
    return {
        showCreateModal: false,
        showViewModal: false,
        showEditModal: false,
        viewData: null,
        editData: null,
        viewLoading: false,
        editLoading: false,
        createPhotoPreview: null,
        editPhotoPreview: null,

        defaultAcademicYear: '{{ $defaultAcademicYear }}',

        createForm: { academic_year: '{{ $defaultAcademicYear }}', roll_no: '', section: '', batch: '', admission_date: '', form_collect_date: '', form_submit_date: '', class_level: '', student_name_bn: '', student_name_en: '', dob: '', age: '', blood_group: '', nationality: '', religion: '', father_name_bn: '', father_name_en: '', father_occupation: '', mother_name_bn: '', mother_name_en: '', mother_occupation: '', present_address: '', permanent_address: '', phone: '', email: '', emergency_contact: '', legal_guardian_name: '', legal_guardian_occupation: '', legal_guardian_relation: '', legal_guardian_address: '', local_guardian_name: '', local_guardian_occupation: '', local_guardian_relation: '', local_guardian_address: '', local_guardian_phone: '', prev_school_name: '', prev_school_address: '', prev_roll_no: '', prev_marks: '', reference: '', reference_phone: '', status: 'pending' },
        createErrors: {},
        createAttempted: false,

        editErrors: {},
        editAttempted: false,

        openCreateModal() {
            this.createForm = { academic_year: '{{ $defaultAcademicYear }}', roll_no: '', section: '', batch: '', admission_date: '', form_collect_date: '', form_submit_date: '', class_level: '', student_name_bn: '', student_name_en: '', dob: '', age: '', blood_group: '', nationality: '', religion: '', father_name_bn: '', father_name_en: '', father_occupation: '', mother_name_bn: '', mother_name_en: '', mother_occupation: '', present_address: '', permanent_address: '', phone: '', email: '', emergency_contact: '', legal_guardian_name: '', legal_guardian_occupation: '', legal_guardian_relation: '', legal_guardian_address: '', local_guardian_name: '', local_guardian_occupation: '', local_guardian_relation: '', local_guardian_address: '', local_guardian_phone: '', prev_school_name: '', prev_school_address: '', prev_roll_no: '', prev_marks: '', reference: '', reference_phone: '', status: 'pending' };
            this.createErrors = {};
            this.createAttempted = false;
            this.createPhotoPreview = null;
            this.showCreateModal = true;
        },

        async openViewModal(id) {
            this.showViewModal = true;
            this.viewLoading = true;
            this.viewData = null;
            try {
                const res = await fetch(`{{ url('admin/admission') }}/${id}/show`);
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
                const res = await fetch(`{{ url('admin/admission') }}/${id}/edit`);
                this.editData = await res.json();
                this.editPhotoPreview = this.editData.student_photo || null;
                this.$nextTick(() => {
                    const form = document.getElementById('admissionEditForm');
                    if (form && !form.dataset.maskInit && window.RisDateMask?.init) {
                        form.dataset.maskInit = '1';
                        window.RisDateMask.init(form);
                    }
                });
            } catch (e) {
                this.showEditModal = false;
                alert('তথ্য লোড করতে সমস্যা হয়েছে।');
            } finally {
                this.editLoading = false;
            }
        },

        previewCreatePhoto(event) {
            this.createPhotoPreview = this.previewFile(event);
        },

        previewEditPhoto(event) {
            this.editPhotoPreview = this.previewFile(event);
        },

        previewFile(event) {
            const file = event.target.files[0];
            return file ? URL.createObjectURL(file) : null;
        },

        validateField(field, data, errors) {
            delete errors[field];
            const val = data[field];
            const required = ['academic_year', 'roll_no', 'section', 'batch', 'admission_date', 'form_collect_date', 'form_submit_date', 'class_level', 'student_name_bn', 'student_name_en', 'dob', 'age', 'nationality', 'religion', 'blood_group', 'father_name_bn', 'father_name_en', 'father_occupation', 'mother_name_bn', 'mother_name_en', 'mother_occupation', 'present_address', 'permanent_address', 'phone', 'emergency_contact', 'legal_guardian_name', 'legal_guardian_occupation', 'legal_guardian_relation', 'legal_guardian_address', 'local_guardian_name', 'local_guardian_occupation', 'local_guardian_relation', 'local_guardian_address', 'local_guardian_phone', 'reference', 'reference_phone', 'status'];
            if (required.includes(field)) {
                if (!val || (typeof val === 'string' && val.trim() === '')) {
                    const msgs = {
                        academic_year: 'শিক্ষাবর্ষ আবশ্যক।', roll_no: 'রোল নং আবশ্যক।', section: 'সেকশন আবশ্যক।', batch: 'ব্যাচ নির্বাচন করুন।',
                        admission_date: 'ভর্তির তারিখ আবশ্যক।', form_collect_date: 'ফরম সংগ্রহের তারিখ আবশ্যক।', form_submit_date: 'ফরম জমা দেয়ার তারিখ আবশ্যক।',
                        class_level: 'শ্রেণি নির্বাচন করুন।', student_name_bn: 'ছাত্র/ছাত্রীর নাম আবশ্যক।', student_name_en: 'ইংরেজিতে নাম আবশ্যক।',
                        dob: 'জন্ম তারিখ আবশ্যক।', age: 'বয়স আবশ্যক।', nationality: 'জাতীয়তা আবশ্যক।', religion: 'ধর্ম আবশ্যক।', blood_group: 'ব্লাড গ্রুপ আবশ্যক।',
                        father_name_bn: 'পিতার নাম আবশ্যক।', father_name_en: 'পিতার নাম (ইংরেজি) আবশ্যক।', father_occupation: 'পিতার পেশা আবশ্যক।',
                        mother_name_bn: 'মাতার নাম আবশ্যক।', mother_name_en: 'মাতার নাম (ইংরেজি) আবশ্যক।', mother_occupation: 'মাতার পেশা আবশ্যক।',
                        present_address: 'বর্তমান ঠিকানা আবশ্যক।', permanent_address: 'স্থায়ী ঠিকানা আবশ্যক।', phone: 'ফোন/মোবাইল আবশ্যক।',
                        emergency_contact: 'জরুরি প্রয়োজনে ফোন আবশ্যক।', legal_guardian_name: 'আইনানুগ অভিভাবকের নাম আবশ্যক।',
                        legal_guardian_occupation: 'আইনানুগ অভিভাবকের পেশা আবশ্যক।', legal_guardian_relation: 'আইনানুগ অভিভাবকের সম্পর্ক আবশ্যক।',
                        legal_guardian_address: 'আইনানুগ অভিভাবকের ঠিকানা আবশ্যক।', local_guardian_name: 'স্থানীয় অভিভাবকের নাম আবশ্যক।',
                        local_guardian_occupation: 'স্থানীয় অভিভাবকের পেশা আবশ্যক।', local_guardian_relation: 'স্থানীয় অভিভাবকের সম্পর্ক আবশ্যক।',
                        local_guardian_address: 'স্থানীয় অভিভাবকের ঠিকানা আবশ্যক।', local_guardian_phone: 'স্থানীয় অভিভাবকের ফোন আবশ্যক।',
                        reference: 'রেফারেন্স আবশ্যক।', reference_phone: 'রেফারেন্সের ফোন আবশ্যক।',
                        status: 'স্ট্যাটাস নির্বাচন করুন।'
                    };
                    errors[field] = msgs[field];
                    return false;
                }
            }
            if (field === 'email' && val && !/^\S+@\S+\.\S+$/.test(val)) {
                errors[field] = 'সঠিক ইমেইল দিন।';
                return false;
            }
            return true;
        },

        validateCreateField(field) {
            return this.validateField(field, this.createForm, this.createErrors);
        },

        validateEditField(field) {
            return this.validateField(field, this.editData, this.editErrors);
        },

        scrollToFirstError(errors, el) {
            if (!errors || Object.keys(errors).length === 0) return;
            this.$nextTick(() => {
                const firstKey = Object.keys(errors)[0];
                const target = el.querySelector(`[name="${firstKey}"]`) ||
                    [...el.querySelectorAll('[data-error-target]')].find(t => t.dataset.errorTarget === firstKey);
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        },

        validateCreateForm(el) {
            this.createAttempted = true;
            this.createErrors = {};
            let valid = true;
            const fields = ['academic_year', 'roll_no', 'section', 'batch', 'admission_date', 'form_collect_date', 'form_submit_date', 'class_level', 'student_name_bn', 'student_name_en', 'dob', 'age', 'nationality', 'religion', 'blood_group', 'father_name_bn', 'father_name_en', 'father_occupation', 'mother_name_bn', 'mother_name_en', 'mother_occupation', 'present_address', 'permanent_address', 'phone', 'emergency_contact', 'legal_guardian_name', 'legal_guardian_occupation', 'legal_guardian_relation', 'legal_guardian_address', 'local_guardian_name', 'local_guardian_occupation', 'local_guardian_relation', 'local_guardian_address', 'local_guardian_phone', 'reference', 'reference_phone', 'email', 'status'];
            fields.forEach(f => {
                if (!this.validateCreateField(f)) valid = false;
            });
            const photo = el.querySelector('[name="student_photo"]');
            if (!photo.files.length) {
                this.createErrors.student_photo = 'শিক্ষার্থীর ছবি আবশ্যক।';
                valid = false;
            }
            if (!valid) this.scrollToFirstError(this.createErrors, el);
            if (valid) el.submit();
        },

        validateEditForm(el) {
            this.editAttempted = true;
            this.editErrors = {};
            let valid = true;
            const fields = ['academic_year', 'roll_no', 'section', 'batch', 'admission_date', 'form_collect_date', 'form_submit_date', 'class_level', 'student_name_bn', 'student_name_en', 'dob', 'age', 'nationality', 'religion', 'blood_group', 'father_name_bn', 'father_name_en', 'father_occupation', 'mother_name_bn', 'mother_name_en', 'mother_occupation', 'present_address', 'permanent_address', 'phone', 'emergency_contact', 'legal_guardian_name', 'legal_guardian_occupation', 'legal_guardian_relation', 'legal_guardian_address', 'local_guardian_name', 'local_guardian_occupation', 'local_guardian_relation', 'local_guardian_address', 'local_guardian_phone', 'reference', 'reference_phone', 'email', 'status'];
            fields.forEach(f => {
                if (!this.validateEditField(f)) valid = false;
            });
            const photo = el.querySelector('[name="student_photo"]');
            if (!photo.files.length && !this.editData.student_photo) {
                this.editErrors.student_photo = 'শিক্ষার্থীর ছবি আবশ্যক।';
                valid = false;
            }
            if (!valid) this.scrollToFirstError(this.editErrors, el);
            if (valid) el.submit();
        }
    }
}
</script>
@endsection
@endsection