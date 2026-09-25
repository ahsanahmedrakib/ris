@extends('layouts.admin')

@section('title', 'ক্লাশ রুটিন')

@section('content')
    <div class="space-y-6" x-data="classRoutineApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">ক্লাশ রুটিন</h1>
                <p class="text-sm text-gray-500 mt-1">সকল শ্রেণির সময়সূচি পরিচালনা করুন</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.class-routines.index') }}">
                    <select name="class_id" onchange="this.form.submit()"
                        class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors cursor-pointer">
                        <option value="">সকল শ্রেণি</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected((int) $classId === (int) $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.class-routines.download', array_filter(['class_id' => $classId])) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel ডাউনলোড
                </a>
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন ক্লাশ রুটিন
                </button>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="gradient-logo">
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিষয়</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষক</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">দিন</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">সময়</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">কক্ষ</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" data-table-body>
                        @forelse($routines as $routine)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $routine->classRoom?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $routine->subject?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $routine->teacher?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $routine->dayEnum()?->color() ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $routine->dayLabel() ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $routine->start_time?->format('H:i') }} - {{ $routine->end_time?->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $routine->room_no ?? '-' }}</td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openViewModal({{ $routine->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $routine->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteId = {{ $routine->id }}; deleteUrl = '{{ route('admin.class-routines.destroy', $routine) }}'; showDeleteModal = true"
                                            class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
                                            title="মুছুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো ক্লাশ রুটিন পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন ক্লাশ রুটিন যোগ করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $routines])
        </div>

        {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">নতুন ক্লাশ রুটিন</h3>
                    <button @click="showCreateModal = false"
                        class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.class-routines.store') }}" method="POST" class="p-6 space-y-5"
                    @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <select name="class_id" x-model="createForm.class_id"
                                @change="validateCreateField('class_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.class_id ? 'border-red-400' : 'border-gray-200'">
                                <option value="">-- শ্রেণি নির্বাচন --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.class_id">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.class_id"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিষয় <span
                                    class="text-red-500">*</span></label>
                            <select name="subject_id" x-model="createForm.subject_id"
                                @change="validateCreateField('subject_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.subject_id ? 'border-red-400' : 'border-gray-200'">
                                <option value="">-- বিষয় নির্বাচন --</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.subject_id">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.subject_id"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষক <span
                                    class="text-red-500">*</span></label>
                            <select name="teacher_id" x-model="createForm.teacher_id"
                                @change="validateCreateField('teacher_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.teacher_id ? 'border-red-400' : 'border-gray-200'">
                                <option value="">-- শিক্ষক নির্বাচন --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.teacher_id">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.teacher_id"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">দিন <span
                                    class="text-red-500">*</span></label>
                            <select name="day_of_week" x-model="createForm.day_of_week"
                                @change="validateCreateField('day_of_week')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.day_of_week ? 'border-red-400' : 'border-gray-200'">
                                <option value="">-- দিন নির্বাচন --</option>
                                @foreach ($days as $day)
                                    <option value="{{ $day->value }}">{{ $day->label() }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.day_of_week">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.day_of_week"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শুরু সময় <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="start_time" x-model="createForm.start_time"
                                @blur="validateCreateField('start_time')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="createErrors.start_time ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.start_time">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.start_time"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শেষ সময় <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="end_time" x-model="createForm.end_time"
                                @blur="validateCreateField('end_time')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="createErrors.end_time ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.end_time">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.end_time"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">কক্ষ নং</label>
                            <input type="text" name="room_no" x-model="createForm.room_no"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false"
                            class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বাতিল</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">তৈরি
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
                    <h3 class="font-heading font-bold text-white text-lg">ক্লাশ রুটিনর তথ্য</h3>
                    <button @click="showViewModal = false"
                        class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="viewData">
                        <div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.class_name"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">বিষয়</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.subject_name"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শিক্ষক</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.teacher_name"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">দিন</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.day_label"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">সময়</dt>
                                    <dd class="font-medium text-gray-900"
                                        x-text="viewData.start_time + ' - ' + viewData.end_time"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">কক্ষ নং</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.room_no"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">তৈরির তারিখ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.created_at"></dd>
                                </div>
                            </dl>
                            <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end gap-3">
                                <button @click="showViewModal = false"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বন্ধ
                                    করুন</button>
                                <button @click="showViewModal = false; openEditModal(viewData.id)"
                                    class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">সম্পাদনা</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="viewLoading">
                        <x-skeleton.modal />
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
                    <h3 class="font-heading font-bold text-white text-lg">ক্লাশ রুটিন সম্পাদনা</h3>
                    <button @click="showEditModal = false"
                        class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/class-routines') }}/' + editData.id" method="POST"
                            class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                            class="text-red-500">*</span></label>
                                    <select name="class_id" x-model="editData.class_id"
                                        @change="validateEditField('class_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="editErrors.class_id ? 'border-red-400' : 'border-gray-200'">
                                        <option value="">-- শ্রেণি নির্বাচন --</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.class_id">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.class_id"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বিষয় <span
                                            class="text-red-500">*</span></label>
                                    <select name="subject_id" x-model="editData.subject_id"
                                        @change="validateEditField('subject_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="editErrors.subject_id ? 'border-red-400' : 'border-gray-200'">
                                        <option value="">-- বিষয় নির্বাচন --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.subject_id">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.subject_id"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষক <span
                                            class="text-red-500">*</span></label>
                                    <select name="teacher_id" x-model="editData.teacher_id"
                                        @change="validateEditField('teacher_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="editErrors.teacher_id ? 'border-red-400' : 'border-gray-200'">
                                        <option value="">-- শিক্ষক নির্বাচন --</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.teacher_id">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.teacher_id"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">দিন <span
                                            class="text-red-500">*</span></label>
                                    <select name="day_of_week" x-model="editData.day_of_week"
                                        @change="validateEditField('day_of_week')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="editErrors.day_of_week ? 'border-red-400' : 'border-gray-200'">
                                        <option value="">-- দিন নির্বাচন --</option>
                                        @foreach ($days as $day)
                                            <option value="{{ $day->value }}">{{ $day->label() }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.day_of_week">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.day_of_week"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শুরু সময় <span
                                            class="text-red-500">*</span></label>
                                    <input type="time" name="start_time" x-model="editData.start_time"
                                        @blur="validateEditField('start_time')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="editErrors.start_time ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.start_time">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.start_time"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শেষ সময় <span
                                            class="text-red-500">*</span></label>
                                    <input type="time" name="end_time" x-model="editData.end_time"
                                        @blur="validateEditField('end_time')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="editErrors.end_time ? 'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.end_time">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.end_time"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কক্ষ নং</label>
                                    <input type="text" name="room_no" x-model="editData.room_no"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="showEditModal = false"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বাতিল</button>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">আপডেট
                                    করুন</button>
                            </div>
                        </form>
                    </template>
                    <template x-if="editLoading">
                        <x-skeleton.modal />
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══════════════ DELETE MODAL ═══════════════ --}}
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showDeleteModal = false"
                    class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0 sm:scale-100"
                    x-transition:leave-end="translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="deleteUrl" method="POST" @submit="showDeleteModal = false">
                        @csrf
                        @method('DELETE')
                        <div class="bg-white px-6 pt-5 pb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-heading font-bold text-gray-900">মুছে ফেলুন</h3>
                                    <p class="text-sm text-gray-500 mt-1">আপনি কি নিশ্চিত এই ক্লাশ রুটিন মুছে ফেলতে চান? এই
                                        কাজটি পূর্বাবস্থায় ফেরানো যাবে না।</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showDeleteModal = false"
                                class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer">বাতিল</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm cursor-pointer">মুছুন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @section('scripts')
        <script>
            function classRoutineApp() {
                return {
                    showCreateModal: false,
                    showViewModal: false,
                    showEditModal: false,
                    showDeleteModal: false,
                    deleteId: null,
                    deleteUrl: '',
                    viewData: null,
                    editData: null,
                    viewLoading: false,
                    editLoading: false,

                    createForm: {
                        class_id: '',
                        subject_id: '',
                        teacher_id: '',
                        day_of_week: '',
                        start_time: '',
                        end_time: '',
                        room_no: ''
                    },
                    createErrors: {},
                    createAttempted: false,

                    editErrors: {},
                    editAttempted: false,

                    formSubmitting: false,

                    openCreateModal() {
                        this.createForm = {
                            class_id: '',
                            subject_id: '',
                            teacher_id: '',
                            day_of_week: '',
                            start_time: '',
                            end_time: '',
                            room_no: ''
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
                            const res = await fetch(`{{ url('admin/class-routines') }}/${id}/show`);
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
                            const res = await fetch(`{{ url('admin/class-routines') }}/${id}/edit`);
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

                        if (['class_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time'].includes(field)) {
                            if (!val || String(val).trim() === '') {
                                const msgs = {
                                    class_id: 'শ্রেণি নির্বাচন আবশ্যক।',
                                    subject_id: 'বিষয় নির্বাচন আবশ্যক।',
                                    teacher_id: 'শিক্ষক নির্বাচন আবশ্যক।',
                                    day_of_week: 'দিন নির্বাচন আবশ্যক।',
                                    start_time: 'শুরু সময় আবশ্যক।',
                                    end_time: 'শেষ সময় আবশ্যক।'
                                };
                                errors[field] = msgs[field];
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
                        ['class_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) this.submitForm(el, 'create');
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['class_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) this.submitForm(el, 'edit');
                    },

                    async submitForm(el, mode) {
                        if (this.formSubmitting) return;
                        this.formSubmitting = true;
                        try {
                            const { ok, status, data } = await RisAdmin.submitForm(el);
                            if (status === 422 && data.errors) {
                                if (mode === 'edit') this.editErrors = { ...this.editErrors, ...data.errors };
                                else this.createErrors = { ...this.createErrors, ...data.errors };
                                return;
                            }
                            if (!ok) {
                                RisAdmin.toast('error', data.message || 'সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                return;
                            }
                            if (mode === 'edit') this.showEditModal = false;
                            else this.showCreateModal = false;
                            RisAdmin.toast('success', data.message || 'সফলভাবে সংরক্ষণ হয়েছে।');
                            RisAdmin.refreshTable();
                        } catch (e) {
                            RisAdmin.toast('error', 'সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                        } finally {
                            this.formSubmitting = false;
                        }
                    }
                }
            }
        </script>
    @endsection
@endsection
