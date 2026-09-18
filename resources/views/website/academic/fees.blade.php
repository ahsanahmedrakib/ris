@extends('layouts.website')

@section('title', 'টিউশন ফি — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">টিউশন ফি</h1>
            <p class="mt-3 text-white/70 text-lg">শ্রেণি অনুযায়ী ফি কাঠামো</p>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($academicYear)
                <div class="mb-8 text-center reveal">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-ris-primary/10 text-ris-primary rounded-full text-sm font-heading font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        বর্তমান শিক্ষাবর্ষ: {{ $academicYear->yearLabel() }}
                    </span>
                </div>
            @endif

            @if ($classes->count() && $feeTypes->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden reveal">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[640px]">
                            <thead>
                                <tr class="bg-ris-dark text-white">
                                    <th class="text-left px-5 py-4 font-heading font-semibold whitespace-nowrap">শ্রেণি</th>
                                    @foreach ($feeTypes as $type)
                                        <th class="text-right px-5 py-4 font-heading font-semibold whitespace-nowrap">
                                            {{ $type->label() }}
                                        </th>
                                    @endforeach
                                    <th class="text-right px-5 py-4 font-heading font-semibold whitespace-nowrap">মোট</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($classes as $class)
                                    <tr class="hover:bg-ris-primary/5 transition-colors">
                                        <td class="px-5 py-4 font-medium text-ris-dark whitespace-nowrap">
                                            {{ $class->name }}
                                            @if ($class->section)
                                                <span class="text-gray-400 font-normal">({{ $class->section }})</span>
                                            @endif
                                        </td>
                                        @foreach ($feeTypes as $type)
                                            @php
                                                $structure = $class->feeStructures->firstWhere('fee_type', $type->value);
                                            @endphp
                                            <td class="px-5 py-4 text-right {{ $structure ? 'text-gray-700' : 'text-gray-300' }}">
                                                {{ $structure ? '৳'.number_format($structure->amount) : '—' }}
                                            </td>
                                        @endforeach
                                        <td class="px-5 py-4 text-right font-heading font-bold text-ris-primary">
                                            ৳{{ number_format($class->feeStructures->sum('amount')) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 border-t border-gray-100">
                                    <td class="px-5 py-4 font-heading font-bold text-ris-dark">সর্বমোট</td>
                                    @foreach ($feeTypes as $type)
                                        <td class="px-5 py-4 text-right font-semibold text-gray-700">
                                            ৳{{ number_format($classes->sum(fn ($class) => $class->feeStructures->firstWhere('fee_type', $type->value)?->amount ?? 0)) }}
                                        </td>
                                    @endforeach
                                    <td class="px-5 py-4 text-right font-heading font-bold text-ris-primary text-base">
                                        ৳{{ number_format($classes->sum(fn ($class) => $class->feeStructures->sum('amount'))) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-400 text-center">সকল পরিমাণ বাংলাদেশি টাকায় (৳)। ফি বছরভিত্তিক পরিবর্তন হতে পারে।</p>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো ফি কাঠামো নেই</h3>
                    <p class="mt-2 text-gray-500">শ্রেণি অনুযায়ী ফি শীঘ্রই যোগ করা হবে।</p>
                </div>
            @endif
        </div>
    </section>

@endsection