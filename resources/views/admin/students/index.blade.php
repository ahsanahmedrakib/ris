@extends('layouts.admin')

@section('title', 'ছাত্র/ছাত্রী তালিকা')

@section('content')
    <div class="space-y-6" x-data="studentApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">ছাত্র/ছাত্রী তালিকা</h1>
                <p class="text-sm text-gray-500 mt-1">সকল ছাত্র/ছাত্রীদের তথ্য পরিচালনা করুন</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.download', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel ডাউনলোড
                </a>
                <button @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন ছাত্র/ছাত্রী
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.students.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="নাম বা ভর্তি নং দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল শ্রেণি</option>
                            @foreach ($classes ?? [] as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থা</label>
                        <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল অবস্থা</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>সক্রিয়</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>ডিলিট
                            </option>
                            <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>
                                স্থানান্তরিত</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.students.index') }}"
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
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">ভর্তি নং</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">শ্রেণি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">রোল নং</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">অভিভাবক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">মোবাইল</th>
                            <th class="text-left px-5 py-3.5 font-medium text-white whitespace-nowrap">স্ট্যাটাস</th>
                            <th
                                class="text-center px-5 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">
                                অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($students ?? []) as $index => $student)
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-data="activeRow('{{ url('admin/students') }}', {{ $student->id }}, {{ $student->is_active ? 'true' : 'false' }})">
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900 whitespace-nowrap">{{ $student->admission_no }}</td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="relative w-8 h-8 rounded-full overflow-hidden shrink-0 {{ $student->user?->avatar ? '' : 'bg-ris-primary/10' }} flex items-center justify-center">
                                            @if ($student->user?->avatar)
                                                <img src="{{ Storage::url($student->user->avatar) }}"
                                                    alt="{{ $student->user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span
                                                    class="text-ris-primary text-xs font-semibold">{{ mb_substr($student->user?->name ?? 'ছ', 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <button @click="openViewModal({{ $student->id }})"
                                            class="font-heading font-semibold text-ris-primary hover:underline cursor-pointer">{{ $student->user?->name }}</button>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">
                                    {{ $student->classRoom?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $student->roll_no ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $student->guardian_name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $student->guardian_phone ?? '-' }}</td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                        x-text="active ? 'সক্রিয়' : 'ডিলিট'">{{ $student->is_active ? 'সক্রিয়' : 'ডিলিট' }}</span>
                                </td>
                                <td class="px-5 py-3.5 sticky right-0 bg-white z-10">
                                    <div class="flex items-center justify-center gap-1">
                                        <button x-show="!active" @click="toggle()" title="সক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button x-show="active" @click="toggle()" title="নিষ্ক্রিয় করুন"
                                            class="p-1.5 rounded-lg text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer"
                                            :disabled="busy">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openViewModal({{ $student->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                            title="দেখুন">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('student.id-card', $student) }}" target="_blank"
                                            class="p-1.5 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                            title="ID কার্ড">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5l-2-2z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3m-3 4h2m-5-4H9m2 4H7" />
                                            </svg>
                                        </a>
                                        <button @click="openEditModal({{ $student->id }})"
                                            class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer"
                                            title="সম্পাদনা">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত এই ছাত্রটিকে ডিলিট করতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors cursor-pointer"
                                                title="ডিলিট করুন">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
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
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো ছাত্র/ছাত্রী পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">নতুন ছাত্র/ছাত্রী যোগ করুন বা ফিল্টার পরিবর্তন করুন</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $students])
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
                    <h3 class="font-heading font-bold text-white text-lg">নতুন ছাত্র/ছাত্রী যোগ</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data"
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
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.name || 'নাম আবশ্যক'"></p>
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
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.email || 'ইমেইল আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ফোন</label>
                            <input type="tel" name="phone" x-model="createForm.phone"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ভর্তি নং <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="admission_no" x-model="createForm.admission_no"
                                @blur="validateCreateField('admission_no')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.admission_no || (createAttempted && !createForm.admission_no)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.admission_no || (createAttempted && !createForm.admission_no)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.admission_no || 'ভর্তি নম্বর আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>
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
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.class_id || 'শ্রেণি নির্বাচন আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">সেকশন</label>
                            <input type="text" name="section" x-model="createForm.section"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: ক">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">রোল নং</label>
                            <input type="number" name="roll_no" x-model.number="createForm.roll_no" min="1"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="যেমন: 01">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">লিঙ্গ <span
                                    class="text-red-500">*</span></label>
                            <select name="gender" x-model="createForm.gender"
                                @blur="validateCreateField('gender')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                :class="(createErrors.gender || (createAttempted && !createForm.gender)) ?
                                'border-red-400' : 'border-gray-200'">
                                <option value="">-- নির্বাচন করুন --</option>
                                <option value="male">পুরুষ</option>
                                <option value="female">মহিলা</option>
                                <option value="other">অন্যান্য</option>
                            </select>
                            <template x-if="createErrors.gender || (createAttempted && !createForm.gender)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.gender || 'লিঙ্গ নির্বাচন আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">জন্ম তারিখ <span
                                    class="text-red-500">*</span></label>
                            <input type="text" data-date-mask name="date_of_birth" x-model="createForm.date_of_birth"
                                @blur="validateCreateField('date_of_birth')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.date_of_birth || (createAttempted && !createForm.date_of_birth)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.date_of_birth || (createAttempted && !createForm.date_of_birth)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.date_of_birth || 'জন্ম তারিখ আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">রক্তের গ্রুপ</label>
                            <select name="blood_group" x-model="createForm.blood_group"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ঠিকানা</label>
                            <textarea name="address" rows="2" x-model="createForm.address"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                placeholder="সম্পূর্ণ ঠিকানা লিখুন"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="guardian_name" x-model="createForm.guardian_name"
                                @blur="validateCreateField('guardian_name')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.guardian_name || (createAttempted && !createForm.guardian_name)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.guardian_name || (createAttempted && !createForm.guardian_name)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.guardian_name || 'অভিভাবকের নাম আবশ্যক'"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের মোবাইল <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="guardian_phone" x-model="createForm.guardian_phone"
                                @blur="validateCreateField('guardian_phone')"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                :class="(createErrors.guardian_phone || (createAttempted && !createForm.guardian_phone)) ?
                                'border-red-400' : 'border-gray-200'">
                            <template
                                x-if="createErrors.guardian_phone || (createAttempted && !createForm.guardian_phone)">
                                <p class="mt-1 text-xs text-red-600"
                                    x-text="createErrors.guardian_phone || 'অভিভাবকের ফোন নম্বর আবশ্যক'"></p>
                            </template>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের ইমেইল</label>
                            <input type="email" name="guardian_email" x-model="createForm.guardian_email"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                placeholder="guardian@email.com">
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
                    <h3 class="font-heading font-bold text-white text-lg">ছাত্রের তথ্য</h3>
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
                                    <img :src="viewData.photo" class="w-20 h-20 rounded-full object-cover">
                                </template>
                                <template x-if="!viewData.photo">
                                    <div class="w-20 h-20 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-semibold"
                                        x-text="(viewData.name || 'ছ').charAt(0)"></div>
                                </template>
                                <div>
                                    <h4 class="font-heading font-bold text-lg text-gray-900" x-text="viewData.name">
                                    </h4>
                                    <p class="text-sm text-gray-500" x-text="viewData.admission_no"></p>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                        :class="viewData.is_active ? 'bg-emerald-100 text-emerald-800' :
                                            'bg-red-100 text-red-800'"
                                        x-text="viewData.is_active ? 'সক্রিয়' : 'ডিলিট'"></span>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-gray-500 mb-1">শ্রেণি</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.class || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">সেকশন</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.section || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">রোল নং</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.roll_no || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">লিঙ্গ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.gender || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">জন্ম তারিখ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.date_of_birth || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">রক্তের গ্রুপ</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.blood_group || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">ইমেইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.email || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">মোবাইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.phone || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">অভিভাবকের নাম</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.guardian_name || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">অভিভাবকের মোবাইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.guardian_phone || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">অভিভাবকের ইমেইল</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.guardian_email || '-'"></dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 mb-1">যোগদান</dt>
                                    <dd class="font-medium text-gray-900" x-text="viewData.created_at || '-'"></dd>
                                </div>
                            </dl>
                            <template x-if="viewData.address">
                                <div class="mt-5 pt-5 border-t border-gray-100">
                                    <dt class="text-gray-500 mb-2 text-sm">ঠিকানা</dt>
                                    <dd class="text-sm text-gray-700 whitespace-pre-wrap" x-text="viewData.address"></dd>
                                </div>
                            </template>
                            <div class="mt-5 pt-5 border-t border-gray-100">
                                <h5 class="font-medium text-sm text-gray-900 mb-3">উপস্থিতি সারসংক্ষেপ</h5>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="text-center p-3 bg-emerald-50 rounded-lg">
                                        <p class="text-2xl font-heading font-bold text-emerald-600"
                                            x-text="viewData.attendance_summary.present"></p>
                                        <p class="text-xs text-emerald-600 mt-1">উপস্থিত</p>
                                    </div>
                                    <div class="text-center p-3 bg-red-50 rounded-lg">
                                        <p class="text-2xl font-heading font-bold text-red-600"
                                            x-text="viewData.attendance_summary.absent"></p>
                                        <p class="text-xs text-red-600 mt-1">অনুপস্থিত</p>
                                    </div>
                                    <div class="text-center p-3 bg-amber-50 rounded-lg">
                                        <p class="text-2xl font-heading font-bold text-amber-600"
                                            x-text="viewData.attendance_summary.late"></p>
                                        <p class="text-xs text-amber-600 mt-1">বিলম্বিত</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-2xl font-heading font-bold text-gray-600"
                                            x-text="viewData.attendance_summary.total"></p>
                                        <p class="text-xs text-gray-600 mt-1">মোট</p>
                                    </div>
                                </div>
                            </div>
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
                    <h3 class="font-heading font-bold text-white text-lg">ছাত্র সম্পাদনা</h3>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <template x-if="editData">
                        <form :action="'{{ url('admin/students') }}/' + editData.id" method="POST"
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
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.name || 'নাম আবশ্যক'"></p>
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ফোন</label>
                                    <input type="tel" name="phone" x-model="editData.phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ভর্তি নং <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="admission_no" x-model="editData.admission_no"
                                        @blur="validateEditField('admission_no')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.admission_no || (editAttempted && !editData.admission_no)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template x-if="editErrors.admission_no || (editAttempted && !editData.admission_no)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.admission_no || 'ভর্তি নম্বর আবশ্যক'"></p>
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
                                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-ris-primary/10 file:text-ris-primary hover:file:bg-ris-primary/20">
                                            <p x-show="editData.photo && !editPhotoPreview"
                                                class="mt-1 text-xs text-gray-500">বর্তমান ছবি আছে। নতুন দিলে পুরোনো মুছে
                                                যাবে।</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি <span
                                            class="text-red-500">*</span></label>
                                    <select name="class_id" @blur="validateEditField('class_id')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="(editErrors.class_id || (editAttempted && !editData.class_id)) ?
                                        'border-red-400' : 'border-gray-200'">
                                        <option value="">-- নির্বাচন করুন --</option>
                                        @foreach ($classes as $class)
                                            <option :value="'{{ $class->id }}'"
                                                :selected="editData.class_id == '{{ $class->id }}'">{{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <template x-if="editErrors.class_id || (editAttempted && !editData.class_id)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.class_id || 'শ্রেণি নির্বাচন আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">সেকশন</label>
                                    <input type="text" name="section" x-model="editData.section"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="যেমন: ক">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">রোল নং</label>
                                    <input type="number" name="roll_no" x-model.number="editData.roll_no" min="1"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="যেমন: 01">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">লিঙ্গ <span
                                            class="text-red-500">*</span></label>
                                    <select name="gender" x-model="editData.gender"
                                        @blur="validateEditField('gender')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white"
                                        :class="(editErrors.gender || (editAttempted && !editData.gender)) ?
                                        'border-red-400' : 'border-gray-200'">
                                        <option value="">-- নির্বাচন করুন --</option>
                                        <option value="male" :selected="editData.gender === 'male'">পুরুষ</option>
                                        <option value="female" :selected="editData.gender === 'female'">মহিলা</option>
                                        <option value="other" :selected="editData.gender === 'other'">অন্যান্য</option>
                                    </select>
                                    <template x-if="editErrors.gender || (editAttempted && !editData.gender)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.gender || 'লিঙ্গ নির্বাচন আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">জন্ম তারিখ <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" data-date-mask name="date_of_birth"
                                        x-model="editData.date_of_birth" @blur="validateEditField('date_of_birth')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.date_of_birth || (editAttempted && !editData.date_of_birth)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.date_of_birth || (editAttempted && !editData.date_of_birth)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.date_of_birth || 'জন্ম তারিখ আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">রক্তের গ্রুপ</label>
                                    <select name="blood_group"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="">-- নির্বাচন করুন --</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                            <option :value="'{{ $group }}'"
                                                :selected="editData.blood_group === '{{ $group }}'">{{ $group }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস</label>
                                    <select name="is_active"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                        <option value="1" :selected="editData.is_active">সক্রিয়</option>
                                        <option value="0" :selected="!editData.is_active">ডিলিট</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ঠিকানা</label>
                                    <textarea name="address" rows="2" x-model="editData.address"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none"
                                        placeholder="সম্পূর্ণ ঠিকানা লিখুন"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের নাম <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="guardian_name" x-model="editData.guardian_name"
                                        @blur="validateEditField('guardian_name')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.guardian_name || (editAttempted && !editData.guardian_name)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.guardian_name || (editAttempted && !editData.guardian_name)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.guardian_name || 'অভিভাবকের নাম আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের মোবাইল <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="guardian_phone" x-model="editData.guardian_phone"
                                        @blur="validateEditField('guardian_phone')"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        :class="(editErrors.guardian_phone || (editAttempted && !editData.guardian_phone)) ?
                                        'border-red-400' : 'border-gray-200'">
                                    <template
                                        x-if="editErrors.guardian_phone || (editAttempted && !editData.guardian_phone)">
                                        <p class="mt-1 text-xs text-red-600"
                                            x-text="editErrors.guardian_phone || 'অভিভাবকের ফোন নম্বর আবশ্যক'"></p>
                                    </template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">অভিভাবকের ইমেইল</label>
                                    <input type="email" name="guardian_email" x-model="editData.guardian_email"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors"
                                        placeholder="guardian@email.com">
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

            function studentApp() {
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
                        admission_no: '',
                        class_id: '',
                        section: '',
                        roll_no: '',
                        gender: '',
                        date_of_birth: '',
                        blood_group: '',
                        address: '',
                        guardian_name: '',
                        guardian_phone: '',
                        guardian_email: '',
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
                            admission_no: '',
                            class_id: '',
                            section: '',
                            roll_no: '',
                            gender: '',
                            date_of_birth: '',
                            blood_group: '',
                            address: '',
                            guardian_name: '',
                            guardian_phone: '',
                            guardian_email: '',
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
                            const res = await fetch(`{{ url('admin/students') }}/${id}`);
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
                            const res = await fetch(`{{ url('admin/students') }}/${id}/edit`);
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
                            admission_no: 'ভর্তি নম্বর',
                            class_id: 'শ্রেণি',
                            date_of_birth: 'জন্ম তারিখ',
                            gender: 'লিঙ্গ',
                            guardian_name: 'অভিভাবকের নাম',
                            guardian_phone: 'অভিভাবকের ফোন নম্বর',
                        };

                        if (requiredFields[field] && (val === null || val === undefined || String(val).trim() === '')) {
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
                        ['name', 'email', 'admission_no', 'class_id', 'date_of_birth', 'gender', 'guardian_name',
                            'guardian_phone'
                        ].forEach(f => {
                            if (!this.validateCreateField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    },

                    validateEditForm(el) {
                        this.editAttempted = true;
                        this.editErrors = {};
                        let valid = true;
                        ['name', 'email', 'admission_no', 'class_id', 'date_of_birth', 'gender', 'guardian_name',
                            'guardian_phone'
                        ].forEach(f => {
                            if (!this.validateEditField(f)) valid = false;
                        });
                        if (valid) el.submit();
                    }
                }
            }
        </script>
    @endsection
@endsection