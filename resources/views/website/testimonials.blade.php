@extends('layouts.website')

@section('title', 'টেস্টিমোনিয়াল — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের পরিবারের কথা</h1>
            <p class="mt-3 text-white/70 text-lg">অভিভাবক ও শিক্ষার্থীদের মূল্যবান মতামত</p>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($testimonials->count())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 reveal-stagger">
                    @foreach ($testimonials as $testimonial)
                        <div class="h-full reveal">
                            <div
                                class="testimonial-card group relative h-full flex flex-col overflow-hidden rounded-[1.6rem] bg-linear-to-br from-amber-50 via-orange-50/70 to-rose-50 border border-amber-100 px-6 sm:px-7 py-6 sm:py-7 shadow-card hover:shadow-card-hover transition-all duration-300">
                                <div
                                    class="absolute top-5 -left-9 w-40 h-7 gradient-logo -rotate-45 flex items-center justify-center shadow-md z-10">
                                    <span
                                        class="text-[10px] font-heading font-semibold text-white uppercase tracking-widest">Review</span>
                                </div>
                                <div
                                    class="absolute top-7 right-6 text-4xl leading-none select-none pointer-events-none text-amber-300/40">
                                    ✦</div>
                                <div
                                    class="absolute bottom-24 left-5 text-2xl leading-none select-none pointer-events-none text-ris-accent/15">
                                    ✦</div>
                                <div
                                    class="w-11 h-11 gradient-logo rounded-2xl flex items-center justify-center shadow-lg rotate-6 transition-transform duration-300 group-hover:rotate-0 mt-8 mb-4">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 leading-relaxed italic flex-1 line-clamp-4 min-h-26"
                                    title="{{ $testimonial->message }}">"{{ $testimonial->message }}"</p>
                                <div class="pt-4 mt-6 border-t border-dashed border-amber-200 flex items-center gap-4 relative">
                                    @if ($testimonial->photo)
                                        <img src="{{ Storage::url($testimonial->photo) }}" alt="{{ $testimonial->name }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-amber-200 shadow-sm shrink-0">
                                    @else
                                        <div
                                            class="w-12 h-12 rounded-full bg-linear-to-br from-amber-200 to-orange-200 border-2 border-amber-300 flex items-center justify-center text-amber-900 font-semibold text-lg shrink-0">
                                            {{ mb_substr($testimonial->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-heading font-bold text-sm text-amber-900 truncate">
                                            {{ $testimonial->name }}</p>
                                        @if ($testimonial->designation)
                                            <p class="text-xs text-amber-700/70">{{ $testimonial->designation }}</p>
                                        @endif
                                    </div>
                                    <div
                                        class="flex items-center gap-0.5 bg-white/80 border border-amber-200 rounded-full px-2.5 py-1.5 shadow-sm shrink-0">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if ($i < $testimonial->rating)
                                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-300" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $testimonials->links() }}
                </div>

                <div class="mt-10 text-center flex flex-col sm:flex-row items-center justify-center gap-4">
                    @include('website.partials.testimonial-submit')
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো টেস্টিমোনিয়াল নেই</h3>
                    <p class="mt-2 text-gray-500">শীঘ্রই আমাদের অভিভাবকদের মতামত যোগ করা হবে।</p>
                </div>
            @endif
        </div>
    </section>

@endsection