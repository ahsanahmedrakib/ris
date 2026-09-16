@extends('layouts.admin')

@section('title', 'হিরো স্লাইডার তালিকা')

@section('content')
    <div class="space-y-6" x-data="heroSlideApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">হিরো স্লাইডার</h1>
                <p class="text-sm text-gray-500 mt-1">হোমপেজের ব্যানার স্লাইড পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন স্লাইড
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

        @if ($items->isEmpty())
            <div class="bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-xl text-sm font-medium">
                বর্তমানে কোনো স্লাইড যোগ করা হয়নি। হোমপেজে ডিফল্ট ১০টি স্লাইড প্রদর্শিত হচ্ছে। স্লাইড যোগ করলে সেগুলো দেখানো হবে।
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.hero-slides.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="শিরোনাম দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end gap-2 lg:col-span-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.hero-slides.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ছবি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">শিরোনাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বাটন</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/hero-slides') }}', {{ $item->id }}, {{ $item->is_active ? 'true' : 'false' }})">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                                        class="w-24 h-12 rounded-lg object-cover">
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <button @click="openViewModal({{ $item->id }})"
                                        class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer text-left">
                                        {{ $item->title }}
                                    </button>
                                    @if ($item->subtitle)
                                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $item->subtitle }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $item->btn_text ?? '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="active ? 'bg-emerald-100 text-emerald-700' :
                                            'bg-gray-100 text-gray-600'"
                                            x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
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
                                        <button @click="openViewModal({{ $item->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $item->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.hero-slides.destroy', $item->id) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই স্লাইডটি মুছে ফেলতে চান?')">
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
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো স্লাইড পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন স্লাইড যোগ করুন বা ফিল্টার পরিবর্তন করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $items])
        </div>

        {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">নতুন স্লাইড</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিরোনাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="createForm.title"
                                @blur="validateCreateField('title')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.title || (createAttempted && !createForm.title)) ? 'border-red-400' :
                                'border-gray-200'">
                            <template x-if="createErrors.title || (createAttempted && !createForm.title)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.title || 'শিরোনাম আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বাটনের লেখা</label>
                            <input type="text" name="btn_text" x-model="createForm.btn_text"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: ভর্তি করুন">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">সাবটাইটেল / বিবরণ</label>
                            <textarea name="subtitle" rows="2" x-model="createForm.subtitle"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                placeholder="ব্যানারে দেখানোর সংক্ষিপ্ত বিবরণ (ঐচ্ছিক)..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বাটনের লিংক</label>
                            <select name="link" x-model="createForm.link"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">লিংক নির্বাচন করুন</option>
                                <option value="admission">ভর্তি</option>
                                <option value="about">আমাদের সম্পর্কে</option>
                                <option value="scholarship">মেধাবৃত্তি</option>
                                <option value="contact">যোগাযোগ</option>
                                <option value="teachers">শিক্ষক</option>
                                <option value="gallery">গ্যালারি</option>
                                <option value="notices">নোটিশ</option>
                                <option value="testimonials">শুভকামনা ও মতামত</option>
                                <option value="fees">টিউশন ফি</option>
                                <option value="calendar">একাডেমিক ক্যালেন্ডার</option>
                                <option value="facilities">স্কুলের সুবিধা</option>
                                <option value="results">ফলাফল</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">সাজানোর ক্রম</label>
                            <input type="number" name="sort_order" x-model="createForm.sort_order"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="0">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ছবি <span
                                    class="text-red-500">*</span></label>
                            <img x-show="createImagePreview" :src="createImagePreview"
                                class="max-h-52 w-auto rounded-lg border border-gray-200 shadow-sm mb-3">
                            <input type="file" name="image" accept="image/*" id="create_image"
                                @change="createImagePreview = URL.createObjectURL($event.target.files[0])"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.image ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="createErrors.image">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.image"></p>
                            </template>
                            <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — সর্বোচ্চ 5MB। ভালো ফলাফলের জন্য 1920×800
                                ব্যবহার করুন।</p>
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary"
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
                    <h3 class="font-heading font-bold text-white text-lg">স্লাইডের তথ্য</h3>
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
                            <div class="mb-5 rounded-xl overflow-hidden">
                                <img :src="viewData.image" class="w-full max-h-72 object-cover">
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">শিরোনাম</dt>
                                    <dd class="font-medium text-gray-900 font-heading font-bold" x-text="viewData.title"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">বাটন</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.btn_text || '-'"></dd>
                                </div>
                                <div class="sm:col-span-2" x-show="viewData.subtitle">
                                    <dt class="text-gray-500 mb-1">সাবটাইটেল / বিবরণ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.subtitle"></dd>
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
                    <h3 class="font-heading font-bold text-white text-lg">স্লাইড সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/hero-slides') }}/' + editData.id" method="POST"
                            enctype="multipart/form-data" class="space-y-5" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শিরোনাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="title" x-model="editData.title"
                                        @blur="validateEditField('title')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.title || (editAttempted && !editData.title)) ? 'border-red-400' :
                                        'border-gray-200'">
                                    <template x-if="editErrors.title || (editAttempted && !editData.title)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.title || 'শিরোনাম আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বাটনের লেখা</label>
                                    <input type="text" name="btn_text" x-model="editData.btn_text"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="যেমন: ভর্তি করুন">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">সাবটাইটেল / বিবরণ</label>
                                    <textarea name="subtitle" rows="2" x-model="editData.subtitle"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                        placeholder="ব্যানারে দেখানোর সংক্ষিপ্ত বিবরণ (ঐচ্ছিক)..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">বাটনের লিংক</label>
                                    <select name="link" x-model="editData.link"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="">লিংক নির্বাচন করুন</option>
                                        <option value="admission" x-bind:selected="editData.link === 'admission'">ভর্তি</option>
                                        <option value="about" x-bind:selected="editData.link === 'about'">আমাদের সম্পর্কে</option>
                                        <option value="scholarship" x-bind:selected="editData.link === 'scholarship'">মেধাবৃত্তি</option>
                                        <option value="contact" x-bind:selected="editData.link === 'contact'">যোগাযোগ</option>
                                        <option value="teachers" x-bind:selected="editData.link === 'teachers'">শিক্ষক</option>
                                        <option value="gallery" x-bind:selected="editData.link === 'gallery'">গ্যালারি</option>
                                        <option value="notices" x-bind:selected="editData.link === 'notices'">নোটিশ</option>
                                        <option value="testimonials" x-bind:selected="editData.link === 'testimonials'">শুভকামনা ও মতামত</option>
                                        <option value="fees" x-bind:selected="editData.link === 'fees'">টিউশন ফি</option>
                                        <option value="calendar" x-bind:selected="editData.link === 'calendar'">একাডেমিক ক্যালেন্ডার</option>
                                        <option value="facilities" x-bind:selected="editData.link === 'facilities'">স্কুলের সুবিধা</option>
                                        <option value="results" x-bind:selected="editData.link === 'results'">ফলাফল</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">সাজানোর ক্রম</label>
                                    <input type="number" name="sort_order" x-model="editData.sort_order"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="0">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">নতুন ছবি</label>
                                    <input type="file" name="image" accept="image/*"
                                        @change="editImagePreview = URL.createObjectURL($event.target.files[0])"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — সর্বোচ্চ 5MB। ফাইল নির্বাচন না করলে
                                        পুরাতন থাকবে।</p>
                                    <div class="mt-3 flex items-center gap-3">
                                        <img :src="editImagePreview || editData.image"
                                            class="w-40 h-20 rounded-lg object-cover border border-gray-200">
                                        <span class="text-sm text-gray-500" x-text="editImagePreview ? 'নতুন ছবি' : 'বর্তমান ছবি'"></span>
                                    </div>
                                </div>
                                <div class="flex items-end">
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

            function heroSlideApp() {
                return {
                    showCreateModal: false,
                    showViewModal: false,
                    showEditModal: false,
                    viewData: null,
                    editData: null,
                    viewLoading: false,
                    editLoading: false,

                    createForm: {
                        title: '',
                        subtitle: '',
                        btn_text: '',
                        link: '',
                        sort_order: '0',
                        is_active: true
                    },
                    createErrors: {},
                    createAttempted: false,
                    createImagePreview: null,

                    editErrors: {},
                    editAttempted: false,
                    editImagePreview: null,

                    openCreateModal() {
                        this.createForm = {
                            title: '',
                            subtitle: '',
                            btn_text: '',
                            link: '',
                            sort_order: '0',
                            is_active: true
                        };
                        this.createErrors = {};
                        this.createAttempted = false;
                        this.createImagePreview = null;
                        this.showCreateModal = true;
                    },

                    async openViewModal(id) {
                        this.showViewModal = true;
                        this.viewLoading = true;
                        this.viewData = null;
                        try {
                            const res = await fetch(`{{ url('admin/hero-slides') }}/${id}`);
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
                        this.editImagePreview = null;
                        this.editErrors = {};
                        this.editAttempted = false;
                        try {
                            const res = await fetch(`{{ url('admin/hero-slides') }}/${id}/edit`);
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

                        if (['title'].includes(field)) {
                            if (!val || val.trim() === '') {
                                errors[field] = 'শিরোনাম আবশ্যক।';
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
                        ['title'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        const fileInput = el.querySelector('input[name="image"]');
                        if (!fileInput || !fileInput.files.length) {
                            this.createErrors.image = 'ছবি আবশ্যক।';
                            valid = false;
                        }
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['title'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection