@extends('layouts.website')

@section('title', ($student->user?->name ?? 'ছাত্র') . ' — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">{{ $student->user?->name ?? '-' }}</h1>
            <p class="mt-3 text-white/70 text-lg">
                শ্রেণি: {{ $student->classRoom?->name ?? '-' }}
                @if($student->section)
                    ({{ $student->section }})
                @endif
            </p>
        </div>
    </section>

    {{-- Student Profile --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-10">
                {{-- Left: Photo + Identity --}}
                <div class="text-center lg:text-left">
                    @if($student->user?->avatar)
                        <img src="{{ Storage::url($student->user->avatar) }}" alt="{{ $student->user->name }}"
                            class="w-48 h-56 rounded-2xl object-cover mx-auto lg:mx-0 shadow-lg">
                    @else
                        <div
                            class="w-48 h-56 rounded-2xl bg-ris-primary/10 flex items-center justify-center text-ris-primary text-6xl font-semibold mx-auto lg:mx-0 shadow-lg">
                            {{ mb_substr($student->user?->name ?? '?', 0, 1) }}
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col items-center lg:items-start gap-2">
                        <h2 class="font-heading font-bold text-2xl text-ris-dark">{{ $student->user?->name ?? '-' }}</h2>
                        <p class="text-ris-primary font-medium">আইডি: {{ $student->id }}</p>
                        <p class="text-sm text-gray-500">ভর্তি নং: {{ $student->admission_no ?? '-' }}</p>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $student->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                            {{ $student->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                        </span>
                    </div>
                </div>

                {{-- Right: Details --}}
                <div class="lg:col-span-2 space-y-8">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">ব্যক্তিগত তথ্য</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">শ্রেণি</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->classRoom?->name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">সেকশন</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->section ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">রোল নং</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->roll_no ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">জন্ম তারিখ</dt>
                                <dd class="font-medium text-gray-900 mt-1">
                                    {{ $student->date_of_birth?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">লিঙ্গ</dt>
                                <dd class="font-medium text-gray-900 mt-1">
                                    @php
                                        $gender = match ($student->gender) {
                                            'male' => 'পুরুষ',
                                            'female' => 'মহিলা',
                                            'other' => 'অন্যান্য',
                                            default => null,
                                        };
                                    @endphp
                                    {{ $gender ?? '-' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">রক্তের গ্রুপ</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->blood_group ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                                <dt class="text-sm text-gray-500">ঠিকানা</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->address ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">যোগাযোগ</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">মোবাইল নম্বর</dt>
                                <dd class="font-medium text-gray-900 mt-1">
                                    @if($student->user?->phone)
                                        <a href="tel:{{ $student->user->phone }}"
                                            class="hover:text-ris-primary">{{ $student->user->phone }}</a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">ইমেইল</dt>
                                <dd class="font-medium text-gray-900 mt-1 break-all">
                                    @if($student->user?->email)
                                        <a href="mailto:{{ $student->user->email }}"
                                            class="hover:text-ris-primary">{{ $student->user->email }}</a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">অভিভাবকের তথ্য</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">নাম</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $student->guardian_name ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">মোবাইল নম্বর</dt>
                                <dd class="font-medium text-gray-900 mt-1">
                                    @if($student->guardian_phone)
                                        <a href="tel:{{ $student->guardian_phone }}"
                                            class="hover:text-ris-primary">{{ $student->guardian_phone }}</a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                                <dt class="text-sm text-gray-500">ইমেইল</dt>
                                <dd class="font-medium text-gray-900 mt-1 break-all">
                                    @if($student->guardian_email)
                                        <a href="mailto:{{ $student->guardian_email }}"
                                            class="hover:text-ris-primary">{{ $student->guardian_email }}</a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if($student->bus)
                        <div>
                            <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">পরিবহন</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <dt class="text-sm text-gray-500">বাস নম্বর</dt>
                                    <dd class="font-medium text-gray-900 mt-1">{{ $student->bus->bus_no ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <dt class="text-sm text-gray-500">রুট</dt>
                                    <dd class="font-medium text-gray-900 mt-1">{{ $student->bus->route_name ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <dt class="text-sm text-gray-500">চালকের নাম</dt>
                                    <dd class="font-medium text-gray-900 mt-1">{{ $student->bus->driver_name ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <dt class="text-sm text-gray-500">চালকের মোবাইল</dt>
                                    <dd class="font-medium text-gray-900 mt-1">{{ $student->bus->driver_phone ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
