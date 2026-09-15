<a href="{{ route('teacher.single', $teacher->teacherProfile->slug) }}"
    class="block h-full group focus:outline-none focus-visible:ring-2 focus-visible:ring-ris-primary rounded-3xl">
    <div
        class="teacher-card relative bg-white rounded-3xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-full">
        {{-- Gradient header band with wave --}}
        <div class="relative h-24 bg-linear-to-br from-ris-dark via-ris-accent to-ris-light">
            <div class="absolute inset-0 opacity-15 section-pattern-dots"></div>
            <div class="absolute inset-x-0 bottom-0">
                <svg viewBox="0 0 1440 40" fill="none" class="w-full h-6 text-white block" preserveAspectRatio="none">
                    <path d="M0 40C240 0 480 40 720 20C960 0 1200 40 1440 20V40H0V40Z" fill="currentColor" />
                </svg>
            </div>
            <div
                class="absolute top-3 left-3 w-8 h-8 bg-white/10 rounded-lg rotate-12 pointer-events-none backdrop-blur-sm">
            </div>
            <div class="absolute top-2 right-4 w-2.5 h-2.5 bg-white/40 rounded-full pointer-events-none"></div>
            <div class="absolute bottom-8 right-8 w-1.5 h-1.5 bg-white/50 rounded-full pointer-events-none"></div>
        </div>

        <div class="px-4 pb-6 -mt-10 relative">
            {{-- Avatar with gradient ring --}}
            <div class="mx-auto w-20 h-20 rounded-2xl p-[3px] gradient-logo shadow-lg ring-4 ring-white overflow-hidden">
                @if ($teacher->teacherProfile?->photo)
                    <img src="{{ Storage::url($teacher->teacherProfile->photo) }}" alt="{{ $teacher->name }}"
                        class="w-full h-full rounded-[13px] object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div
                        class="w-full h-full rounded-[13px] bg-white flex items-center justify-center text-ris-primary text-2xl font-semibold group-hover:scale-105 transition-transform duration-300">
                        {{ mb_substr($teacher->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <h3 class="mt-3 font-heading font-bold text-base text-ris-dark group-hover:text-ris-primary transition-colors line-clamp-1">
                {{ $teacher->name }}
            </h3>

            <div class="mt-2 flex justify-center">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ris-primary/10 text-ris-primary text-xs font-medium">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6" />
                    </svg>
                    {{ $teacher->teacherProfile?->subject ?? 'শিক্ষক' }}
                </span>
            </div>

            <p class="text-xs text-gray-500 mt-2 line-clamp-1">{{ $teacher->teacherProfile?->designation ?? '-' }}</p>

            @if ($teacher->teacherProfile?->qualification)
                <p class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 line-clamp-1">
                    {{ $teacher->teacherProfile->qualification }}
                </p>
            @endif

            {{-- Hover CTA --}}
            <div class="teacher-card-cta" aria-hidden="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </div>
        </div>

        {{-- Bottom accent bar --}}
        <div class="teacher-card-bar"></div>
    </div>
</a>