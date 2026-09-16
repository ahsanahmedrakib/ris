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
                        বর্তমান শিক্ষাবর্ষ: {{ $academicYear->name }}
                    </span>
                </div>
            @endif

            @php
                $classesWithFees = $classes->filter(fn ($class) => $class->feeStructures->count());
            @endphp

            @if ($classesWithFees->count())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                    @foreach ($classesWithFees as $class)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden reveal">
                            <div class="bg-ris-primary text-white px-6 py-4">
                                <h3 class="font-heading font-bold text-lg">{{ $class->name }}</h3>
                                @if ($class->section)
                                    <p class="text-white/70 text-sm">সেকশন: {{ $class->section }}</p>
                                @endif
                            </div>
                            <div class="p-5">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="text-left py-2 text-gray-500 font-medium">ফি ধরন</th>
                                            <th class="text-right py-2 text-gray-500 font-medium">পরিমাণ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $typeLabels = [
                                                'tuition' => 'টিউশন ফি',
                                                'transport' => 'পরিবহন',
                                                'library' => 'লাইব্রেরি',
                                                'exam' => 'পরীক্ষা',
                                                'others' => 'অন্যান্য',
                                            ];
                                        @endphp
                                        @foreach ($class->feeStructures as $structure)
                                            <tr class="border-b border-gray-50">
                                                <td class="py-2.5 text-gray-700">{{ $typeLabels[$structure->fee_type] ?? $structure->fee_type }}</td>
                                                <td class="py-2.5 text-right font-semibold text-ris-dark">
                                                    ৳{{ number_format($structure->amount) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="pt-3 font-heading font-bold text-ris-dark">মোট</td>
                                            <td class="pt-3 text-right font-heading font-bold text-ris-primary text-base">
                                                ৳{{ number_format($class->feeStructures->sum('amount')) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                @if ($class->feeStructures->first()->due_date)
                                    <p class="mt-3 text-xs text-gray-400">পেমেন্টের শেষ তারিখ: {{ $class->feeStructures->first()->due_date->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
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