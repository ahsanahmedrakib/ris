@extends('layouts.admin')

@section('title', 'FAQ তালিকা')

@section('content')
    <div class="space-y-6" x-data="faqApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">প্রশ্নোত্তর (FAQ)</h1>
                <p class="text-sm text-gray-500 mt-1">সাধারণ জিজ্ঞাসা ও উত্তর পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন প্রশ্ন
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm font-medium">
                {{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.faqs.index') }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="প্রশ্ন বা উত্তর দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.faqs.index') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">প্রশ্ন</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">উত্তর</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রম</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" data-table-body>
                        @forelse($faqs as $faq)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/faqs') }}', {{ $faq->id }}, {{ $faq->is_active ? 'true' : 'false' }})">
                                <td class="px-4 py-3 max-w-72">
                                    <button @click="openViewModal({{ $faq->id }})"
                                        class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer text-left">{{ $faq->question }}</button>
                                </td>
                                <td class="px-4 py-3 text-gray-500 max-w-80 truncate">{{ $faq->answer }}</td>
                                <td class="px-4 py-3 text-gray-500 text-center">{{ $faq->sort_order }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-700' :
                                        'bg-gray-100 text-gray-600'"
                                        x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ $faq->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button x-show="!active" @click="toggle()" title="সক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button x-show="active" @click="toggle()" title="নিষ্ক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openViewModal({{ $faq->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $faq->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই প্রশ্নোত্তরটি মুছে ফেলতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
                                                title="মুছুন">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো প্রশ্নোত্তর পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন প্রশ্ন যোগ করুন বা অনুসন্ধান পরিবর্তন করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $faqs])
        </div>

        {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">নতুন প্রশ্নোত্তর</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.faqs.store') }}" method="POST"
                    class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">প্রশ্ন <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="question" x-model="createForm.question"
                                @blur="validateCreateField('question')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.question || (createAttempted && !createForm.question)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.question || (createAttempted && !createForm.question)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.question || 'প্রশ্ন আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">উত্তর <span
                                    class="text-red-500">*</span></label>
                            <textarea name="answer" rows="4" x-model="createForm.answer"
                                @blur="validateCreateField('answer')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                :class="(createErrors.answer || (createAttempted && !createForm.answer)) ? 'border-red-400' :
                                'border-gray-200'"></textarea>
                            <template x-if="createErrors.answer || (createAttempted && !createForm.answer)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.answer || 'উত্তর আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">সাজানোর ক্রম</label>
                            <input type="number" name="sort_order" x-model="createForm.sort_order"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer mt-6">
                                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary"
                                    x-model="createForm.is_active">
                                সক্রিয়
                            </label>
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
                    <h3 class="font-heading font-bold text-white text-lg">প্রশ্নোত্তরের তথ্য</h3>
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
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500 mb-1">প্রশ্ন</dt>
                                    <dd class="font-heading font-semibold text-gray-900" x-text="viewData.question"></dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500 mb-1">উত্তর</dt>
                                    <dd class="font-medium text-gray-900 leading-relaxed" x-text="viewData.answer"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                                    <dd>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="viewData.is_active ? 'bg-emerald-100 text-emerald-700' :
                                            'bg-gray-100 text-gray-600'" x-text="viewData.status_label"></span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">তৈরির সময়</dt>
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
                        <div class="py-12 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
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
                    <h3 class="font-heading font-bold text-white text-lg">প্রশ্নোত্তর সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/faqs') }}/' + editData.id" method="POST"
                            class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">প্রশ্ন <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="question" x-model="editData.question"
                                        @blur="validateEditField('question')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.question || (editAttempted && !editData.question)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.question || (editAttempted && !editData.question)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.question || 'প্রশ্ন আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">উত্তর <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="answer" rows="4" x-model="editData.answer"
                                        @blur="validateEditField('answer')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                        :class="(editErrors.answer || (editAttempted && !editData.answer)) ? 'border-red-400' :
                                        'border-gray-200'"></textarea>
                                    <template x-if="editErrors.answer || (editAttempted && !editData.answer)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.answer || 'উত্তর আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">সাজানোর ক্রম</label>
                                    <input type="number" name="sort_order" x-model="editData.sort_order"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="0">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1"
                                            class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary"
                                            x-model="editData.is_active">
                                        সক্রিয়
                                    </label>
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
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
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

            function faqApp() {
                return {
                    showCreateModal: false,
                    showViewModal: false,
                    showEditModal: false,
                    viewData: null,
                    editData: null,
                    viewLoading: false,
                    editLoading: false,

                    formSubmitting: false,

                    createForm: {
                        question: '',
                        answer: '',
                        sort_order: '0',
                        is_active: true
                    },
                    createErrors: {},
                    createAttempted: false,

                    editErrors: {},
                    editAttempted: false,

                    openCreateModal() {
                        this.createForm = {
                            question: '',
                            answer: '',
                            sort_order: '0',
                            is_active: true
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
                            const res = await fetch(`{{ url('admin/faqs') }}/${id}`);
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
                            const res = await fetch(`{{ url('admin/faqs') }}/${id}/edit`);
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

                        if (['question', 'answer'].includes(field)) {
                            if (!val || val.trim() === '') {
                                const msgs = {
                                    question: 'প্রশ্ন আবশ্যক।',
                                    answer: 'উত্তর আবশ্যক।'
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
                        ['question', 'answer'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) this.submitForm(el, 'create');
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['question', 'answer'].forEach(f => {
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