@extends('layouts.admin')

@section('title', 'নোটিশ ব্যবস্থাপনা')

@section('content')
<div class="space-y-6" x-data="noticeApp()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">নোটিশ ব্যবস্থাপনা</h1>
            <p class="text-sm text-gray-500 mt-1">সকল নোটিশ দেখুন ও পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.notices.download', request()->query()) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel ডাউনলোড
            </a>
            <button @click="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                নতুন নোটিশ
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm font-medium">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.notices.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="শিরোনাম বা বিবরণ দিয়ে খুঁজুন..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল ক্যাটাগরি</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                        ফিল্টার করুন
                    </button>
                    <a href="{{ route('admin.notices.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
                        রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="gradient-logo">
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">শিরোনাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">ক্যাটাগরি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">ধরন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">লক্ষ্য</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">প্রকাশের তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">মেয়াদোত্তীর্ণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                        <th class="text-right px-5 py-3.5 font-medium text-white whitespace-nowrap">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($notices as $index => $notice)
                        <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/notices') }}', {{ $notice->id }}, {{ $notice->is_active ? 'true' : 'false' }})">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($notices->currentPage() - 1) * $notices->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div>
                                    <button @click="openViewModal({{ $notice->id }})" class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer text-left">{{ $notice->title }}</button>
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $notice->content }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($notice->category ?? 'general') === 'admission' ? 'bg-blue-50 text-blue-700' : (($notice->category ?? 'general') === 'exam' ? 'bg-purple-50 text-purple-700' : (($notice->category ?? 'general') === 'holiday' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700')) }}">
                                    {{ $categories[$notice->category] ?? 'সাধারণ' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($notice->type ?? '') === 'event' ? 'bg-blue-50 text-blue-700' : (($notice->type ?? '') === 'holiday' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ($notice->type ?? '') === 'event' ? 'অনুষ্ঠান' : (($notice->type ?? '') === 'holiday' ? 'ছুটি' : 'নোটিশ') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ ($notice->target_role ?? '') === 'all' ? 'সকল' : (($notice->target_role ?? '') === 'teacher' ? 'শিক্ষক' : (($notice->target_role ?? '') === 'parent' ? 'অভিভাবক' : (($notice->target_role ?? '') === 'admin' ? 'অ্যাডমিন' : 'ছাত্র'))) }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $notice->published_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $notice->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                                        x-text="active ? 'সক্রিয়' : 'নিষ্ক্রিয়'">{{ $notice->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</span>
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
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button @click="openViewModal({{ $notice->id }})" class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer" title="দেখুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button @click="openEditModal({{ $notice->id }})" class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer" title="সম্পাদনা">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer" title="মুছুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো নোটিশ নেই</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($notices) && $notices instanceof \Illuminate\Pagination\LengthAwarePaginator && $notices->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $notices->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">নতুন নোটিশ</h3>
                <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.notices.store') }}" method="POST" class="p-6 space-y-5" @submit.prevent="validateCreateForm($el)">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শিরোনাম <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="createForm.title" @blur="validateCreateField('title')"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                        :class="(createErrors.title || (createAttempted && !createForm.title)) ? 'border-red-400' : 'border-gray-200'">
                    <template x-if="createErrors.title || (createAttempted && !createForm.title)"><p class="mt-1 text-xs text-red-600" x-text="createErrors.title || 'শিরোনাম আবশ্যক।'"></p></template>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ <span class="text-red-500">*</span></label>
                    <input type="hidden" name="content">
                    <div id="noticeCreateQuill" class="ris-quill bg-white border border-gray-200 rounded-lg"></div>
                    <template x-if="createErrors.content || (createAttempted && !createContentText)">
                        <p class="mt-1 text-xs text-red-600" x-text="createErrors.content || 'বিবরণ আবশ্যক।'"></p>
                    </template>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ধরন <span class="text-red-500">*</span></label>
                        <select name="type" x-model="createForm.type" @blur="validateCreateField('type')"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                            :class="createErrors.type ? 'border-red-400' : 'border-gray-200'">
                            <option value="">ধরন নির্বাচন করুন</option>
                            <option value="notice">নোটিশ</option>
                            <option value="event">অনুষ্ঠান</option>
                            <option value="holiday">ছুটি</option>
                        </select>
                        <template x-if="createErrors.type"><p class="mt-1 text-xs text-red-600" x-text="createErrors.type"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি <span class="text-red-500">*</span></label>
                        <select name="category" x-model="createForm.category" @blur="validateCreateField('category')"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                            :class="createErrors.category ? 'border-red-400' : 'border-gray-200'">
                            <option value="">ক্যাটাগরি নির্বাচন করুন</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <template x-if="createErrors.category"><p class="mt-1 text-xs text-red-600" x-text="createErrors.category"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">লক্ষ্য</label>
                        <select name="target_role" x-model="createForm.target_role"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="all">সকল</option>
                            <option value="admin">অ্যাডমিন</option>
                            <option value="teacher">শিক্ষক</option>
                            <option value="parent">অভিভাবক</option>
                            <option value="student">ছাত্র</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">প্রকাশের তারিখ</label>
                        <input type="text" data-date-mask="datetime" name="published_at" x-model="createForm.published_at"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                            placeholder="dd/mm/yyyy hh:mm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মেয়াদোত্তীর্ণ</label>
                        <input type="text" data-date-mask="datetime" name="expires_at" x-model="createForm.expires_at"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                            placeholder="dd/mm/yyyy hh:mm">
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
                        class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showViewModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">নোটিশের তথ্য</h3>
                <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="viewData">
                    <div>
                        <h3 class="font-heading font-bold text-ris-dark text-lg mb-4" x-text="viewData.title"></h3>
                        <div class="text-sm text-gray-600 leading-relaxed mb-5 prose prose-sm max-w-none" x-html="viewData.content"></div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 mb-1">ধরন</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700" x-text="viewData.type_label"></span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ক্যাটাগরি</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700" x-text="viewData.category_label"></span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">লক্ষ্য</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.target_label"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">স্ট্যাটাস</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="viewData.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'" x-text="viewData.status_label"></span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">প্রকাশের তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.published_at"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">মেয়াদোত্তীর্ণ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.expires_at"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">প্রকাশকারী</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.publisher_name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">তৈরির সময়</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.created_at"></dd>
                            </div>
                        </dl>
                        <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end gap-3">
                            <button @click="showViewModal = false"
                                class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বন্ধ করুন</button>
                            <button @click="showViewModal = false; openEditModal(viewData.id)"
                                class="px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">সম্পাদনা</button>
                        </div>
                    </div>
                </template>
                <template x-if="viewLoading">
                    <div class="py-12 text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
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
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">নোটিশ সম্পাদনা</h3>
                <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="editData">
                    <form :action="'{{ url('admin/notices') }}/' + editData.id" method="POST" class="space-y-5" @submit.prevent="validateEditForm($el)" id="noticeEditForm">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শিরোনাম <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editData.title" @blur="validateEditField('title')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(editErrors.title || (editAttempted && !editData.title)) ? 'border-red-400' : 'border-gray-200'">
                            <template x-if="editErrors.title || (editAttempted && !editData.title)"><p class="mt-1 text-xs text-red-600" x-text="editErrors.title || 'শিরোনাম আবশ্যক।'"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">বিবরণ <span class="text-red-500">*</span></label>
                            <input type="hidden" name="content">
                            <div id="noticeEditQuill" class="ris-quill bg-white border border-gray-200 rounded-lg"></div>
                            <template x-if="editErrors.content || (editAttempted && !editContentText)">
                                <p class="mt-1 text-xs text-red-600" x-text="editErrors.content || 'বিবরণ আবশ্যক।'"></p>
                            </template>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ধরন <span class="text-red-500">*</span></label>
                                <select name="type" x-model="editData.type" @blur="validateEditField('type')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                    :class="editErrors.type ? 'border-red-400' : 'border-gray-200'">
                                    <option value="notice">নোটিশ</option>
                                    <option value="event">অনুষ্ঠান</option>
                                    <option value="holiday">ছুটি</option>
                                </select>
                                <template x-if="editErrors.type"><p class="mt-1 text-xs text-red-600" x-text="editErrors.type"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি <span class="text-red-500">*</span></label>
                                <select name="category" x-model="editData.category" @blur="validateEditField('category')"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                    :class="editErrors.category ? 'border-red-400' : 'border-gray-200'">
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <template x-if="editErrors.category"><p class="mt-1 text-xs text-red-600" x-text="editErrors.category"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">লক্ষ্য</label>
                                <select name="target_role" x-model="editData.target_role"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                    <option value="all">সকল</option>
                                    <option value="admin">অ্যাডমিন</option>
                                    <option value="teacher">শিক্ষক</option>
                                    <option value="parent">অভিভাবক</option>
                                    <option value="student">ছাত্র</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">প্রকাশের তারিখ</label>
                                <input type="text" data-date-mask="datetime" name="published_at" x-model="editData.published_at"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                    placeholder="dd/mm/yyyy hh:mm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">মেয়াদোত্তীর্ণ</label>
                                <input type="text" data-date-mask="datetime" name="expires_at" x-model="editData.expires_at"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                    placeholder="dd/mm/yyyy hh:mm">
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
                                class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">আপডেট করুন</button>
                        </div>
                    </form>
                </template>
                <template x-if="editLoading">
                    <div class="py-12 text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

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

        function noticeApp() {
            return {
                showCreateModal: false,
                showViewModal: false,
                showEditModal: false,
                viewData: null,
                editData: null,
                viewLoading: false,
                editLoading: false,
                quillCreate: null,
                quillEdit: null,

                createForm: {
                    title: '',
                    content: '',
                    type: '',
                    category: '',
                    target_role: 'all',
                    published_at: '',
                    expires_at: '',
                    is_active: true
                },
                createErrors: {},
                createAttempted: false,

                editErrors: {},
                editAttempted: false,

                get createContentText() {
                    return (this.quillCreate?.getText() || '').trim();
                },
                get editContentText() {
                    return (this.quillEdit?.getText() || '').trim();
                },

                initCreateQuill() {
                    if (this.quillCreate || typeof window.Quill === 'undefined') return;
                    this.quillCreate = new window.Quill('#noticeCreateQuill', {
                        theme: 'snow',
                        placeholder: 'নোটিশের বিস্তারিত বিবরণ লিখুন...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'header': [2, 3, false] }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                [{ 'align': [] }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });
                },

                initEditQuill() {
                    if (this.quillEdit || typeof window.Quill === 'undefined') return;
                    const el = document.getElementById('noticeEditQuill');
                    if (!el) return;
                    this.quillEdit = new window.Quill(el, {
                        theme: 'snow',
                        placeholder: 'নোটিশের বিস্তারিত বিবরণ লিখুন...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'header': [2, 3, false] }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                [{ 'align': [] }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });
                },

                openCreateModal() {
                    this.createForm = {
                        title: '',
                        content: '',
                        type: '',
                        category: '',
                        target_role: 'all',
                        published_at: '',
                        expires_at: '',
                        is_active: true
                    };
                    this.createErrors = {};
                    this.createAttempted = false;
                    this.showCreateModal = true;
                    this.$nextTick(() => {
                        this.initCreateQuill();
                        if (this.quillCreate) this.quillCreate.setContents([]);
                    });
                },

                async openViewModal(id) {
                    this.showViewModal = true;
                    this.viewLoading = true;
                    this.viewData = null;
                    try {
                        const res = await fetch(`{{ url('admin/notices') }}/${id}`);
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
                    this.quillEdit = null;
                    try {
                        const res = await fetch(`{{ url('admin/notices') }}/${id}/edit`);
                        this.editData = await res.json();
                        this.$nextTick(() => {
                            const form = document.getElementById('noticeEditForm');
                            if (form && !form.dataset.maskInit && window.RisDateMask?.init) {
                                form.dataset.maskInit = '1';
                                window.RisDateMask.init(form);
                            }
                            this.initEditQuill();
                            if (this.quillEdit) this.quillEdit.root.innerHTML = this.editData.content || '';
                        });
                    } catch (e) {
                        this.showEditModal = false;
                        alert('তথ্য লোড করতে সমস্যা হয়েছে।');
                    } finally {
                        this.editLoading = false;
                    }
                },

                validateField(field, data, errors, fields) {
                    delete errors[field];
                    const val = data[field];
                    if (fields.includes(field) && (!val || val.trim() === '')) {
                        errors[field] = field === 'category' ? 'ক্যাটাগরি নির্বাচন করুন।' : (field === 'type' ? 'ধরন নির্বাচন করুন।' : 'শিরোনাম আবশ্যক।');
                        return false;
                    }
                    return true;
                },

                validateCreateField(field) {
                    return this.validateField(field, this.createForm, this.createErrors, ['title', 'type', 'category']);
                },

                validateEditField(field) {
                    return this.validateField(field, this.editData, this.editErrors, ['title', 'type', 'category']);
                },

                validateCreateForm(el) {
                    this.createAttempted = true;
                    this.createErrors = {};
                    let valid = true;
                    ['title', 'type', 'category'].forEach(f => {
                        if (!this.validateCreateField(f)) valid = false;
                    });
                    if (this.quillCreate) {
                        const html = this.quillCreate.root.innerHTML;
                        el.querySelector('input[name="content"]').value = html;
                        if (this.createContentText === '') {
                            this.createErrors.content = 'বিবরণ আবশ্যক।';
                            valid = false;
                        }
                    }
                    if (valid) el.submit();
                },

                validateEditForm(el) {
                    this.editAttempted = true;
                    this.editErrors = {};
                    let valid = true;
                    ['title', 'type', 'category'].forEach(f => {
                        if (!this.validateEditField(f)) valid = false;
                    });
                    if (this.quillEdit) {
                        const html = this.quillEdit.root.innerHTML;
                        el.querySelector('input[name="content"]').value = html;
                        if (this.editContentText === '') {
                            this.editErrors.content = 'বিবরণ আবশ্যক।';
                            valid = false;
                        }
                    }
                    if (valid) el.submit();
                }
            }
        }
    </script>
@endsection