{{--
    Full page skeleton loader for the public website.

    Shown by RisSkeleton.page.start() the moment an internal navigation begins,
    so the visitor sees the upcoming page structure instead of a blank screen
    while the server renders the data. Removed from the DOM on page load.
--}}
<div id="ris-page-skeleton"
    class="ris-skeleton-page fixed inset-0 z-9998 bg-white overflow-y-auto"
    style="display:none" aria-hidden="true">
    {{-- Top bar + header --}}
    <div class="h-9 bg-ris-dark"></div>
    <div class="border-b border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="skeleton w-10 lg:w-14 h-10 lg:h-14"></div>
                <div class="space-y-2 hidden sm:block">
                    <div class="skeleton h-4 w-48"></div>
                    <div class="skeleton h-3 w-64"></div>
                </div>
            </div>
            <div class="hidden lg:flex items-center gap-3">
                @for ($i = 0; $i < 5; $i++)
                    <div class="skeleton h-3.5 w-20"></div>
                @endfor
            </div>
            <div class="flex items-center gap-3 lg:hidden">
                <div class="skeleton h-8 w-8"></div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-14">
        {{-- Hero --}}
        <div class="space-y-6">
            <div class="skeleton h-10 w-2/3 max-w-2xl"></div>
            <div class="skeleton h-4 w-1/2 max-w-xl"></div>
            <div class="flex gap-3 pt-2">
                <div class="skeleton h-11 w-36 rounded-lg"></div>
                <div class="skeleton h-11 w-36 rounded-lg"></div>
            </div>
        </div>

        {{-- Feature / stat strip --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @for ($i = 0; $i < 4; $i++)
                <div class="rounded-2xl border border-gray-100 p-6 space-y-3">
                    <div class="skeleton h-12 w-12 rounded-xl"></div>
                    <div class="skeleton h-7 w-20"></div>
                    <div class="skeleton h-3 w-28"></div>
                </div>
            @endfor
        </div>

        {{-- Section: heading + card grid --}}
        <div class="space-y-8">
            <div class="space-y-3">
                <div class="skeleton h-8 w-64"></div>
                <div class="skeleton h-4 w-96 max-w-full"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for ($i = 0; $i < 6; $i++)
                    <div class="rounded-2xl border border-gray-100 p-5 space-y-4">
                        <div class="skeleton h-44 w-full rounded-xl"></div>
                        <div class="skeleton h-4 w-4/5"></div>
                        <div class="skeleton h-3 w-full"></div>
                        <div class="skeleton h-3 w-2/3"></div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Section: list rows --}}
        <div class="space-y-8">
            <div class="space-y-3">
                <div class="skeleton h-8 w-56"></div>
                <div class="skeleton h-4 w-80 max-w-full"></div>
            </div>
            <div class="space-y-4">
                @for ($i = 0; $i < 5; $i++)
                    <div class="flex items-center gap-4 rounded-2xl border border-gray-100 p-5">
                        <div class="skeleton h-14 w-14 rounded-2xl"></div>
                        <div class="flex-1 space-y-2.5">
                            <div class="skeleton h-4 w-3/5"></div>
                            <div class="skeleton h-3 w-2/5"></div>
                        </div>
                        <div class="skeleton h-3 w-16"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
