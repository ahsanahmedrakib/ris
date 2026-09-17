{{-- Subtle decorative geometric shapes — minimal section background decor --}}
<div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
    {{-- Big soft rings --}}
    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full border-2 border-ris-primary/10"></div>
    <div class="absolute -bottom-16 -left-14 w-56 h-56 rounded-full border-[10px] border-ris-accent/5"></div>

    {{-- Small filled dot + thin ring --}}
    <div class="absolute top-[14%] left-[5%] w-2 h-2 rounded-full bg-ris-accent/25"></div>
    <div class="absolute bottom-[18%] right-[4%] w-20 h-20 rounded-full border border-ris-primary/10"></div>

    {{-- Rotated square (diamond) --}}
    <div class="absolute top-[22%] right-[6%] w-14 h-14 rotate-45 rounded-2xl bg-ris-primary/5 ring-1 ring-ris-primary/10"></div>

    {{-- Triangle --}}
    <svg class="absolute bottom-[26%] left-[6%] w-11 h-10 text-ris-accent/10 fill-current" viewBox="0 0 24 22">
        <path d="M12 1 L24 22 H0 Z" />
    </svg>

    {{-- Plus sign --}}
    <div class="absolute top-[6%] left-[18%] w-8 h-8 text-ris-primary/15">
        <span class="absolute inset-x-0 top-1/2 h-[3px] -translate-y-1/2 bg-current rounded-full block"></span>
        <span class="absolute inset-y-0 left-1/2 w-[3px] -translate-x-1/2 bg-current rounded-full block"></span>
    </div>

    {{-- Dots cluster --}}
    <div class="absolute bottom-[10%] right-[16%] grid grid-cols-3 gap-1.5">
        @for ($i = 0; $i < 9; $i++)
            <span class="w-1 h-1 rounded-full bg-ris-primary/15"></span>
        @endfor
    </div>

    {{-- Mid-area soft dot --}}
    <div class="absolute top-[40%] left-[46%] hidden lg:block w-2.5 h-2.5 rounded-full bg-ris-light/20"></div>
</div>