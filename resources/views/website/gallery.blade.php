@extends('layouts.website')

@section('title', 'গ্যালারি — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">আমাদের গ্যালারি</h1>
            <p class="mt-3 text-white/70 text-lg">স্কুলের সুন্দর মুহূর্তগুলো</p>
        </div>
    </section>

    {{-- Gallery --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 right-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($categories->count())
                <div class="mb-8 text-center reveal">
                    <div class="inline-flex flex-wrap items-center gap-2 justify-center">
                        <button onclick="document.querySelectorAll('.gallery-item').forEach(el => el.style.display='')"
                            class="px-4 py-2 rounded-full text-sm font-medium bg-ris-primary text-white hover:bg-ris-dark transition-colors cursor-pointer">
                            সব
                        </button>
                        @foreach ($categories as $category)
                            <button onclick="document.querySelectorAll('.gallery-item').forEach(el => el.style.display = el.dataset.category === '{{ $category }}' ? '' : 'none')"
                                class="px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-600 hover:bg-ris-primary/10 hover:text-ris-primary transition-colors cursor-pointer">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($items->count())
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 reveal-stagger">
                    @foreach ($items as $item)
                        <div class="gallery-item lightbox-trigger group relative rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 aspect-square reveal cursor-pointer"
                            data-category="{{ $item->category ?? '' }}"
                            data-title="{{ $item->title }}"
                            data-caption="{{ $item->description ?? '' }}">
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5">
                                    @if ($item->category)
                                        <span class="inline-block px-2.5 py-0.5 bg-ris-primary text-white text-xs font-medium rounded-full mb-2">{{ $item->category }}</span>
                                    @endif
                                    <h4 class="font-heading font-bold text-white text-sm sm:text-base">{{ $item->title }}</h4>
                                    @if ($item->description)
                                        <p class="text-white/80 text-xs mt-1.5 line-clamp-2">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center cursor-pointer hover:bg-white/30 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-ris-dark">এখনো কোনো ছবি নেই</h3>
                    <p class="mt-2 text-gray-500">শীঘ্রই আমাদের গ্যালারিতে ছবি যোগ করা হবে।</p>
                </div>
            @endif
        </div>
    </section>

@endsection