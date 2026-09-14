@extends('layouts.admin')

@section('title', 'শিক্ষক তালিকা')

@section('content')
    <div class="space-y-6" x-data="teacherApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">শিক্ষক</h1>
                <p class="text-sm text-gray-500 mt-1">সকল শিক্ষক পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.teachers.download', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel ডাউনলোড
                </a>
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন শিক্ষক
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.teachers.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="নাম, ইমেইল, বিষয় দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.teachers.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">নাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পদবি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিষয়</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষাগত যোগ্যতা</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষা প্রতিষ্ঠান</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">যোগদান</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($teachers as $index => $teacher)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($teachers->currentPage() - 1) * $teachers->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if ($teacher->teacherProfile?->photo)
                                            <img src="{{ Storage::url($teacher->teacherProfile->photo) }}"
                                                alt="{{ $teacher->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                                {{ mb_substr($teacher->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <button @click="openViewModal({{ $teacher->id }})"
                                            class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $teacher->name }}</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $teacher->teacherProfile?->designation ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $teacher->teacherProfile?->subject ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $teacher->teacherProfile?->qualification ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $teacher->teacherProfile?->institute ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $teacher->teacherProfile?->joining_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($teacher->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">সক্রিয়</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">ডিলিট</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openViewModal({{ $teacher->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $teacher->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই শিক্ষককে ডিলিট করতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
                                                title="ডিলিট করুন">
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
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো শিক্ষক পাওয়া যায়নি</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $teachers])
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন শিক্ষক যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="createForm.name"
                                @blur="validateCreateField('name')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.name || (createAttempted && !createForm.name)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.name || (createAttempted && !createForm.name)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.name || 'নাম আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" x-model="createForm.email"
                                @blur="validateCreateField('email')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.email || (createAttempted && !createForm.email)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.email || (createAttempted && !createForm.email)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.email || 'ইমেইল আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ফোন <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" name="phone" x-model="createForm.phone"
                                @blur="validateCreateField('phone')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.phone || (createAttempted && !createForm.phone)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.phone || (createAttempted && !createForm.phone)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.phone || 'ফোন নম্বর আবশ্যক'">
                                </p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ছবি <span
                                    class="text-red-500">*</span></label>
                            <div class="flex items-center gap-4">
                                <div class="shrink-0">
                                    <img x-show="createPhotoPreview" :src="createPhotoPreview"
                                        class="w-20 h-20 rounded-full object-cover">
                                    <div x-show="!createPhotoPreview"
                                        class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <input type="file" name="photo" accept="image/*"
                                    @change="createPhotoPreview = URL.createObjectURL($event.target.files[0])"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পদবি <span
                                    class="text-red-500">*</span></label>
                            <select name="designation"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach ($designations as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিষয় <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="subject" x-model="createForm.subject"
                                @blur="validateCreateField('subject')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.subject || (createAttempted && !createForm.subject)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.subject || (createAttempted && !createForm.subject)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.subject || 'বিষয় আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">সর্বোচ্চ শিক্ষাগত যোগ্যতা <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="qualification" x-model="createForm.qualification"
                                @blur="validateCreateField('qualification')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.qualification || (createAttempted && !createForm.qualification)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.qualification || (createAttempted && !createForm.qualification)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.qualification || 'যোগ্যতা আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষা প্রতিষ্ঠান <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="institute" x-model="createForm.institute"
                                @blur="validateCreateField('institute')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.institute || (createAttempted && !createForm.institute)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.institute || (createAttempted && !createForm.institute)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.institute || 'প্রতিষ্ঠান আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">যোগদানের তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="joining_date" x-model="createForm.joining_date"
                                @blur="validateCreateField('joining_date')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.joining_date || (createAttempted && !createForm.joining_date)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.joining_date || (createAttempted && !createForm.joining_date)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.joining_date || 'যোগদানের তারিখ আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ববর্তী প্রতিষ্ঠান</label>
                            <textarea name="previous_institutions" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">পরিচিতি</label>
                            <textarea name="bio" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">অভিজ্ঞতা</label>
                            <textarea name="experience" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">অর্জন</label>
                            <textarea name="achievements" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false"
                            class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">তৈরি
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
                    <h3 class="font-heading font-bold text-white text-lg">শিক্ষক তথ্য</h3>
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
                            <div class="flex items-center gap-4 mb-5">
                                <template x-if="viewData.photo">
                                    <img :src="viewData.photo" class="w-20 h-20 rounded-full object-cover">
                                </template>
                                <template x-if="!viewData.photo">
                                    <div class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-semibold"
                                        x-text="viewData.name?.charAt(0)"></div>
                                </template>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="viewData.designation || 'শিক্ষক'"></p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">ইমেইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.email"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">ফোন</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.phone || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">বিষয়</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.subject || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">সর্বোচ্চ শিক্ষাগত যোগ্যতা</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.qualification || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শিক্ষা প্রতিষ্ঠান</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.institute || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">যোগদান</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.joining_date || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                                    <dd><span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="viewData.is_active ? 'bg-emerald-100 text-emerald-800' :
                                                'bg-red-100 text-red-800'"
                                            x-text="viewData.is_active ? 'সক্রিয়' : 'ডিলিট'"></span></dd>
                                </div>
                            </dl>
                            <template x-if="viewData.bio">
                                <div class="mt-5 pt-5 border-t border-gray-100">
                                    <dt class="text-gray-500 mb-2 text-sm">পরিচিতি</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap" x-text="viewData.bio"></dd>
                                </div>
                            </template>
                            <template x-if="viewData.previous_institutions">
                                <div class="mt-4">
                                    <dt class="text-gray-500 mb-2 text-sm">পূর্ববর্তী প্রতিষ্ঠান</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap"
                                        x-text="viewData.previous_institutions"></dd>
                                </div>
                            </template>
                            <template x-if="viewData.experience">
                                <div class="mt-4">
                                    <dt class="text-gray-500 mb-2 text-sm">অভিজ্ঞতা</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap" x-text="viewData.experience">
                                    </dd>
                                </div>
                            </template>
                            <template x-if="viewData.achievements">
                                <div class="mt-4">
                                    <dt class="text-gray-500 mb-2 text-sm">অর্জন</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap" x-text="viewData.achievements">
                                    </dd>
                                </div>
                            </template>
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
                    <h3 class="font-heading font-bold text-white text-lg">শিক্ষক সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/teachers') }}/' + editData.id" method="POST"
                            enctype="multipart/form-data" class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editData.name"
                                        @blur="validateEditField('name')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.name || (editAttempted && !editData.name)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.name || (editAttempted && !editData.name)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.name || 'নাম আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="editData.email"
                                        @blur="validateEditField('email')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.email || (editAttempted && !editData.email)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.email || (editAttempted && !editData.email)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.email || 'ইমেইল আবশ্যক'">
                                        </p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ফোন <span
                                            class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" x-model="editData.phone"
                                        @blur="validateEditField('phone')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.phone || (editAttempted && !editData.phone)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.phone || (editAttempted && !editData.phone)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.phone || 'ফোন নম্বর আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <img x-show="editPhotoPreview" :src="editPhotoPreview"
                                                class="w-20 h-20 rounded-full object-cover">
                                            <img x-show="!editPhotoPreview && editData.photo" :src="editData.photo"
                                                class="w-20 h-20 rounded-full object-cover">
                                            <div x-show="!editPhotoPreview && !editData.photo"
                                                class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="photo" accept="image/*"
                                                @change="editPhotoPreview = URL.createObjectURL($event.target.files[0])"
                                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                            <p x-show="editData.photo && !editPhotoPreview"
                                                class="mt-1 text-xs text-gray-500">বর্তমান ছবি আছে। নতুন দিলে পুরোনো মুছে
                                                যাবে।</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পদবি <span
                                            class="text-red-500">*</span></label>
                                    <select name="designation"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="">-- নির্বাচন করুন --</option>
                                        @foreach ($designations as $value)
                                            <option :value="'{{ $value }}'" x-text="'{{ $value }}'"
                                                :selected="editData.designation === '{{ $value }}'"></option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বিষয় <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="subject" x-model="editData.subject"
                                        @blur="validateEditField('subject')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.subject || (editAttempted && !editData.subject)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.subject || (editAttempted && !editData.subject)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.subject || 'বিষয় আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">সর্বোচ্চ শিক্ষাগত যোগ্যতা
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="qualification" x-model="editData.qualification"
                                        @blur="validateEditField('qualification')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.qualification || (editAttempted && !editData.qualification)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.qualification || (editAttempted && !editData.qualification)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.qualification || 'যোগ্যতা আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষা প্রতিষ্ঠান <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="institute" x-model="editData.institute"
                                        @blur="validateEditField('institute')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.institute || (editAttempted && !editData.institute)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.institute || (editAttempted && !editData.institute)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.institute || 'প্রতিষ্ঠান আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">যোগদানের তারিখ <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="joining_date" x-model="editData.joining_date"
                                        @blur="validateEditField('joining_date')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.joining_date || (editAttempted && !editData.joining_date)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.joining_date || (editAttempted && !editData.joining_date)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.joining_date || 'যোগদানের তারিখ আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস</label>
                                    <select name="is_active"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="1">সক্রিয়</option>
                                        <option value="0">ডিলিট</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ববর্তী
                                        প্রতিষ্ঠান</label>
                                    <textarea name="previous_institutions" rows="2" x-model="editData.previous_institutions"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পরিচিতি</label>
                                    <textarea name="bio" rows="2" x-model="editData.bio"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অভিজ্ঞতা</label>
                                    <textarea name="experience" rows="2" x-model="editData.experience"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অর্জন</label>
                                    <textarea name="achievements" rows="2" x-model="editData.achievements"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"></textarea>
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
            function teacherApp() {
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
                        email: '',
                        phone: '',
                        subject: '',
                        qualification: '',
                        institute: '',
                        joining_date: ''
                    },
                    createPhotoPreview: '',
                    createErrors: {},
                    createAttempted: false,

                    editPhotoPreview: '',
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
                            email: '',
                            phone: '',
                            subject: '',
                            qualification: '',
                            institute: '',
                            joining_date: ''
                        };
                        this.createPhotoPreview = '';
                        this.createErrors = {};
                        this.createAttempted = false;
                        this.showCreateModal = true;
                    },

                    async openViewModal(id) {
                        this.showViewModal = true;
                        this.viewLoading = true;
                        this.viewData = null;
                        try {
                            const res = await fetch(`{{ url('admin/teachers') }}/${id}`);
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
                        this.editPhotoPreview = '';
                        this.editErrors = {};
                        this.editAttempted = false;
                        try {
                            const res = await fetch(`{{ url('admin/teachers') }}/${id}/edit`);
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
                            name: 'নাম',
                            email: 'ইমেইল',
                            phone: 'ফোন নম্বর',
                            subject: 'বিষয়',
                            qualification: 'যোগ্যতা',
                            institute: 'প্রতিষ্ঠান',
                            joining_date: 'যোগদানের তারিখ',
                        };

                        if (requiredFields[field] && (!val || val.trim() === '')) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
                            return false;
                        }

                        if (field === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
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

                    validateCreateForm(el) {
                        this.createAttempted = true;
                        this.createErrors = {};
                        let valid = true;
                        ['name', 'email', 'phone', 'subject', 'qualification', 'institute', 'joining_date'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['name', 'email', 'phone', 'subject', 'qualification', 'institute', 'joining_date'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection
