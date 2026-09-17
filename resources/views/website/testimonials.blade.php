@extends('layouts.website')

@section('title', 'শুভকামনা ও মতামত — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের পরিবারের কথা</h1>
            <p class="mt-3 text-white/70 text-lg">অভিভাবক ও শিক্ষার্থীদের মূল্যবান মতামত</p>
        </div>
    </section>

    {{-- Rating summary --}}
    @if ($testimonialStats['total'] > 0)
        <section class="bg-white pt-12 sm:pt-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 reveal">
                    <div
                        class="inline-flex items-center gap-3.5 px-6 py-4 rounded-2xl bg-white border border-amber-100 shadow-card">
                        <div class="w-12 h-12 rounded-xl bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-heading font-bold text-3xl leading-none text-amber-600">★ {{ $testimonialStats['bangla_average'] }}</p>
                            <p class="text-xs text-gray-500 mt-1.5">গড় রেটিং</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm font-medium shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        প্রশাসক কর্তৃক যাচাইকৃত
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        @include('website.partials.decorative-shapes')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($testimonials->count())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 auto-rows-fr reveal-stagger">
                    @foreach ($testimonials as $testimonial)
                        <div class="h-full reveal">
                            @include('website.partials.testimonial-card', ['testimonial' => $testimonial])
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
                    <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো মতামত নেই</h3>
                    <p class="mt-2 text-gray-500">শীঘ্রই আমাদের অভিভাবকদের মতামত যোগ করা হবে।</p>
                </div>
            @endif
        </div>
    </section>

@endsection