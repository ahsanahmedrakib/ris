@extends('layouts.admin')

@section('title', 'ফি কাঠামো')

@section('content')
    <div class="space-y-6" x-data="feeStructuresApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">ফি কাঠামো</h1>
                <p class="text-sm text-gray-500 mt-1">সকল ফি কাঠামোর তালিকা</p>
            </div>
            <button @click="openCreateModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                নতুন ফি কাঠামো
            </button>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="gradient-logo">
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিক্ষাবর্ষ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ধরন</th>
                            <th class="text-right px-4 py-3.5 font-medium text-white whitespace-nowrap">পরিমাণ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শেষ তারিখ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white">বিবরণ</th>
                            <th
                                class="text-right px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($structures as $index => $structure)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $structure->classRoom->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $structure->academicYear?->yearLabel() ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $feeType = \App\Enums\FeeType::tryFrom($structure->fee_type);
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $feeType ? $feeType->color() : 'bg-gray-100 text-gray-700' }}">
                                        {{ $feeType?->label() ?? $structure->fee_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-heading font-bold text-ris-primary whitespace-nowrap">
                                    ৳{{ number_format($structure->amount, 2) }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $structure->due_date?->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 max-w-40 truncate"
                                    title="{{ $structure->description ?? '-' }}">
                                    {{ $structure->description ?? '-' }}</td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openEditModal({{ $structure->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST"
                                            action="{{ route('admin.fees.structures.destroy', $structure) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই ফি কাঠামোটি মুছে ফেলতে চান?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
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
                                <td colspan="8" class="px-5 py-14 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো ফি কাঠামো নেই</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন ফি কাঠামো যোগ করুন</p>
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন ফি কাঠামো যোগ করুন</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.fees.structures.store') }}" method="POST" class="p-6 space-y-5"
                    @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                    class="text-red-500">*</span></label>
                            <select name="class_id" x-model="createForm.class_id"
                                @blur="validateCreateField('class_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.class_id || (createAttempted && !createForm.class_id)) ?
                                'border-red-400' : 'border-gray-200'">
                                <option value="">-- নির্বাচন করুন --</option>
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
                            <select name="academic_year_id" x-model="createForm.academic_year_id"
                                @blur="validateCreateField('academic_year_id')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.academic_year_id || (createAttempted && !createForm.academic_year_id)) ?
                                'border-red-400' : 'border-gray-200'">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->yearLabel() }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.academic_year_id || (createAttempted && !createForm.academic_year_id)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.academic_year_id || 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ফি-এর ধরন <span
                                    class="text-red-500">*</span></label>
                            <select name="fee_type" x-model="createForm.fee_type"
                                @blur="validateCreateField('fee_type')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.fee_type || (createAttempted && !createForm.fee_type)) ?
                                'border-red-400' : 'border-gray-200'">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach ($feeTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            <template x-if="createErrors.fee_type || (createAttempted && !createForm.fee_type)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.fee_type || 'ফি-এর ধরন নির্বাচন আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পরিমাণ (৳) <span
                                    class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount" x-model="createForm.amount"
                                @blur="validateCreateField('amount')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.amount || (createAttempted && !createForm.amount)) ? 'border-red-400' :
                                'border-gray-200'"
                                placeholder="যেমন: 2000">
                            <template x-if="createErrors.amount || (createAttempted && !createForm.amount)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.amount || 'পরিমাণ আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শেষ তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="due_date" x-model="createForm.due_date"
                                @blur="validateCreateField('due_date')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.due_date || (createAttempted && !createForm.due_date)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.due_date || (createAttempted && !createForm.due_date)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.due_date || 'শেষ তারিখ আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ</label>
                            <textarea name="description" x-model="createForm.description" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: মাসিক বেতন"></textarea>
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

        {{-- ═══════════════ EDIT MODAL ═══════════════ --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showEditModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">ফি কাঠামো সম্পাদনা করুন</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
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
                <template x-if="editData">
                    <form :action="`{{ url('admin/fees/structures') }}/${editData.id}`" method="POST" class="p-6 space-y-5"
                        @submit.prevent="validateEditForm($el)">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                        class="text-red-500">*</span></label>
                                <select name="class_id" x-model="editData.class_id"
                                    @blur="validateEditField('class_id')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                    :class="(editErrors.class_id || (editAttempted && !editData.class_id)) ?
                                    'border-red-400' : 'border-gray-200'">
                                    <option value="">-- নির্বাচন করুন --</option>
                                    @foreach ($classes as $class)
                                        <option :value="'{{ $class->id }}'" x-text="'{{ $class->name }}'"
                                            :selected="editData.class_id == '{{ $class->id }}'"></option>
                                    @endforeach
                                </select>
                                <template x-if="editErrors.class_id || (editAttempted && !editData.class_id)">
                                    <p class="mt-1 text-xs text-red-600" x-text="editErrors.class_id || 'শ্রেণি নির্বাচন আবশ্যক।'"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাবর্ষ <span
                                        class="text-red-500">*</span></label>
                                <select name="academic_year_id" x-model="editData.academic_year_id"
                                    @blur="validateEditField('academic_year_id')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                    :class="(editErrors.academic_year_id || (editAttempted && !editData.academic_year_id)) ?
                                    'border-red-400' : 'border-gray-200'">
                                    <option value="">-- নির্বাচন করুন --</option>
                                    @foreach ($academicYears as $year)
                                        <option :value="'{{ $year->id }}'" x-text="'{{ $year->yearLabel() }}'"
                                            :selected="editData.academic_year_id == '{{ $year->id }}'"></option>
                                    @endforeach
                                </select>
                                <template x-if="editErrors.academic_year_id || (editAttempted && !editData.academic_year_id)">
                                    <p class="mt-1 text-xs text-red-600"
                                        x-text="editErrors.academic_year_id || 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।'"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ফি-এর ধরন <span
                                        class="text-red-500">*</span></label>
                                <select name="fee_type" x-model="editData.fee_type"
                                    @blur="validateEditField('fee_type')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                    :class="(editErrors.fee_type || (editAttempted && !editData.fee_type)) ?
                                    'border-red-400' : 'border-gray-200'">
                                    <option value="">-- নির্বাচন করুন --</option>
                                    @foreach ($feeTypes as $type)
                                        <option :value="'{{ $type->value }}'" x-text="'{{ $type->label() }}'"
                                            :selected="editData.fee_type === '{{ $type->value }}'"></option>
                                    @endforeach
                                </select>
                                <template x-if="editErrors.fee_type || (editAttempted && !editData.fee_type)">
                                    <p class="mt-1 text-xs text-red-600" x-text="editErrors.fee_type || 'ফি-এর ধরন নির্বাচন আবশ্যক।'"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">পরিমাণ (৳) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" name="amount" x-model="editData.amount"
                                    @blur="validateEditField('amount')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                    :class="(editErrors.amount || (editAttempted && !editData.amount && editData.amount !== 0)) ? 'border-red-400' :
                                    'border-gray-200'">
                                <template x-if="editErrors.amount || (editAttempted && !editData.amount && editData.amount !== 0)">
                                    <p class="mt-1 text-xs text-red-600" x-text="editErrors.amount || 'পরিমাণ আবশ্যক।'"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">শেষ তারিখ <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="due_date" x-model="editData.due_date"
                                    @blur="validateEditField('due_date')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                    :class="(editErrors.due_date || (editAttempted && !editData.due_date)) ? 'border-red-400' :
                                    'border-gray-200'">
                                <template x-if="editErrors.due_date || (editAttempted && !editData.due_date)">
                                    <p class="mt-1 text-xs text-red-600" x-text="editErrors.due_date || 'শেষ তারিখ আবশ্যক।'"></p>
                                </template>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ</label>
                                <textarea name="description" x-model="editData.description" rows="2"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                    placeholder="যেমন: মাসিক বেতন"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showEditModal = false"
                                class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বাতিল</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">হালনাগাদ
                                করুন</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
        <script>
            function feeStructuresApp() {
                return {
                    showCreateModal: false,
                    showEditModal: false,
                    editData: null,
                    editLoading: false,
                    toasts: [],

                    createForm: {
                        class_id: '',
                        academic_year_id: '',
                        fee_type: '',
                        amount: '',
                        due_date: '',
                        description: ''
                    },
                    createErrors: {},
                    createAttempted: false,

                    editErrors: {},
                    editAttempted: false,

                    showToast(type, message) {
                        this.toasts.push({ type, message });
                        setTimeout(() => {
                            this.toasts.shift();
                        }, 3000);
                    },

                    openCreateModal() {
                        this.createForm = {
                            class_id: '',
                            academic_year_id: '',
                            fee_type: '',
                            amount: '',
                            due_date: '',
                            description: ''
                        };
                        this.createErrors = {};
                        this.createAttempted = false;
                        this.showCreateModal = true;
                    },

                    async openEditModal(id) {
                        this.showEditModal = true;
                        this.editLoading = true;
                        this.editData = null;
                        this.editErrors = {};
                        this.editAttempted = false;
                        try {
                            const res = await fetch(`{{ url('admin/fees/structures') }}/${id}/edit`);
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

                        if (['class_id', 'academic_year_id', 'fee_type'].includes(field) && (!val || val === '')) {
                            const messages = {
                                class_id: 'শ্রেণি নির্বাচন আবশ্যক।',
                                academic_year_id: 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
                                fee_type: 'ফি-এর ধরন নির্বাচন আবশ্যক।'
                            };
                            errors[field] = messages[field];
                            return false;
                        }

                        if (field === 'amount' && (val === '' || val === null || parseFloat(val) <= 0)) {
                            errors[field] = 'সঠিক পরিমাণ দিন।';
                            return false;
                        }

                        if (field === 'due_date' && (!val || val === '')) {
                            errors[field] = 'শেষ তারিখ আবশ্যক।';
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
                        ['class_id', 'academic_year_id', 'fee_type', 'amount', 'due_date'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['class_id', 'academic_year_id', 'fee_type', 'amount', 'due_date'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                };
            }
        </script>
    @endsection