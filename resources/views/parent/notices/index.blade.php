@extends('layouts.parent')

@section('title', 'নোটিশ বোর্ড — অভিভাবক পোর্টাল')
@section('page-title', 'নোটিশ বোর্ড')

@section('content')

<div class="space-y-6" x-data="{ expandedNotice: null }">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading font-bold text-xl text-ris-dark">নোটিশ বোর্ড</h2>
            <p class="text-sm text-ris-gray mt-1">সকল নোটিশ ও ঘোষণা দেখুন</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card p-4 sm:p-5">
        <form method="GET" action="{{ route('parent.notices') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
            <div class="flex-1">
                <label class="block text-xs font-medium text-ris-gray mb-1.5">নোটিশের ধরন</label>
                <select name="type" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                    <option value="">সব ধরন</option>
                    <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>সাধারণ</option>
                    <option value="academic" {{ request('type') === 'academic' ? 'selected' : '' }}>শৈক্ষিক</option>
                    <option value="exam" {{ request('type') === 'exam' ? 'selected' : '' }}>পরীক্ষা</option>
                    <option value="event" {{ request('type') === 'event' ? 'selected' : '' }}>অনুষ্ঠান</option>
                    <option value="urgent" {{ request('type') === 'urgent' ? 'selected' : '' }}>জরুরি</option>
                </select>
            </div>
            <button type="submit" class="btn-primary py-2.5 px-5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                খুঁজুন
            </button>
        </form>
    </div>

    {{-- Notices List --}}
    @if(!empty($notices) && count($notices) > 0)
        <div class="space-y-4">
            @foreach($notices as $notice)
                <div class="card overflow-hidden hover:-translate-y-0.5 transition-all" x-data>
                    {{-- Notice Header --}}
                    <div class="p-5 sm:p-6 cursor-pointer" @click="expandedNotice = expandedNotice === {{ $notice->id }} ? null : {{ $notice->id }}">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-heading font-bold text-ris-dark">{{ $notice->title }}</h3>
                                    @if(isset($notice->type))
                                        @php
                                            $typeColors = [
                                                'general' => 'bg-gray-100 text-gray-600',
                                                'academic' => 'bg-blue-100 text-blue-600',
                                                'exam' => 'bg-ris-primary/10 text-ris-primary',
                                                'event' => 'bg-emerald-100 text-emerald-600',
                                                'urgent' => 'bg-red-100 text-red-600',
                                            ];
                                            $typeLabels = [
                                                'general' => 'সাধারণ',
                                                'academic' => 'শৈক্ষিক',
                                                'exam' => 'পরীক্ষা',
                                                'event' => 'অনুষ্ঠান',
                                                'urgent' => 'জরুরি',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center text-[10px] font-heading font-semibold px-2.5 py-1 rounded-full {{ $typeColors[$notice->type] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $typeLabels[$notice->type] ?? $notice->type }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-ris-gray mt-1">{{ $notice->created_at->format('d M, Y') }} · {{ $notice->created_at->diffForHumans() }}</p>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ Str::limit($notice->body ?? $notice->content ?? '', 200) }}</p>
                            </div>
                            <div class="shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="expandedNotice === {{ $notice->id }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Expanded Content --}}
                    <div x-show="expandedNotice === {{ $notice->id }}" x-collapse x-cloak class="border-t border-gray-100">
                        <div class="p-5 sm:p-6 bg-gray-50/50">
                            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($notice->body ?? $notice->content ?? '')) !!}
                            </div>

                            @if(isset($notice->attachment))
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-ris-primary hover:text-ris-dark transition-colors font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        সংযুক্তি ডাউনলোড
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notices->withQueryString()->links() }}
        </div>
    @else
        <div class="card p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <h3 class="font-heading font-bold text-xl text-ris-dark">কোনো নোটিশ নেই</h3>
            <p class="text-ris-gray mt-2">বর্তমানে কোনো নোটিশ প্রকাশিত হয়নি।</p>
        </div>
    @endif

</div>

@endsection
