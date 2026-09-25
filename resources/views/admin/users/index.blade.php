@extends('layouts.admin')

@section('title', 'ইউজার ব্যবস্থাপনা')

@section('content')
    <div class="space-y-6" x-data="userApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">অ্যাডমিন ইউজার</h1>
                <p class="text-sm text-gray-500 mt-1">অ্যাডমিন অ্যাকাউন্ট তৈরি, আপডেট ও ডিলিট করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন অ্যাডমিন
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.users.index') }}">
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
                                placeholder="নাম, ইউজারনেম, ইমেইল, মোবাইল দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.users.index') }}"
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
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ইউজারনেম</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ইমেইল</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">মোবাইল</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th
                                class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" data-table-body>
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors" x-data="activeRow('{{ url('admin/users') }}', {{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="relative w-8 h-8 rounded-full overflow-hidden shrink-0 {{ $user->avatar ? '' : 'bg-ris-primary/10' }} flex items-center justify-center">
                                            @if ($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span
                                                    class="text-ris-primary text-xs font-semibold">{{ mb_substr($user->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <button @click="openViewModal({{ $user->id }})"
                                            class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $user->name }}</button>
                                        @if ($user->id === auth()->id())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500">আপনি</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $user->username ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $user->email }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $user->phone ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                        x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ $user->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
                                </td>
                                <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openViewModal({{ $user->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal({{ $user->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @if ($user->id !== auth()->id())
                                            <button x-show="!active" @click="toggle()" title="সক্রিয় করুন"
                                                class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors cursor-pointer"
                                                :disabled="busy">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                            <button x-show="active" @click="toggle()" title="নিষ্ক্রিয় করুন"
                                                class="p-1.5 rounded-lg text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer"
                                                :disabled="busy">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                @submit.prevent="confirmDelete('আপনি কি নিশ্চিত এই অ্যাডমিনকে ডিলিট করতে চান?', $el)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
                                                    title="ডিলিট করুন">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো অ্যাডমিন পাওয়া যায়নি</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $users])
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
                    <button @click="toasts.splice(index, 1)"
                        class="ml-auto shrink-0 opacity-60 hover:opacity-100 cursor-pointer">
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন অ্যাডমিন যোগ</h3>
                    <button @click="showCreateModal = false"
                        class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
<form action="{{ route('admin.users.store') }}" method="POST"
                    class="p-6 space-y-5" enctype="multipart/form-data" @submit.prevent="validateCreateForm($el)">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">প্রোফাইল ছবি <span
                                class="text-gray-400 font-normal">(ঐচ্ছিক)</span></label>
                        <div class="flex items-center gap-4">
                            <div class="shrink-0">
                                <img x-show="createPhotoPreview" :src="createPhotoPreview"
                                    class="w-20 h-20 rounded-full object-cover ring-2 ring-ris-primary/20">
                                <div x-show="!createPhotoPreview"
                                    class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xl font-semibold">
                                    প্রথম অক্ষর
                                </div>
                            </div>
                            <input type="file" name="avatar" accept="image/*"
                                @change="createPhotoPreview = URL.createObjectURL($event.target.files[0])"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">ইউজারনেম</label>
                            <input type="text" name="username" x-model="createForm.username"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: admin">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">পাসওয়ার্ড <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password" x-model="createForm.password"
                                @blur="validateCreateField('password')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.password || (createAttempted && !createForm.password)) ?
                                'border-red-400' : 'border-gray-200'"
                                placeholder="কমপক্ষে ৮ অক্ষর">
                            <template x-if="createErrors.password || (createAttempted && !createForm.password)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.password || 'পাসওয়ার্ড আবশ্যক (কমপক্ষে ৮ অক্ষর)'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পাসওয়ার্ড নিশ্চিত করুন <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation"
                                x-model="createForm.password_confirmation"
                                @blur="validateCreateField('password_confirmation')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.password_confirmation || (createAttempted && createForm.password &&
                                    createForm.password !== createForm.password_confirmation)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.password_confirmation || (createAttempted && createForm.password && createForm.password !== createForm.password_confirmation)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.password_confirmation || 'পাসওয়ার্ড দুটি মিলে যায়নি'"></p>
                            </template>
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
                    <h3 class="font-heading font-bold text-white text-lg">অ্যাডমিন তথ্য</h3>
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
                            <div class="flex items-center gap-4 mb-5">
                                <template x-if="viewData.avatar">
                                    <img :src="viewData.avatar" alt="প্রোফাইল ছবি"
                                        class="w-20 h-20 rounded-full object-cover ring-2 ring-ris-primary/20 shrink-0">
                                </template>
                                <template x-if="!viewData.avatar">
                                    <div
                                        class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-semibold shrink-0"
                                        x-text="viewData.name?.charAt(0)"></div>
                                </template>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="'অ্যাডমিন'"></p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">ইউজারনেম</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.username || '-'"></dd>
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
                                    <dt class="text-gray-500 mb-1">তৈরি হয়েছে</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.created_at"></dd>
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
                    <h3 class="font-heading font-bold text-white text-lg">অ্যাডমিন সম্পাদনা</h3>
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
<form :action="'{{ url('admin/users') }}/' + editData.id" method="POST"
                            class="space-y-5" enctype="multipart/form-data" @submit.prevent="validateEditForm($el)">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">প্রোফাইল ছবি <span
                                        class="text-gray-400 font-normal">(ঐচ্ছিক)</span></label>
                                <div class="flex items-center gap-4">
                                    <div class="shrink-0">
                                        <img x-show="editPhotoPreview" :src="editPhotoPreview"
                                            class="w-20 h-20 rounded-full object-cover ring-2 ring-ris-primary/20">
                                        <img x-show="!editPhotoPreview && !editPhotoRemoved && editData.avatar"
                                            :src="editData.avatar"
                                            class="w-20 h-20 rounded-full object-cover ring-2 ring-ris-primary/20">
                                        <div x-show="editPhotoRemoved || (!editPhotoPreview && !editData.avatar)"
                                            class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xl font-semibold">
                                            প্রথম অক্ষর
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <input type="file" name="avatar" accept="image/*"
                                            @change="editPhotoPreview = URL.createObjectURL($event.target.files[0])"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                        <label class="inline-flex items-center gap-2 text-xs text-gray-500">
                                            <input type="checkbox" name="remove_avatar" value="1"
                                                class="rounded border-gray-300 text-ris-primary focus:ring-ris-primary cursor-pointer"
                                                @change="onToggleRemovePhoto($event)">
                                            ছবি সরিয়ে ফেলুন
                                        </label>
                                        <p x-show="editData.avatar && !editPhotoPreview && !editPhotoRemoved"
                                            class="text-xs text-gray-400">নতুন ছবি নির্বাচন না করলে বর্তমান ছবি
                                            থাকবে।</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ইউজারনেম</label>
                                    <input type="text" name="username" x-model="editData.username"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল</label>
                                    <input type="tel" name="phone" x-model="editData.phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">নতুন পাসওয়ার্ড</label>
                                    <input type="password" name="password" x-model="editData.password"
                                        @blur="validateEditField('password')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.password || (editAttempted && editData.password && editData.password
                                            .length < 8)) ?
                                        'border-red-400' : 'border-gray-200'"
                                        placeholder="খালি রাখলে পরিবর্তন হবে না">
                                    <template
                                        x-if="editErrors.password || (editAttempted && editData.password && editData.password.length < 8)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.password || 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">নতুন পাসওয়ার্ড
                                        নিশ্চিত করুন</label>
                                    <input type="password" name="password_confirmation"
                                        x-model="editData.password_confirmation"
                                        @blur="validateEditField('password_confirmation')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.password_confirmation || (editAttempted && editData.password &&
                                            editData.password !== editData.password_confirmation)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.password_confirmation || (editAttempted && editData.password && editData.password !== editData.password_confirmation)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.password_confirmation || 'পাসওয়ার্ড দুটি মিলে যায়নি'"></p>
                                    </template>
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

            function userApp() {
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
                        username: '',
                        email: '',
                        phone: '',
                        password: '',
                        password_confirmation: ''
                    },
                    createErrors: {},
                    createAttempted: false,
                    createPhotoPreview: null,

                    editErrors: {},
                    editAttempted: false,
                    editPhotoPreview: null,
                    editPhotoRemoved: false,

                    formSubmitting: false,

                    showToast(type, message) {
                        this.toasts.push({
                            type,
                            message
                        });
                        setTimeout(() => {
                            this.toasts.shift();
                        }, 3000);
                    },

                    confirmDelete(message, form) {
                        if (confirm(message)) form.submit();
                    },

                    onToggleRemovePhoto(event) {
                        this.editPhotoRemoved = event.target.checked;
                    },

                    openCreateModal() {
                        this.createForm = {
                            name: '',
                            username: '',
                            email: '',
                            phone: '',
                            password: '',
                            password_confirmation: ''
                        };
                        this.createErrors = {};
                        this.createAttempted = false;
                        this.createPhotoPreview = null;
                        this.showCreateModal = true;
                    },

                    async openViewModal(id) {
                        this.showViewModal = true;
                        this.viewData = null;
                        this.viewLoading = true;
                        try {
                            const res = await fetch(`{{ url('admin/users') }}/${id}`);
                            if (!res.ok) throw new Error();
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
                        this.editData = null;
                        this.editLoading = true;
                        this.editErrors = {};
                        this.editAttempted = false;
                        this.editPhotoPreview = null;
                        this.editPhotoRemoved = false;
                        try {
                            const res = await fetch(`{{ url('admin/users') }}/${id}/edit`);
                            if (!res.ok) throw new Error();
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
                            email: 'ইমেইল'
                        };

                        if (requiredFields[field] && (!val || val.toString().trim() === '')) {
                            errors[field] = requiredFields[field] + ' আবশ্যক।';
                            return false;
                        }

                        if (field === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                            errors[field] = 'সঠিক ইমেইল দিন।';
                            return false;
                        }

                        if ((field === 'password' || field === 'password_confirmation' || field === 'create_password') && data
                            .password) {
                            if (data.password.length < 8) {
                                errors[field] = 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।';
                                return false;
                            }
                            if (data.password !== data.password_confirmation) {
                                errors[field] = 'পাসওয়ার্ড দুটি মিলে যায়নি।';
                                return false;
                            }
                        }

                        if (field === 'create_password' && !data.password) {
                            errors[field] = 'পাসওয়ার্ড আবশ্যক।';
                            return false;
                        }

                        return true;
                    },

                    validateCreateField(field) {
                        field = (field === 'password' || field === 'password_confirmation') ? 'create_password' : field;
                        return this.validateField(field, this.createForm, this.createErrors);
                    },

                    validateEditField(field) {
                        return this.validateField(field, this.editData, this.editErrors);
                    },

                    validateCreateForm(el) {
                        this.createAttempted = true;
                        this.createErrors = {};
                        let valid = true;
                        ['name', 'email', 'password', 'password_confirmation'].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) this.submitForm(el, 'create');
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['name', 'email'].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (this.editData.password && !this.validateEditField('password')) valid = false;
                        if (this.editData.password && !this.validateEditField('password_confirmation')) valid = false;
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
                                this.showToast('error', data.message || 'সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                return;
                            }
                            if (mode === 'edit') this.showEditModal = false;
                            else this.showCreateModal = false;
                            this.showToast('success', data.message || 'সফলভাবে সংরক্ষণ হয়েছে।');
                            RisAdmin.refreshTable();
                        } catch (e) {
                            this.showToast('error', 'সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                        } finally {
                            this.formSubmitting = false;
                        }
                    }
                }
            }
        </script>
    @endsection
@endsection
