{{--
    Modal / panel data loader.

    Replaces the plain spinner used while an admin view or edit modal fetches its
    record, so the panel keeps its layout while the data streams in.
--}}
<div {{ $attributes->merge(['class' => 'py-10 px-6']) }} aria-hidden="true">
    <div class="flex items-center gap-4">
        <x-skeleton.circle size="w-14 h-14" />
        <x-skeleton.text :lines="2" class="flex-1" />
    </div>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
        @for ($i = 0; $i < 6; $i++)
            <x-skeleton.text :lines="2" width="w-full" size="sm" />
        @endfor
    </div>

    <div class="mt-8 space-y-3">
        <div class="skeleton h-3 w-full"></div>
        <div class="skeleton h-3 w-11/12"></div>
        <div class="skeleton h-3 w-2/3"></div>
    </div>
</div>
