<div
    class="teacher-card relative flex flex-col bg-white rounded-3xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 h-full group">
    {{-- Gradient header band with wave --}}
    <div class="relative h-24 shrink-0 bg-linear-to-br from-ris-dark via-ris-accent to-ris-light my-4">
        <div class="absolute inset-0 opacity-15 section-pattern-dots"></div>
        <div class="absolute inset-x-0 bottom-0">
            <svg viewBox="0 0 1440 40" fill="none" class="w-full h-6 text-white block" preserveAspectRatio="none">
                <path d="M0 40C240 0 480 40 720 20C960 0 1200 40 1440 20V40H0V40Z" fill="currentColor" />
            </svg>
        </div>
        <div class="absolute top-3 left-3 w-8 h-8 bg-white/10 rounded-lg rotate-12 pointer-events-none backdrop-blur-sm">
        </div>
        <div class="absolute top-2 right-4 w-2.5 h-2.5 bg-white/40 rounded-full pointer-events-none"></div>
        <div class="absolute bottom-8 right-8 w-1.5 h-1.5 bg-white/50 rounded-full pointer-events-none"></div>

        {{-- Rating badge on header --}}
        <div
            class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/25 text-amber-300 text-xs font-semibold backdrop-blur-sm">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            {{ (int) $testimonial->rating }}
        </div>
    </div>

    <div class="flex-1 flex flex-col px-5 pb-6 -mt-10 relative">
        {{-- Avatar with gradient ring --}}
        <div class="mx-auto w-20 h-20 rounded-2xl p-0.75 gradient-logo shadow-lg ring-4 ring-white overflow-hidden">
            @if ($testimonial->photo)
                <img src="{{ Storage::url($testimonial->photo) }}" alt="{{ $testimonial->name }}"
                    class="w-full h-full rounded-[13px] object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div
                    class="w-full h-full rounded-[13px] bg-white flex items-center justify-center text-ris-primary text-2xl font-semibold group-hover:scale-105 transition-transform duration-300">
                    {{ mb_substr($testimonial->name, 0, 1) }}
                </div>
            @endif
        </div>

        {{-- Name --}}
        <h3
            class="mt-3 font-heading font-bold text-base text-ris-dark group-hover:text-ris-primary transition-colors line-clamp-1 text-center">
            {{ $testimonial->name }}
        </h3>

        {{-- Stars --}}
        <div class="mt-2 flex justify-center gap-0.5">
            @for ($i = 0; $i < 5; $i++)
                <svg class="w-4 h-4 {{ $i < $testimonial->rating ? 'text-amber-400' : 'text-gray-200' }}"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @endfor
        </div>

        @if ($testimonial->designation)
            <div class="mt-2 flex justify-center">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ris-primary/10 text-ris-primary text-xs font-medium">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2V7zm0 8h2v2h-2v-2z" />
                    </svg>
                    {{ $testimonial->designation }}
                </span>
            </div>
        @endif

        {{-- Message --}}
        <p class="mt-3 text-gray-600 leading-relaxed italic flex-1 min-h-16 line-clamp-4"
            title="{{ $testimonial->message }}">
            {{ $testimonial->message }}
        </p>

        {{-- Footer: approved + hover CTA pinned to bottom --}}
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end relative">
            <div class="teacher-card-cta" aria-hidden="true">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609L9.978 5.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Bottom accent bar --}}
    <div class="teacher-card-bar"></div>
</div>
