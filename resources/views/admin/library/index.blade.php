@extends('layouts.admin')

@section('title', 'লাইব্রেরি ব্যবস্থাপনা')

@section('content')
    <div class="space-y-6" x-data="libraryApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">লাইব্রেরি ব্যবস্থাপনা</h1>
                <p class="text-sm text-gray-500 mt-1">সকল বইয়ের তালিকা ও ব্যবস্থাপনা</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.library.borrowings') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    ধার তালিকা
                </a>
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন বই
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.library.index') }}">
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
                                placeholder="বইয়ের নাম, লেখক, ISBN, ক্যাটাগরি দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.library.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বইয়ের নাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">লেখক</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ISBN</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্যাটাগরি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মোট কপি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">উপলব্ধ</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($books as $index => $book)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($books->currentPage() - 1) * $books->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <button @click="openViewModal({{ $book->id }})"
                                            class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $book->title }}</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $book->author }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs text-gray-500">{{ $book->isbn ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $book->category ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $book->total_copies ?? 0 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($book->available_copies ?? 0) > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $book->available_copies ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openViewModal({{ $book->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $book->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.library.destroy', $book) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই বইটি মুছে ফেলতে চান?')">
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
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো বই পাওয়া যায়নি</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $books])
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন বই যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.library.store') }}" method="POST" class="p-6 space-y-5"
                    @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">বইয়ের শিরোনাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="createForm.title"
                                @blur="validateCreateField('title')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.title || (createAttempted && !createForm.title)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.title || (createAttempted && !createForm.title)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.title || 'বইয়ের শিরোনাম আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">লেখক <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="author" x-model="createForm.author"
                                @blur="validateCreateField('author')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.author || (createAttempted && !createForm.author)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.author || (createAttempted && !createForm.author)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.author || 'লেখকের নাম আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ISBN <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="isbn" x-model="createForm.isbn"
                                @blur="validateCreateField('isbn')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.isbn || (createAttempted && !createForm.isbn)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.isbn || (createAttempted && !createForm.isbn)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.isbn || 'ISBN আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি</label>
                            <input type="text" name="category" x-model="createForm.category"
                                placeholder="যেমন: উপন্যাস, বিজ্ঞান"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">মোট কপি <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="total_copies" min="1" x-model="createForm.total_copies"
                                @blur="validateCreateField('total_copies')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.total_copies || (createAttempted && !createForm.total_copies)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.total_copies || (createAttempted && !createForm.total_copies)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.total_copies || 'মোট কপি সংখ্যা আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">উপলব্ধ কপি <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="available_copies" min="0"
                                x-model="createForm.available_copies"
                                @blur="validateCreateField('available_copies')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.available_copies || (createAttempted && !createForm.available_copies)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.available_copies || (createAttempted && !createForm.available_copies)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.available_copies || 'উপলব্ধ কপি সংখ্যা আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থান</label>
                            <input type="text" name="location" x-model="createForm.location"
                                placeholder="যেমন: শেলফ ৩, র্যাক ২"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ</label>
                            <textarea name="description" rows="3" x-model="createForm.description"
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
                    <h3 class="font-heading font-bold text-white text-lg">বইয়ের তথ্য</h3>
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
                                <div class="w-14 h-14 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.title"></h4>
                                    <p class="text-sm text-gray-500" x-text="viewData.author"></p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">ISBN</dt>
                                    <dd class="font-medium text-gray-900 font-mono" x-text="viewData.isbn"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">ক্যাটাগরি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.category || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">অবস্থান</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.location || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোট কপি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.total_copies"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">উপলব্ধ কপি</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="viewData.available_copies > 0 ? 'bg-emerald-100 text-emerald-800' :
                                                'bg-red-100 text-red-800'"
                                            x-text="viewData.available_copies"></span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোট ধার</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.borrowings_count + 'টি'"></dd>
                                </div>
                            </dl>
                            <template x-if="viewData.description">
                                <div class="mt-5 pt-5 border-t border-gray-100">
                                    <dt class="text-gray-500 mb-2 text-sm">বিবরণ</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap"
                                        x-text="viewData.description"></dd>
                                </div>
                            </template>
                            <div class="mt-6 pt-5 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-semibold text-gray-700">ধার তালিকা</h5>
                                    <span class="text-xs text-gray-400"
                                        x-text="'মোট ' + viewData.borrowings_count + 'টি'"></span>
                                </div>
                                <template x-if="viewData.borrowings.length">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-100">
                                                <th class="text-left px-3 py-2.5 font-medium text-gray-500">ছাত্র/ছাত্রী</th>
                                                <th class="text-left px-3 py-2.5 font-medium text-gray-500">ধারের তারিখ</th>
                                                <th class="text-left px-3 py-2.5 font-medium text-gray-500">ফেরার তারিখ</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <template x-for="(b, i) in viewData.borrowings" :key="i">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-3 py-2.5 font-medium text-gray-900"
                                                        x-text="b.student_name"></td>
                                                    <td class="px-3 py-2.5 text-gray-600" x-text="b.borrowed_at"></td>
                                                    <td class="px-3 py-2.5 text-gray-600" x-text="b.returned_at || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>
                                <template x-if="!viewData.borrowings.length">
                                    <p class="text-center text-sm text-gray-400 py-6">এই বইটি এখনো কেউ ধার নেয়নি</p>
                                </template>
                            </div>
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
                    <h3 class="font-heading font-bold text-white text-lg">বই সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/library') }}/' + editData.id" method="POST" class="space-y-5"
                            @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বইয়ের শিরোনাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="title" x-model="editData.title"
                                        @blur="validateEditField('title')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.title || (editAttempted && !editData.title)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.title || (editAttempted && !editData.title)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.title || 'বইয়ের শিরোনাম আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">লেখক <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="author" x-model="editData.author"
                                        @blur="validateEditField('author')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.author || (editAttempted && !editData.author)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.author || (editAttempted && !editData.author)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.author || 'লেখকের নাম আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ISBN <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="isbn" x-model="editData.isbn"
                                        @blur="validateEditField('isbn')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.isbn || (editAttempted && !editData.isbn)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.isbn || (editAttempted && !editData.isbn)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.isbn || 'ISBN আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি</label>
                                    <input type="text" name="category" x-model="editData.category"
                                        placeholder="যেমন: উপন্যাস, বিজ্ঞান"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মোট কপি <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="total_copies" min="1" x-model="editData.total_copies"
                                        @blur="validateEditField('total_copies')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.total_copies || (editAttempted && !editData.total_copies)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.total_copies || (editAttempted && !editData.total_copies)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.total_copies || 'মোট কপি সংখ্যা আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">উপলব্ধ কপি <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="available_copies" min="0"
                                        x-model="editData.available_copies"
                                        @blur="validateEditField('available_copies')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.available_copies || (editAttempted && !editData.available_copies)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.available_copies || (editAttempted && !editData.available_copies)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.available_copies || 'উপলব্ধ কপি সংখ্যা আবশ্যক।'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থান</label>
                                    <input type="text" name="location" x-model="editData.location"
                                        placeholder="যেমন: শেলফ ৩, র্যাক ২"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ</label>
                                    <textarea name="description" rows="3" x-model="editData.description"
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
            function libraryApp() {
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
                        title: '',
                        author: '',
                        isbn: '',
                        category: '',
                        total_copies: '',
                        available_copies: '',
                        location: '',
                        description: ''
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
                            title: '',
                            author: '',
                            isbn: '',
                            category: '',
                            total_copies: '',
                            available_copies: '',
                            location: '',
                            description: ''
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
                            const res = await fetch(`{{ url('admin/library') }}/${id}`);
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
                            const res = await fetch(`{{ url('admin/library') }}/${id}/edit`);
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
                            title: 'বইয়ের শিরোনাম',
                            author: 'লেখকের নাম',
                            isbn: 'ISBN',
                            total_copies: 'মোট কপি সংখ্যা',
                            available_copies: 'উপলব্ধ কপি সংখ্যা',
                        };

                        if (requiredFields[field] && (val === null || val === undefined || String(val).trim() === '')) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
                            return false;
                        }

                        if (field === 'total_copies') {
                            const n = parseInt(val, 10);
                            if (isNaN(n) || n < 1) {
                                errors[field] = 'মোট কপি সংখ্যা কমপক্ষে ১ হতে হবে।';
                                return false;
                            }
                            const avail = parseInt(data.available_copies, 10);
                            if (!isNaN(avail) && avail > n) {
                                errors.available_copies = 'উপলব্ধ কপি মোট কপির সমান বা কম হতে হবে।';
                                return false;
                            }
                        }

                        if (field === 'available_copies') {
                            const n = parseInt(val, 10);
                            if (isNaN(n) || n < 0) {
                                errors[field] = 'উপলব্ধ কপি সংখ্যা ০ বা তার বেশি হতে হবে।';
                                return false;
                            }
                            const total = parseInt(data.total_copies, 10);
                            if (!isNaN(total) && n > total) {
                                errors[field] = 'উপলব্ধ কপি মোট কপির সমান বা কম হতে হবে।';
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
                        ['title', 'author', 'isbn', 'total_copies', 'available_copies'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['title', 'author', 'isbn', 'total_copies', 'available_copies'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection