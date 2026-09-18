@extends('layouts.admin')

@section('title', 'পরীক্ষা তালিকা')

@section('content')
    <div class="space-y-6" x-data="examApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">পরীক্ষা</h1>
                <p class="text-sm text-gray-500 mt-1">সকল পরীক্ষা পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন পরীক্ষা
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.exams.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি ফিল্টার</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল শ্রেণি</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.exams.index') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পরীক্ষার নাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ধরন</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">তারিখ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মোট নম্বর</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পাস নম্বর</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($exams as $index => $exam)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($exams->currentPage() - 1) * $exams->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button @click="openViewModal({{ $exam->id }})"
                                        class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $exam->name }}</button>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $examType = \App\Enums\ExamType::tryFrom($exam->type ?? '');
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $examType?->color() ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $examType?->label() ?? ($exam->type ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $exam->classRoom->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $exam->start_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $exam->total_marks ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $exam->passing_marks ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openViewModal({{ $exam->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $exam->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.exams.results', $exam) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors text-xs font-medium"
                                            title="ফলাফল প্রবেশ করুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            ফলাফল
                                        </a>
                                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই পরীক্ষাটি মুছে ফেলতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
                                                title="মুছুন">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো পরীক্ষা পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন পরীক্ষা যোগ করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $exams])
        </div>

        {{-- Toast --}}
        <div x-show="toasts.length" x-cloak class="fixed top-5 right-5 z-9999 space-y-3">
            <template x-for="(toast, index) in toasts" :key="index">
                <div x-show="true" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                    :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' :
                        'bg-red-50 border-red-200 text-red-700'"
                    class="flex items-center gap-3 px-5 py-3 rounded-xl border shadow-lg min-w-75 max-w-112.5">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium" x-text="toast.message"></span>
                    <button @click="toasts.splice(index, 1)" class="ml-auto shrink-0 opacity-60 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">নতুন পরীক্ষা যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.exams.store') }}" method="POST" class="p-6 space-y-5"
                    @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">পরীক্ষার নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="createForm.name"
                                @blur="validateCreateField('name')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.name || (createAttempted && !createForm.name)) ? 'border-red-400' :
                                'border-gray-200'" placeholder="যেমন: অর্ধবার্ষিক পরীক্ষা">
                            <template x-if="createErrors.name || (createAttempted && !createForm.name)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.name || 'পরীক্ষার নাম আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ধরন <span
                                    class="text-red-500">*</span></label>
                            <select name="type" x-model="createForm.type" @change="validateCreateField('type')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.type || (createAttempted && !createForm.type)) ? 'border-red-400' :
                                'border-gray-200'">
                                <option value="">ধরন নির্বাচন করুন</option>
                                @foreach ($examTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.type || (createAttempted && !createForm.type)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.type || 'পরীক্ষার ধরন আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <select name="class_id" x-model="createForm.class_id" @change="validateCreateField('class_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.class_id || (createAttempted && !createForm.class_id)) ? 'border-red-400' :
                                'border-gray-200'">
                                <option value="">শ্রেণি নির্বাচন করুন</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.class_id || (createAttempted && !createForm.class_id)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.class_id || 'শ্রেণি নির্বাচন আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাবর্ষ <span
                                    class="text-red-500">*</span></label>
                            <select name="academic_year_id" x-model="createForm.academic_year_id" @change="validateCreateField('academic_year_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.academic_year_id || (createAttempted && !createForm.academic_year_id)) ? 'border-red-400' :
                                'border-gray-200'">
                                <option value="">শিক্ষাবর্ষ নির্বাচন করুন</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->yearLabel() }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.academic_year_id || (createAttempted && !createForm.academic_year_id)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.academic_year_id || 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শুরুর তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="start_date" x-model="createForm.start_date"
                                @blur="validateCreateField('start_date')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.start_date || (createAttempted && !createForm.start_date)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.start_date || (createAttempted && !createForm.start_date)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.start_date || 'শুরুর তারিখ আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শেষ তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="end_date" x-model="createForm.end_date"
                                @blur="validateCreateField('end_date')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.end_date || (createAttempted && !createForm.end_date)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.end_date || (createAttempted && !createForm.end_date)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.end_date || 'শেষ তারিখ আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">মোট নম্বর <span
                                    class="text-red-500">*</span></label>
                            <input name="total_marks" x-model="createForm.total_marks"
                                @blur="validateCreateField('total_marks')" min="1"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.total_marks || (createAttempted && !createForm.total_marks)) ? 'border-red-400' :
                                'border-gray-200'" placeholder="যেমন: 100">
                            <template x-if="createErrors.total_marks || (createAttempted && !createForm.total_marks)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.total_marks || 'মোট নম্বর আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পাস নম্বর <span
                                    class="text-red-500">*</span></label>
                            <input name="passing_marks" x-model="createForm.passing_marks"
                                @blur="validateCreateField('passing_marks')" min="1"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.passing_marks || (createAttempted && !createForm.passing_marks)) ? 'border-red-400' :
                                'border-gray-200'" placeholder="যেমন: 33">
                            <template x-if="createErrors.passing_marks || (createAttempted && !createForm.passing_marks)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.passing_marks || 'পাসের নম্বর আবশ্যক।'"></p>
                            </template>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false"
                            class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">সংরক্ষণ
                            করুন</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
        <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showViewModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">পরীক্ষা তথ্য</h3>
                    <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="viewData">
                        <div>
                            <div class="mb-5">
                                <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name"></h4>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-2"
                                    :class="{
                                        'bg-cyan-50 text-cyan-700': viewData.type === 'quiz',
                                        'bg-blue-50 text-blue-700': viewData.type === 'midterm',
                                        'bg-purple-50 text-purple-700': viewData.type === 'final',
                                        'bg-violet-50 text-violet-700': viewData.type === 'assignment'
                                    }"
                                    x-text="viewData.type_label"></span>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.class_name"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শিক্ষাবর্ষ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.academic_year_name"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শুরুর তারিখ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.start_date"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শেষ তারিখ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.end_date"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোট নম্বর</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.total_marks"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">পাস নম্বর</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.passing_marks"></dd>
                                </div>
                            </dl>
                            <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end gap-3">
                                <button @click="showViewModal = false"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বন্ধ
                                    করুন</button>
                                <button @click="showViewModal = false; openEditModal(viewData.id)"
                                    class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">সম্পাদনা</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="viewLoading">
                        <div class="py-12 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══════════════ EDIT MODAL ═══════════════ --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showEditModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">পরীক্ষা সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/exams') }}/' + editData.id" method="POST" class="space-y-5"
                            @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পরীক্ষার নাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editData.name"
                                        @blur="validateEditField('name')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.name || (editAttempted && !editData.name)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.name || (editAttempted && !editData.name)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.name || 'পরীক্ষার নাম আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ধরন <span
                                            class="text-red-500">*</span></label>
                                    <select name="type" x-model="editData.type" @change="validateEditField('type')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="(editErrors.type || (editAttempted && !editData.type)) ? 'border-red-400' :
                                        'border-gray-200'">
                                        <option value="">ধরন নির্বাচন করুন</option>
                                        @foreach ($examTypes as $type)
                                            <option value="{{ $type->value }}" x-text="'{{ $type->label() }}'"
                                                :selected="editData.type === '{{ $type->value }}'"></option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.type || (editAttempted && !editData.type)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.type || 'পরীক্ষার ধরন আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                            class="text-red-500">*</span></label>
                                    <select name="class_id" x-model="editData.class_id" @change="validateEditField('class_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="(editErrors.class_id || (editAttempted && !editData.class_id)) ? 'border-red-400' :
                                        'border-gray-200'">
                                        <option value="">শ্রেণি নির্বাচন করুন</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" x-text="'{{ $class->name }}'"
                                                :selected="editData.class_id === '{{ $class->id }}'"></option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.class_id || (editAttempted && !editData.class_id)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.class_id || 'শ্রেণি নির্বাচন আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাবর্ষ <span
                                            class="text-red-500">*</span></label>
                                    <select name="academic_year_id" x-model="editData.academic_year_id" @change="validateEditField('academic_year_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="(editErrors.academic_year_id || (editAttempted && !editData.academic_year_id)) ? 'border-red-400' :
                                        'border-gray-200'">
                                        <option value="">শিক্ষাবর্ষ নির্বাচন করুন</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}" x-text="'{{ $year->yearLabel() }}'"
                                                :selected="editData.academic_year_id === '{{ $year->id }}'"></option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.academic_year_id || (editAttempted && !editData.academic_year_id)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.academic_year_id || 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শুরুর তারিখ <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="start_date" x-model="editData.start_date"
                                        @blur="validateEditField('start_date')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.start_date || (editAttempted && !editData.start_date)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.start_date || (editAttempted && !editData.start_date)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.start_date || 'শুরুর তারিখ আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শেষ তারিখ <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="end_date" x-model="editData.end_date"
                                        @blur="validateEditField('end_date')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.end_date || (editAttempted && !editData.end_date)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.end_date || (editAttempted && !editData.end_date)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.end_date || 'শেষ তারিখ আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মোট নম্বর <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="total_marks" x-model="editData.total_marks"
                                        @blur="validateEditField('total_marks')" min="1"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.total_marks || (editAttempted && !editData.total_marks)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.total_marks || (editAttempted && !editData.total_marks)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.total_marks || 'মোট নম্বর আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পাস নম্বর <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="passing_marks" x-model="editData.passing_marks"
                                        @blur="validateEditField('passing_marks')" min="1"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.passing_marks || (editAttempted && !editData.passing_marks)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.passing_marks || (editAttempted && !editData.passing_marks)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.passing_marks || 'পাসের নম্বর আবশ্যক।'"></p>
                                    </template>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="showEditModal = false"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">আপডেট
                                    করুন</button>
                            </div>
                        </form>
                    </template>
                    <template x-if="editLoading">
                        <div class="py-12 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    @section('scripts')
        <script>
            function examApp() {
                return {
                    showCreateModal: false,
                    showViewModal: false,
                    showEditModal: false,
                    viewData: null,
                    editData: null,
                    viewLoading: false,
                    editLoading: false,
                    toasts: [],

                    createForm: {
                        name: '',
                        type: '',
                        class_id: '',
                        academic_year_id: '',
                        start_date: '',
                        end_date: '',
                        total_marks: '',
                        passing_marks: ''
                    },
                    createErrors: {},
                    createAttempted: false,

                    editErrors: {},
                    editAttempted: false,

                    showToast(type, message) {
                        this.toasts.push({
                            type,
                            message
                        });
                        setTimeout(() => {
                            this.toasts.shift();
                        }, 3000);
                    },

                    openCreateModal() {
                        this.createForm = {
                            name: '',
                            type: '',
                            class_id: '',
                            academic_year_id: '',
                            start_date: '',
                            end_date: '',
                            total_marks: '',
                            passing_marks: ''
                        };
                        this.createErrors = {};
                        this.createAttempted = false;
                        this.showCreateModal = true;
                    },

                    async openViewModal(id) {
                        this.showViewModal = true;
                        this.viewLoading = true;
                        this.viewData = null;
                        try {
                            const res = await fetch(`{{ url('admin/exams') }}/${id}`);
                            this.viewData = await res.json();
                        } catch (e) {
                            this.showViewModal = false;
                            this.showToast('error', 'তথ্য লোড করতে সমস্যা হয়েছে।');
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
                            const res = await fetch(`{{ url('admin/exams') }}/${id}/edit`);
                            this.editData = await res.json();
                        } catch (e) {
                            this.showEditModal = false;
                            this.showToast('error', 'তথ্য লোড করতে সমস্যা হয়েছে।');
                        } finally {
                            this.editLoading = false;
                        }
                    },

                    validateField(field, data, errors) {
                        delete errors[field];
                        const val = data[field];

                        const requiredFields = {
                            name: 'পরীক্ষার নাম',
                            type: 'পরীক্ষার ধরন',
                            class_id: 'শ্রেণি',
                            academic_year_id: 'শিক্ষাবর্ষ',
                            start_date: 'শুরুর তারিখ',
                            end_date: 'শেষ তারিখ',
                            total_marks: 'মোট নম্বর',
                            passing_marks: 'পাসের নম্বর',
                        };

                        if (requiredFields[field] && (!val || val.toString().trim() === '')) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
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

                    validateCreateForm(el) {
                        this.createAttempted = true;
                        this.createErrors = {};
                        let valid = true;
                        ['name', 'type', 'class_id', 'academic_year_id', 'start_date', 'end_date', 'total_marks', 'passing_marks'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['name', 'type', 'class_id', 'academic_year_id', 'start_date', 'end_date', 'total_marks', 'passing_marks'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection
