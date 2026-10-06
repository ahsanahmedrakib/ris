{{--
    The visible breadcrumb trail.

    It exists for two audiences. Google reads it and shows the path under the
    result, which tells a searcher the page is one step from the section they
    wanted. And a visitor who lands on a notice from a search result can see
    where they are without scrolling back to the menu.

    It reads the trail off the Seo object rather than taking its own argument, so
    the crumbs on screen and the BreadcrumbList in the page's structured data are
    the same list and cannot drift apart.
--}}
@if (! empty($seo?->breadcrumbs()) && count($seo->breadcrumbs()) > 1)
    <nav aria-label="Breadcrumb" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <ol class="flex items-center gap-2 py-3 text-xs text-gray-500">
                @foreach ($seo->breadcrumbs() as $crumb)
                    @php $isLast = $loop->last; @endphp
                    <li class="flex items-center gap-2">
                        @if (! empty($crumb['url']) && ! $isLast)
                            <a href="{{ $crumb['url'] }}"
                                class="hover:text-ris-primary transition-colors">{{ $crumb['name'] }}</a>
                        @else
                            <span class="text-gray-700 font-medium" aria-current="page">{{ $crumb['name'] }}</span>
                        @endif
                        @unless ($isLast)
                            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        @endunless
                    </li>
                @endforeach
            </ol>
        </div>
    </nav>
@endif