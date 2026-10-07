@extends('layouts.website')

@section('content')
    {{-- Hero --}}
    <section class="relative bg-cover bg-center overflow-hidden py-16 sm:py-20"
        style="background-image: url('{{ asset('assets/banner.png') }}')">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light opacity-90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের শিক্ষকবৃন্দ</h1>
            <p class="mt-3 text-white/70 text-lg">অভিজ্ঞ ও নিবেদিত শিক্ষকদের দল</p>
        </div>
    </section>

    {{-- Teachers Grid --}}
    <section class="py-16 sm:py-20 bg-white relative overflow-hidden">
        <div class="absolute top-10 right-10 w-32 h-32 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($teachers as $teacher)
                    @include('website.partials.teacher-card', ['teacher' => $teacher])
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 font-medium">শীঘ্রই আমাদের শিক্ষকদের তথ্য যোগ করা হবে।</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
