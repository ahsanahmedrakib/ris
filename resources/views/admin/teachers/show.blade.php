@extends('layouts.admin')

@section('title', $teacher->name)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.teachers.index') }}"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $teacher->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $teacher->teacherProfile?->designation ?? 'শিক্ষক' }}</p>
            </div>
            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                সম্পাদনা
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-center">
                    @if ($teacher->teacherProfile?->photo)
                        <img src="{{ Storage::url($teacher->teacherProfile->photo) }}" alt="{{ $teacher->name }}"
                            class="w-32 h-32 rounded-full object-cover mx-auto">
                    @else
                        <div
                            class="w-32 h-32 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-3xl font-semibold mx-auto">
                            {{ mb_substr($teacher->name, 0, 1) }}
                        </div>
                    @endif
                    <h2 class="mt-4 text-xl font-heading font-bold text-gray-900">{{ $teacher->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $teacher->teacherProfile?->designation ?? '-' }}</p>
                    <div class="mt-2">
                        @if ($teacher->is_active)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">সক্রিয়</span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">ডিলিট</span>
                        @endif
                        @if ($teacher->teacherProfile?->is_featured)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">ফিচার্ড</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ $teacher->email }}
                    </div>
                    @if ($teacher->phone)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ $teacher->phone }}
                        </div>
                    @endif
                    @if ($teacher->teacherProfile?->joining_date)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            যোগদান: {{ $teacher->teacherProfile->joining_date->format('d/m/Y') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="lg:col-span-2 space-y-6">
                @if ($teacher->teacherProfile?->bio)
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">পরিচিতি</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $teacher->teacherProfile->bio }}</p>
                    </div>
                @endif

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">শিক্ষাগত ও পেশাদার তথ্য
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500 mb-1">বিষয়</dt>
                            <dd class="font-medium text-gray-900">{{ $teacher->teacherProfile?->subject ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">যোগ্যতা</dt>
                            <dd class="font-medium text-gray-900">{{ $teacher->teacherProfile?->qualification ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">শিক্ষাপ্রতিষ্ঠান</dt>
                            <dd class="font-medium text-gray-900">{{ $teacher->teacherProfile?->institute ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">পদবি</dt>
                            <dd class="font-medium text-gray-900">{{ $teacher->teacherProfile?->designation ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($teacher->teacherProfile?->previous_institutions)
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">পূর্ববর্তী
                            প্রতিষ্ঠানসমূহ</h3>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">
                            {{ $teacher->teacherProfile->previous_institutions }}</p>
                    </div>
                @endif

                @if ($teacher->teacherProfile?->experience)
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">অভিজ্ঞতা</h3>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">
                            {{ $teacher->teacherProfile->experience }}</p>
                    </div>
                @endif

                @if ($teacher->teacherProfile?->achievements)
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">অর্জনসমূহ</h3>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">
                            {{ $teacher->teacherProfile->achievements }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
