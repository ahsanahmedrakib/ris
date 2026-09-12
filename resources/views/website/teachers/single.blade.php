@extends('layouts.website')

@section('title', $teacher->name . ' — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">{{ $teacher->name }}</h1>
            <p class="mt-3 text-white/70 text-lg">{{ $teacher->teacherProfile?->designation ?? 'শিক্ষক' }}</p>
        </div>
    </section>

    {{-- Teacher Profile --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-10">
                {{-- Left: Photo + QR --}}
                <div class="text-center lg:text-left">
                    @if($teacher->teacherProfile?->photo)
                        <img src="{{ Storage::url($teacher->teacherProfile->photo) }}" alt="{{ $teacher->name }}" class="w-48 h-48 rounded-full object-cover mx-auto lg:mx-0 shadow-lg">
                    @else
                        <div class="w-48 h-48 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-5xl font-semibold mx-auto lg:mx-0 shadow-lg">
                            {{ mb_substr($teacher->name, 0, 1) }}
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col items-center lg:items-start gap-2">
                        <h2 class="font-heading font-bold text-2xl text-ris-dark">{{ $teacher->name }}</h2>
                        <p class="text-ris-primary font-medium">{{ $teacher->teacherProfile?->subject ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $teacher->teacherProfile?->designation ?? '-' }}</p>
                    </div>

                    {{-- QR Code --}}
                    <div class="mt-6 inline-flex flex-col items-center bg-gray-50 rounded-xl p-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('teacher.single', $teacher->teacherProfile->slug)) }}" alt="QR Code" class="w-32 h-32">
                        <p class="mt-2 text-xs text-gray-500">স্ক্যান করে প্রোফাইল দেখুন</p>
                    </div>
                </div>

                {{-- Right: Details --}}
                <div class="lg:col-span-2 space-y-8">
                    @if($teacher->teacherProfile?->bio)
                        <div>
                            <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">পরিচিতি</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $teacher->teacherProfile->bio }}</p>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">শিক্ষাগত ও পেশাদার তথ্য</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">বিষয়</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $teacher->teacherProfile?->subject ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">যোগ্যতা</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $teacher->teacherProfile?->qualification ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">শিক্ষাপ্রতিষ্ঠান</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $teacher->teacherProfile?->institute ?? '-' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-sm text-gray-500">যোগদানের তারিখ</dt>
                                <dd class="font-medium text-gray-900 mt-1">{{ $teacher->teacherProfile?->joining_date?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($teacher->teacherProfile?->previous_institutions)
                        <div>
                            <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">পূর্ববর্তী প্রতিষ্ঠানসমূহ</h3>
                            <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $teacher->teacherProfile->previous_institutions }}</p>
                        </div>
                    @endif

                    @if($teacher->teacherProfile?->experience)
                        <div>
                            <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">অভিজ্ঞতা</h3>
                            <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $teacher->teacherProfile->experience }}</p>
                        </div>
                    @endif

                    @if($teacher->teacherProfile?->achievements)
                        <div>
                            <h3 class="font-heading font-bold text-lg text-ris-dark mb-3">অর্জনসমূহ</h3>
                            <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $teacher->teacherProfile->achievements }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
