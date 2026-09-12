@extends('layouts.website')

@section('title', 'আমাদের শিক্ষকবৃন্দ — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের শিক্ষকবৃন্দ</h1>
            <p class="mt-3 text-white/70 text-lg">অভিজ্ঞ ও নিবেদিত শিক্ষকদের দল</p>
        </div>
    </section>

    {{-- Teachers Grid --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($teachers as $teacher)
                    <a href="{{ route('teacher.single', $teacher->teacherProfile->slug) }}" class="group">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 text-center p-6">
                            @if($teacher->teacherProfile?->photo)
                                <img src="{{ Storage::url($teacher->teacherProfile->photo) }}" alt="{{ $teacher->name }}" class="w-28 h-28 rounded-full object-cover mx-auto group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-28 h-28 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-3xl font-semibold mx-auto group-hover:scale-105 transition-transform duration-300">
                                    {{ mb_substr($teacher->name, 0, 1) }}
                                </div>
                            @endif
                            <h3 class="mt-4 font-heading font-bold text-lg text-ris-dark group-hover:text-ris-primary transition-colors">{{ $teacher->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $teacher->teacherProfile?->subject ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $teacher->teacherProfile?->designation ?? '-' }}</p>
                            @if($teacher->teacherProfile?->qualification)
                                <p class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ris-primary/10 text-ris-primary">{{ $teacher->teacherProfile->qualification }}</p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 font-medium">শীঘ্রই আমাদের শিক্ষকদের তথ্য যোগ করা হবে।</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
