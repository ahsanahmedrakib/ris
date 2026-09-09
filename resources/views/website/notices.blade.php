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
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

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
                                    <p class="mt-3 text-gray-600 leading-relaxed">{{ Str::limit($notice->body, 200) }}</p>
                                    @if (isset($notice->attachment))
                                        <a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 mt-3 text-sm text-ris-primary hover:text-ris-dark transition-colors font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            সংযুক্তি ডাউনলোড
                                        </a>
                                    @endif
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
