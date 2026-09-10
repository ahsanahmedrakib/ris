@extends('layouts.parent')

@section('title', 'আমার সন্তান — অভিভাবক পোর্টাল')
@section('page-title', 'আমার সন্তান')

@section('content')

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-bold text-xl text-ris-dark">আমার সন্তান</h2>
                <p class="text-sm text-ris-gray mt-1">আপনার সকল সন্তানের তথ্য দেখুন</p>
            </div>
        </div>

        {{-- Children Cards --}}
        @if (!empty($children) && count($children) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($children as $child)
                    <div class="card overflow-hidden hover:-translate-y-0.5 transition-all group">
                        {{-- Card Header --}}
                        <div class="bg-linear-to-r from-ris-dark via-ris-primary to-ris-light p-6 relative">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2">
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                {{-- Photo Placeholder --}}
                                <div
                                    class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-heading font-bold text-2xl shrink-0">
                                    @if (isset($child->photo))
                                        <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}"
                                            class="w-full h-full rounded-full object-cover">
                                    @else
                                        {{ substr($child->name ?? 'ছ', 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-heading font-bold text-white text-lg leading-tight">{{ $child->name }}
                                    </h3>
                                    <p class="text-white/70 text-sm mt-0.5">{{ $child->class->name ?? '' }} ·
                                        {{ $child->section->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5 space-y-3">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-ris-gray text-xs">শ্রেণি</p>
                                    <p class="font-medium text-gray-800 mt-0.5">{{ $child->class->name ?? '—' }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-ris-gray text-xs">শাখা</p>
                                    <p class="font-medium text-gray-800 mt-0.5">{{ $child->section->name ?? '—' }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-ris-gray text-xs">রোল নং</p>
                                    <p class="font-medium text-gray-800 mt-0.5">{{ $child->roll_no ?? '—' }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-ris-gray text-xs">ভর্তি নং</p>
                                    <p class="font-medium text-gray-800 mt-0.5">{{ $child->admission_no ?? '—' }}</p>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Quick Links --}}
                            <div class="grid grid-cols-3 gap-2">
                                <a href="{{ route('parent.attendance', ['child_id' => $child->id]) }}"
                                    class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg hover:bg-emerald-50 transition-colors group/link">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center group-hover/link:bg-emerald-500 transition-colors">
                                        <svg class="w-4 h-4 text-emerald-500 group-hover/link:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-ris-gray group-hover/link:text-emerald-600 transition-colors">উপস্থিতি</span>
                                </a>

                                <a href="{{ route('parent.fees', ['child_id' => $child->id]) }}"
                                    class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg hover:bg-amber-50 transition-colors group/link">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center group-hover/link:bg-amber-500 transition-colors">
                                        <svg class="w-4 h-4 text-amber-500 group-hover/link:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-ris-gray group-hover/link:text-amber-600 transition-colors">ফি</span>
                                </a>

                                <a href="{{ route('parent.exams', ['child_id' => $child->id]) }}"
                                    class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg hover:bg-ris-primary/5 transition-colors group/link">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-ris-primary/10 flex items-center justify-center group-hover/link:bg-ris-primary transition-colors">
                                        <svg class="w-4 h-4 text-ris-primary group-hover/link:text-white transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-ris-gray group-hover/link:text-ris-primary transition-colors">ফলাফল</span>
                                </a>
                            </div>

                            <a href="{{ route('parent.children.show', $child->id) }}"
                                class="block w-full text-center btn-primary text-sm py-2.5 mt-2">
                                বিস্তারিত দেখুন
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-xl text-ris-dark">কোনো সন্তান পাওয়া যায়নি</h3>
                <p class="text-ris-gray mt-2 max-w-md mx-auto">আপনার অ্যাকাউন্টে এখনো কোনো সন্তান যুক্ত করা হয়নি। অ্যাডমিন
                    প্যানেল থেকে সন্তান যুক্ত করুন।</p>
            </div>
        @endif

    </div>

@endsection
