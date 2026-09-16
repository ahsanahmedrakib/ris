@extends('layouts.website')

@section('title', 'নোটিশ — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">নোটিশ বোর্ড</h1>
            <p class="mt-3 text-white/70 text-lg">সর্বশেষ ঘোষণা ও নোটিশসমূহ</p>
        </div>
    </section>

    {{-- Notices --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (!empty($notices) && $notices->count() > 0)
                <div class="space-y-6 reveal-stagger">
                    @foreach ($notices as $notice)
                        <div class="card p-6 sm:p-8 group hover:-translate-y-0.5 reveal">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-14 h-14 shrink-0 rounded-2xl bg-ris-primary/10 flex items-center justify-center group-hover:bg-ris-primary transition-all duration-300">
                                    <svg class="w-7 h-7 text-ris-primary group-hover:text-white transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-heading font-bold text-lg text-ris-dark">{{ $notice->title }}</h3>
                                    <p class="mt-1 text-sm text-ris-gray">{{ $notice->created_at->format('d M, Y') }}</p>
                                    <div class="mt-3 text-gray-600 leading-relaxed prose prose-sm max-w-none">{!! $notice->content !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $notices->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">কোনো নোটিশ নেই</h3>
                    <p class="mt-2 text-gray-500">বর্তমানে কোনো নোটিশ প্রকাশিত হয়নি।</p>
                </div>
            @endif
        </div>
    </section>

@endsection
