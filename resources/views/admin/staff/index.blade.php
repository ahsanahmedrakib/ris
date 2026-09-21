@extends('layouts.admin')

@section('title', 'কর্মচারী তালিকা')

@section('content')
    <div class="space-y-6" x-data="staffApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">কর্মচারী</h1>
                <p class="text-sm text-gray-500 mt-1">সকল কর্মচারীদের পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন কর্মচারী
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.staff.index') }}">
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
                                placeholder="নাম, ইমেইল, আইডি, পদবি দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.staff.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">আইডি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">নাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পদবি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিভাগ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মোবাইল</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">যোগদান</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($staff as $index => $member)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/staff') }}', {{ $member->id }}, {{ ($member->user?->is_active ?? false) ? 'true' : 'false' }})">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($staff->currentPage() - 1) * $staff->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $member->employee_id }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                            {{ mb_substr($member->user?->name ?? '?', 0, 1) }}
                                        </div>
                                        <button @click="openViewModal({{ $member->id }})"
                                            class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $member->user?->name ?? '-' }}</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $member->designation ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $member->department ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $member->user?->phone ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $member->joining_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                        x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ ($member->user?->is_active ?? false) ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button x-show="!active" @click="toggle()" title="সক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button x-show="active" @click="toggle()" title="নিষ্ক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openViewModal({{ $member->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $member->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.staff.destroy', $member) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই কর্মচারীকে ডিলিট করতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
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
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো কর্মচারী পাওয়া যায়নি</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $staff])
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
                    <button @click="toasts.splice(index, 1)" class="ml-auto shrink-0 opacity-60 hover:opacity-100 cursor-pointer">
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন কর্মচারী যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.staff.store') }}" method="POST"
                    class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">কর্মচারী আইডি <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="employee_id" x-model="createForm.employee_id"
                                @blur="validateCreateField('employee_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.employee_id || (createAttempted && !createForm.employee_id)) ? 'border-red-400' :
                                'border-gray-200'"
                                placeholder="যেমন: STF-001">
                            <template x-if="createErrors.employee_id || (createAttempted && !createForm.employee_id)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.employee_id || 'কর্মচারী আইডি আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ণ নাম <span
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল</label>
                            <input type="tel" name="phone" x-model="createForm.phone"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="01XXXXXXXXX">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পদবি <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="designation" x-model="createForm.designation"
                                @blur="validateCreateField('designation')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.designation || (createAttempted && !createForm.designation)) ? 'border-red-400' :
                                'border-gray-200'"
                                placeholder="যেমন: অফিস সহকারী">
                            <template x-if="createErrors.designation || (createAttempted && !createForm.designation)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.designation || 'পদবি আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিভাগ</label>
                            <select name="department"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">-- নির্বাচন করুন --</option>
                                <option value="admin">প্রশাসন</option>
                                <option value="academic">একাডেমিক</option>
                                <option value="finance">আর্থিক</option>
                                <option value="transport">পরিবহন</option>
                                <option value="library">লাইব্রেরি</option>
                                <option value="maintenance">রক্ষণাবেক্ষণ</option>
                            </select>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">মাসিক বেতন ৳ <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="salary" x-model="createForm.salary" min="0" step="0.01"
                                @blur="validateCreateField('salary')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.salary || (createAttempted && !createForm.salary)) ?
                                'border-red-400' : 'border-gray-200'"
                                placeholder="মাসিক বেতন">
                            <template x-if="createErrors.salary || (createAttempted && !createForm.salary)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.salary || 'বেতন আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাগত যোগ্যতা</label>
                            <input type="text" name="qualification" x-model="createForm.qualification"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false"
                            class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বাতিল</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">সংরক্ষণ
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
                    <h3 class="font-heading font-bold text-white text-lg">কর্মচারী তথ্য</h3>
                    <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
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
                                <div class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-semibold"
                                    x-text="viewData.name?.charAt(0)"></div>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="viewData.designation || 'কর্মচারী'"></p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">কর্মচারী আইডি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.employee_id"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">ইমেইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.email"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোবাইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.phone || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">বিভাগ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.department || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">যোগদান</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.joining_date || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মাসিক বেতন</dt>
                                    <dd class="font-medium text-gray-900" x-text="'৳' + viewData.salary"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">শিক্ষাগত যোগ্যতা</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.qualification || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                                    <dd><span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="viewData.is_active ? 'bg-emerald-100 text-emerald-800' :
                                                'bg-red-100 text-red-800'"
                                            x-text="viewData.is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়'"></span></dd>
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
                    <h3 class="font-heading font-bold text-white text-lg">কর্মচারী সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/staff') }}/' + editData.id" method="POST"
                            class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">কর্মচারী আইডি <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="employee_id" x-model="editData.employee_id"
                                        @blur="validateEditField('employee_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.employee_id || (editAttempted && !editData.employee_id)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.employee_id || (editAttempted && !editData.employee_id)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.employee_id || 'কর্মচারী আইডি আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ণ নাম <span
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
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.email || 'ইমেইল আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল</label>
                                    <input type="tel" name="phone" x-model="editData.phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পদবি <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="designation" x-model="editData.designation"
                                        @blur="validateEditField('designation')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.designation || (editAttempted && !editData.designation)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.designation || (editAttempted && !editData.designation)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.designation || 'পদবি আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বিভাগ</label>
                                    <select name="department"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="">-- নির্বাচন করুন --</option>
                                        <option value="admin" :selected="editData.department === 'admin'">প্রশাসন</option>
                                        <option value="academic" :selected="editData.department === 'academic'">একাডেমিক</option>
                                        <option value="finance" :selected="editData.department === 'finance'">আর্থিক</option>
                                        <option value="transport" :selected="editData.department === 'transport'">পরিবহন</option>
                                        <option value="library" :selected="editData.department === 'library'">লাইব্রেরি</option>
                                        <option value="maintenance" :selected="editData.department === 'maintenance'">রক্ষণাবেক্ষণ</option>
                                    </select>
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মাসিক বেতন ৳ <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="salary" x-model="editData.salary" min="0" step="0.01"
                                        @blur="validateEditField('salary')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.salary || (editAttempted && !editData.salary)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.salary || (editAttempted && !editData.salary)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.salary || 'বেতন আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাগত যোগ্যতা</label>
                                    <input type="text" name="qualification" x-model="editData.qualification"
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
            function activeRow(baseUrl, id, active) {
                return {
                    active,
                    busy: false,
                    async toggle() {
                        if (this.busy) return;
                        this.busy = true;
                        try {
                            const res = await fetch(`${baseUrl}/${id}/toggle-active`, {
                                method: 'PATCH',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await res.json();
                            if (res.ok) {
                                this.active = data.is_active;
                            } else {
                                alert(data.message || 'স্ট্যাটাস পরিবর্তন করা যায়নি।');
                            }
                        } catch (e) {
                            alert('স্ট্যাটাস পরিবর্তন করা যায়নি।');
                        } finally {
                            this.busy = false;
                        }
                    }
                }
            }

            function staffApp() {
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
                        employee_id: '',
                        name: '',
                        email: '',
                        phone: '',
                        designation: '',
                        department: '',
                        joining_date: '',
                        salary: '',
                        qualification: ''
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
                            employee_id: '',
                            name: '',
                            email: '',
                            phone: '',
                            designation: '',
                            department: '',
                            joining_date: '',
                            salary: '',
                            qualification: ''
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
                            const res = await fetch(`{{ url('admin/staff') }}/${id}`);
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
                            const res = await fetch(`{{ url('admin/staff') }}/${id}/edit`);
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
                            employee_id: 'কর্মচারী আইডি',
                            name: 'নাম',
                            email: 'ইমেইল',
                            designation: 'পদবি',
                            joining_date: 'যোগদানের তারিখ',
                            salary: 'বেতন'
                        };

                        if (requiredFields[field] && (!val || val.toString().trim() === '')) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
                            return false;
                        }

                        if (field === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                            errors[field] = 'সঠিক ইমেইল দিন।';
                            return false;
                        }

                        if (field === 'salary' && val !== '' && val !== null) {
                            const num = parseFloat(val);
                            if (isNaN(num)) {
                                errors[field] = 'বেতন অবশ্যই একটি সংখ্যা হতে হবে।';
                                return false;
                            }
                            if (num < 0) {
                                errors[field] = 'বেতন ০ এর বেশি হতে হবে।';
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
                        ['employee_id', 'name', 'email', 'designation', 'joining_date', 'salary'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['employee_id', 'name', 'email', 'designation', 'joining_date', 'salary'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection