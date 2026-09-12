@php
    $paginator = $paginator ?? null;
    if (!$paginator) return;
    $baseUrl = request()->url();
    $queryParams = request()->except(['page', 'per_page']);
@endphp

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2 py-3 border-t border-gray-100">
    {{-- Left: Per Page --}}
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <span>প্রতি পৃষ্ঠায়</span>
        <form method="GET" action="{{ $baseUrl }}">
            @foreach($queryParams as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none bg-white cursor-pointer">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
        </form>
        <span>টি ডাটা</span>
    </div>

    {{-- Middle: Showing --}}
    <div class="text-sm text-gray-600">
        দেখাচ্ছে <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>-<span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span> টি ডাটা মোট <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> টি ডাটা
    </div>

    {{-- Right: Pagination Buttons --}}
    <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">&laquo;</a>
            @endif

            {{-- Page Numbers --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp
            @if($start > 1)
                <a href="{{ $paginator->url(1) }}" class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">1</a>
                @if($start > 2)
                    <span class="px-2 py-1.5 text-sm text-gray-400">...</span>
                @endif
            @endif
            @for($i = $start; $i <= $end; $i++)
                @if($i == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-sm text-white bg-ris-primary rounded-lg font-medium">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}" class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">{{ $i }}</a>
                @endif
            @endfor
            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <span class="px-2 py-1.5 text-sm text-gray-400">...</span>
                @endif
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">{{ $paginator->lastPage() }}</a>
            @endif

            {{-- Next --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">&raquo;</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">&raquo;</span>
            @endif
        </div>
</div>