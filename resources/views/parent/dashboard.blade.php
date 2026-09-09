@extends('layouts.parent')

@section('title', 'ড্যাশবোর্ড — অভিভাবক পোর্টাল')
@section('page-title', 'ড্যাশবোর্ড')

@section('content')

    <div class="space-y-8">

        {{-- Welcome Banner --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-linear-to-r from-ris-dark via-ris-primary to-ris-light p-6 sm:p-8">
            <div class="relative z-10">
                <h2 class="font-heading font-bold text-2xl sm:text-3xl text-white">স্বাগতম,
                    {{ Auth::user()->name ?? 'অভিভাবক' }}</h2>
                <p class="mt-2 text-white/70 text-sm sm:text-base">রেশমা ইন্টারন্যাশনাল স্কুল অভিভাবক পোর্টালে আপনাকে
                    স্বাগতম।</p>
            </div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 right-10 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        </div>

        {{-- Quick Links --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('parent.attendance') }}"
                class="card p-5 flex items-center gap-4 group hover:-translate-y-0.5 transition-all">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-500 transition-colors">
                    <svg class="w-6 h-6 text-emerald-500 group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <p class="font-heading font-semibold text-sm text-ris-dark">উপস্থিতি দেখুন</p>
                    <p class="text-xs text-ris-gray mt-0.5">সন্তানের উপস্থিতি রেকর্ড</p>
                </div>
            </a>

            <a href="{{ route('parent.fees') }}"
                class="card p-5 flex items-center gap-4 group hover:-translate-y-0.5 transition-all">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-500 transition-colors">
                    <svg class="w-6 h-6 text-amber-500 group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-heading font-semibold text-sm text-ris-dark">ফি দেখুন</p>
                    <p class="text-xs text-ris-gray mt-0.5">বকেয়া ও পরিশোধের অবস্থা</p>
                </div>
            </a>

            <a href="{{ route('parent.notices') }}"
                class="card p-5 flex items-center gap-4 group hover:-translate-y-0.5 transition-all">
                <div
                    class="w-12 h-12 rounded-xl bg-ris-primary/10 flex items-center justify-center group-hover:bg-ris-primary transition-colors">
                    <svg class="w-6 h-6 text-ris-primary group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <div>
                    <p class="font-heading font-semibold text-sm text-ris-dark">নোটিশ দেখুন</p>
                    <p class="text-xs text-ris-gray mt-0.5">সর্বশেষ ঘোষণা ও নোটিশ</p>
                </div>
            </a>
        </div>

        {{-- Children Cards --}}
        <div>
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-heading font-bold text-lg text-ris-dark">আমার সন্তান</h3>
                <a href="{{ route('parent.children') }}"
                    class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">সব দেখুন →</a>
            </div>

            @if (!empty($children) && count($children) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($children as $child)
                        <div class="card overflow-hidden hover:-translate-y-0.5 transition-all">
                            {{-- Card Header --}}
                            <div class="bg-linear-to-r from-ris-dark to-ris-primary p-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-white font-heading font-bold text-xl shrink-0">
                                        {{ substr($child->name ?? 'ছাত্র', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-heading font-bold text-white text-lg truncate">{{ $child->name }}
                                        </h4>
                                        <p class="text-white/70 text-sm">{{ $child->class->name ?? '' }} ·
                                            {{ $child->section->name ?? '' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 space-y-4">
                                {{-- Info Row --}}
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-ris-gray">রোল নং</span>
                                    <span class="font-medium text-gray-800">{{ $child->roll_no ?? '—' }}</span>
                                </div>

                                {{-- Attendance --}}
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-ris-gray">আজকের উপস্থিতি</span>
                                    @if (isset($child->today_attendance))
                                        @if ($child->today_attendance === 'present')
                                            <span class="badge-success badge text-xs">উপস্থিত</span>
                                        @elseif($child->today_attendance === 'absent')
                                            <span class="badge-danger badge text-xs">অনুপস্থিত</span>
                                        @elseif($child->today_attendance === 'late')
                                            <span class="badge-warning badge text-xs">বিলম্বিত</span>
                                        @else
                                            <span class="badge text-xs">—</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-ris-gray">তথ্য নেই</span>
                                    @endif
                                </div>

                                {{-- Recent Exam Result --}}
                                @if (isset($child->recent_result))
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-ris-gray">সর্বশেষ ফলাফল</span>
                                        <span class="font-medium text-gray-800">{{ $child->recent_result }}</span>
                                    </div>
                                @endif

                                {{-- Pending Fees --}}
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-ris-gray">বকেয়া ফি</span>
                                    @if (isset($child->pending_fees) && $child->pending_fees > 0)
                                        <span
                                            class="font-semibold text-ris-light">৳{{ number_format($child->pending_fees, 2) }}</span>
                                    @else
                                        <span class="text-emerald-600 font-medium text-sm">পরিশোধিত</span>
                                    @endif
                                </div>

                                <hr class="border-gray-100">

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('parent.children.show', $child->id) }}"
                                        class="flex-1 btn-primary text-xs py-2 px-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        বিস্তারিত
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card p-10 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="font-heading font-bold text-ris-dark">কোনো সন্তান পাওয়া যায়নি</h4>
                    <p class="text-sm text-ris-gray mt-1">আপনার অ্যাকাউন্টে কোনো সন্তান যুক্ত করা হয়নি।</p>
                </div>
            @endif
        </div>

        {{-- Recent Notices --}}
        <div>
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-heading font-bold text-lg text-ris-dark">সর্বশেষ নোটিশ</h3>
                <a href="{{ route('parent.notices') }}"
                    class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">সব দেখুন →</a>
            </div>

            @if (!empty($notices) && count($notices) > 0)
                <div class="space-y-3">
                    @foreach ($notices->take(5) as $notice)
                        <div class="card p-4 sm:p-5 flex items-start gap-4">
                            <div class="w-10 h-10 shrink-0 rounded-lg bg-ris-primary/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-heading font-semibold text-sm text-ris-dark">{{ $notice->title }}</h4>
                                    @if (isset($notice->type))
                                        <span class="badge text-[10px] px-2 py-0.5">{{ $notice->type }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                    {{ Str::limit($notice->body ?? ($notice->content ?? ''), 150) }}</p>
                                <p class="text-xs text-ris-gray mt-2">{{ $notice->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card p-8 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h4 class="font-heading font-bold text-ris-dark text-sm">কোনো নোটিশ নেই</h4>
                    <p class="text-xs text-ris-gray mt-1">বর্তমানে কোনো নোটিশ প্রকাশিত হয়নি।</p>
                </div>
            @endif
        </div>

    </div>

@endsection
