@extends('layouts.admin')

@section('title', 'পরিবহন ব্যবস্থাপনা')

@section('content')
    <div class="space-y-6" x-data="transportApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">পরিবহন ব্যবস্থাপনা</h1>
                <p class="text-sm text-gray-500 mt-1">বাস ও রুটের তথ্য পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন বাস
                </button>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="gradient-logo">
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বাস নং</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">রুট</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ড্রাইভার</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">চালকের ফোন</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">আসন সংখ্যা</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">নির্ধারিত ছাত্র</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($buses as $index => $bus)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button @click="openViewModal({{ $bus->id }})"
                                        class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $bus->bus_no }}</button>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $bus->route_name ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $bus->driver_name ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $bus->driver_phone ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $bus->capacity ?: '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ris-primary/10 text-ris-primary">
                                        {{ $bus->student_transports_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.transport.routes', $bus) }}" title="রুট ব্যবস্থাপনা"
                                            class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                            </svg>
                                        </a>
                                        <button @click="openViewModal({{ $bus->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $bus->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.transport.destroy', $bus) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই বাসটি মুছে ফেলতে চান?')">
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
                                            d="M8 7h8m0 0v8m0-8l-4 4m4-4l-4 4" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো বাস নেই</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন বাস যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.transport.store') }}" method="POST" class="p-6 space-y-5"
                    @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বাস নং <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="bus_no" x-model="createForm.bus_no"
                                @blur="validateCreateField('bus_no')" placeholder="যেমন: ঢাকা-গা-১২-৩৪৫৬"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.bus_no || (createAttempted && !createForm.bus_no)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.bus_no || (createAttempted && !createForm.bus_no)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.bus_no || 'বাস নম্বর আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ড্রাইভারের নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_name" x-model="createForm.driver_name"
                                @blur="validateCreateField('driver_name')" placeholder="ড্রাইভারের নাম"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.driver_name || (createAttempted && !createForm.driver_name)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.driver_name || (createAttempted && !createForm.driver_name)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.driver_name || 'চালকের নাম আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ড্রাইভারের মোবাইল <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_phone" x-model="createForm.driver_phone"
                                @blur="validateCreateField('driver_phone')" placeholder="01XXXXXXXXX"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.driver_phone || (createAttempted && !createForm.driver_phone)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.driver_phone || (createAttempted && !createForm.driver_phone)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.driver_phone || 'চালকের ফোন নম্বর আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">আসন সংখ্যা <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="capacity" x-model="createForm.capacity"
                                @blur="validateCreateField('capacity')" min="1" placeholder="যেমন: 40"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.capacity || (createAttempted && !createForm.capacity)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.capacity || (createAttempted && !createForm.capacity)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.capacity || 'আসন সংখ্যা আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">রুটের নাম</label>
                            <input type="text" name="route_name" x-model="createForm.route_name"
                                placeholder="যেমন: মিরপুর - স্কুল"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
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
                    <h3 class="font-heading font-bold text-white text-lg">বাস তথ্য</h3>
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
                                <div class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-semibold"
                                    x-text="viewData.bus_no?.charAt(0)"></div>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.bus_no"></h4>
                                    <p class="text-sm text-gray-500">বাস বিবরণ</p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">ড্রাইভারের নাম</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.driver_name || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">চালকের ফোন</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.driver_phone || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">আসন সংখ্যা</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.capacity || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">রুট</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.route_name || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">নির্ধারিত ছাত্র</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.student_transports_count ?? 0"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোট স্টপ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.bus_routes_count ?? 0"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">যোগের তারিখ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.created_at || '-'"></dd>
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
                    <h3 class="font-heading font-bold text-white text-lg">বাস সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/transport') }}/' + editData.id" method="POST"
                            class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বাস নম্বর <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="bus_no" x-model="editData.bus_no"
                                        @blur="validateEditField('bus_no')" placeholder="যেমন: ঢাকা-১২-৩৪৫৬"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.bus_no || (editAttempted && !editData.bus_no)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.bus_no || (editAttempted && !editData.bus_no)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.bus_no || 'বাস নম্বর আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">চালকের নাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="driver_name" x-model="editData.driver_name"
                                        @blur="validateEditField('driver_name')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.driver_name || (editAttempted && !editData.driver_name)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.driver_name || (editAttempted && !editData.driver_name)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.driver_name || 'চালকের নাম আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">চালকের ফোন <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="driver_phone" x-model="editData.driver_phone"
                                        @blur="validateEditField('driver_phone')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.driver_phone || (editAttempted && !editData.driver_phone)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.driver_phone || (editAttempted && !editData.driver_phone)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.driver_phone || 'চালকের ফোন নম্বর আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">আসন সংখ্যা <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="capacity" x-model="editData.capacity"
                                        @blur="validateEditField('capacity')" min="1"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.capacity || (editAttempted && !editData.capacity)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.capacity || (editAttempted && !editData.capacity)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.capacity || 'আসন সংখ্যা আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">রুট</label>
                                    <input type="text" name="route_name" x-model="editData.route_name"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
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
            function transportApp() {
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
                        bus_no: '',
                        driver_name: '',
                        driver_phone: '',
                        capacity: '',
                        route_name: ''
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
                            bus_no: '',
                            driver_name: '',
                            driver_phone: '',
                            capacity: '',
                            route_name: ''
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
                            const res = await fetch(`{{ url('admin/transport') }}/${id}`);
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
                            const res = await fetch(`{{ url('admin/transport') }}/${id}/edit`);
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
                            bus_no: 'বাস নম্বর',
                            driver_name: 'চালকের নাম',
                            driver_phone: 'চালকের ফোন নম্বর',
                            capacity: 'আসন সংখ্যা',
                        };

                        if (requiredFields[field] && (val === '' || val === null || val === undefined)) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
                            return false;
                        }

                        if (field === 'capacity' && val !== '' && val !== null && val !== undefined) {
                            const n = Number(val);
                            if (!Number.isInteger(n) || n < 1) {
                                errors[field] = 'আসন সংখ্যা কমপক্ষে ১ হতে হবে।';
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
                        ['bus_no', 'driver_name', 'driver_phone', 'capacity'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['bus_no', 'driver_name', 'driver_phone', 'capacity'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection