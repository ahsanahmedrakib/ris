@extends('layouts.admin')

@section('title', 'শুভকামনা ও মতামত তালিকা')

@section('content')
    <div class="space-y-6" x-data="testimonialApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">শুভকামনা ও মতামত</h1>
                <p class="text-sm text-gray-500 mt-1">অভিভাবকদের মতামত পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন মতামত
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
            <form method="GET" action="{{ route('admin.testimonials.index') }}">
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
                                placeholder="নাম বা মন্তব্য দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.testimonials.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">নাম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">পদবি</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মন্তব্য</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">রেটিং</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($testimonials as $testimonial)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/testimonials') }}', {{ $testimonial->id }}, {{ $testimonial->is_active ? 'true' : 'false' }})">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($testimonial->photo)
                                        <img src="{{ Storage::url($testimonial->photo) }}" alt="{{ $testimonial->name }}"
                                            class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <div
                                            class="w-9 h-9 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-sm font-semibold">
                                            {{ mb_substr($testimonial->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button @click="openViewModal({{ $testimonial->id }})"
                                        class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $testimonial->name }}</button>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $testimonial->designation ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 max-w-64 truncate">{{ $testimonial->message }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-0.5">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i < $testimonial->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-700' :
                                        'bg-gray-100 text-gray-600'"
                                        x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ $testimonial->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button x-show="!active" @click="toggle()" title="সক্রিয় করুন (অনুমোদন)"
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
                                        <button @click="openViewModal({{ $testimonial->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $testimonial->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial->id) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই মতামতটি মুছে ফেলতে চান?')">
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
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো মতামত পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন মতামত যোগ করুন বা অনুসন্ধান পরিবর্তন করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $testimonials])
        </div>

        {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-heading font-bold text-white text-lg">নতুন মতামত</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data"
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">পদবি</label>
                            <input type="text" name="designation" x-model="createForm.designation"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="createErrors.designation ? 'border-red-400' : 'border-gray-200'"
                                placeholder="যেমন: অভিভাবক, শিক্ষার্থী">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">মন্তব্য <span
                                    class="text-red-500">*</span></label>
                            <textarea name="message" rows="4" x-model="createForm.message"
                                @blur="validateCreateField('message')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                :class="(createErrors.message || (createAttempted && !createForm.message)) ? 'border-red-400' :
                                'border-gray-200'"></textarea>
                            <template x-if="createErrors.message || (createAttempted && !createForm.message)">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.message || 'মন্তব্য আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">রেটিং <span
                                    class="text-red-500">*</span></label>
                            <select name="rating" x-model="createForm.rating"
                                @change="validateCreateField('rating')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="createErrors.rating ? 'border-red-400' : 'border-gray-200'">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} স্টার</option>
                                @endfor
                            </select>
                            <template x-if="createErrors.rating">
                                <p class="mt-1 text-xs text-red-600" x-text="createErrors.rating"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>
                            <div class="flex items-center gap-4">
                                <div class="shrink-0">
                                    <img x-show="createPhotoPreview" :src="createPhotoPreview"
                                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-100 shadow-sm">
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
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            </div>
                            <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — সর্বোচ্চ 2MB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">সাজানোর ক্রম</label>
                            <input type="number" name="sort_order" x-model="createForm.sort_order"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="0">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
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
                    <h3 class="font-heading font-bold text-white text-lg">মতামতের তথ্য</h3>
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
                                <template x-if="viewData.photo">
                                    <img :src="viewData.photo" class="w-16 h-16 rounded-full object-cover">
                                </template>
                                <template x-if="!viewData.photo">
                                    <div
                                        class="w-16 h-16 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xl font-semibold"
                                        x-text="viewData.name ? viewData.name.substring(0,1) : ''"></div>
                                </template>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="viewData.designation || '-'"></p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">মন্তব্য</dt>
                                    <dd class="font-medium text-gray-900 italic" x-text="viewData.message"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">রেটিং</dt>
                                    <dd>
                                        <div class="flex items-center gap-0.5">
                                            <template x-for="i in 5">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                    :class="i <= viewData.rating ? 'text-amber-400' : 'text-gray-200'">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </template>
                                        </div>
                                    </dd>
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
                    <h3 class="font-heading font-bold text-white text-lg">মতামত সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/testimonials') }}/' + editData.id" method="POST"
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">পদবি</label>
                                    <input type="text" name="designation" x-model="editData.designation"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="editErrors.designation ? 'border-red-400' : 'border-gray-200'"
                                        placeholder="যেমন: অভিভাবক, শিক্ষার্থী">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মন্তব্য <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="message" rows="4" x-model="editData.message"
                                        @blur="validateEditField('message')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                        :class="(editErrors.message || (editAttempted && !editData.message)) ? 'border-red-400' :
                                        'border-gray-200'"></textarea>
                                    <template x-if="editErrors.message || (editAttempted && !editData.message)">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.message || 'মন্তব্য আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">রেটিং <span
                                            class="text-red-500">*</span></label>
                                    <select name="rating" x-model="editData.rating"
                                        @change="validateEditField('rating')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="editErrors.rating ? 'border-red-400' : 'border-gray-200'">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} স্টার</option>
                                        @endfor
                                    </select>
                                    <template x-if="editErrors.rating">
                                        <p class="mt-1 text-xs text-red-600" x-text="editErrors.rating"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">নতুন ছবি</label>
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <img x-show="editPhotoPreview || editData?.photo"
                                                :src="editPhotoPreview || editData.photo"
                                                class="w-20 h-20 rounded-full object-cover border-2 border-gray-100 shadow-sm">
                                            <div x-show="!editPhotoPreview && !editData.photo"
                                                class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <input type="file" name="photo" accept="image/*"
                                            @change="editPhotoPreview = URL.createObjectURL($event.target.files[0])"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — সর্বোচ্চ 2MB। ফাইল নির্বাচন না করলে পুরাতন থাকবে।</p>
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

            function testimonialApp() {
                return {
                    showCreateModal: false,
                    showViewModal: false,
                    showEditModal: false,
                    viewData: null,
                    editData: null,
                    viewLoading: false,
                    editLoading: false,

                    createForm: {
                        name: '',
                        designation: '',
                        message: '',
                        rating: '5',
                        sort_order: '0',
                        is_active: true
                    },
                    createErrors: {},
                    createAttempted: false,
                    createPhotoPreview: null,

                    editErrors: {},
                    editAttempted: false,
                    editPhotoPreview: null,

                    openCreateModal() {
                        this.createForm = {
                            name: '',
                            designation: '',
                            message: '',
                            rating: '5',
                            sort_order: '0',
                            is_active: true
                        };
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
                            const res = await fetch(`{{ url('admin/testimonials') }}/${id}`);
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
                        this.editPhotoPreview = null;
                        this.editErrors = {};
                        this.editAttempted = false;
                        try {
                            const res = await fetch(`{{ url('admin/testimonials') }}/${id}/edit`);
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

                        if (['name', 'message', 'rating'].includes(field)) {
                            if (!val || val.trim() === '') {
                                const msgs = {
                                    name: 'নাম আবশ্যক।',
                                    message: 'মন্তব্য আবশ্যক।',
                                    rating: 'রেটিং নির্বাচন করুন।'
                                };
                                errors[field] = msgs[field];
                                return false;
                            }
                        }

                        if (field === 'rating' && val && (parseInt(val) < 1 || parseInt(val) > 5)) {
                            errors[field] = 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।';
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
                        ['name', 'message', 'rating'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['name', 'message', 'rating'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection