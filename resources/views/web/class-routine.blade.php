@extends('layouts.website')

@section('title', 'ক্লাশ রুটিন — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">ক্লাশ রুটিন</h1>
            <p class="mt-3 text-white/70 text-lg">শ্রেণি নির্বাচন করে সাপ্তাহিক সময়সূচি দেখুন</p>
        </div>
    </section>

    <section class="py-12 sm:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($classes->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 py-16 text-center">
                    <p class="text-gray-400">কোনো শ্রেণি পাওয়া যায়নি</p>
                </div>
            @else
                @include('web.partials.class-routine-grid')
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function() {
            var grid = document.getElementById('routine-grid');
            if (!grid) return;

            var activeClassId = {{ json_encode((int) $selectedClassId) }};

            grid.addEventListener('click', function(e) {
                var btn = e.target.closest('.routine-class-btn');
                if (!btn) return;
                e.preventDefault();

                var classId = Number(btn.dataset.classId);
                if (classId === activeClassId) return;

                var url = new URL(btn.href, window.location.origin);
                url.searchParams.set('class_id', classId);

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(res) {
                    if (!res.ok) throw new Error('Failed to load routine');
                    return res.text();
                }).then(function(html) {
                    var next = document.createElement('div');
                    next.innerHTML = html.trim();

                    var nextGrid = next.querySelector('#routine-grid');
                    if (!nextGrid) throw new Error('Invalid response');
                    grid.innerHTML = nextGrid.innerHTML;
                    activeClassId = classId;

                    history.pushState({ class_id: classId }, '', url.toString());
                    window.dispatchEvent(new Event('routineUpdated'));
                }).catch(function() {
                    window.location.href = btn.href;
                });
            });

            window.addEventListener('popstate', function(e) {
                var params = new URLSearchParams(window.location.search);
                var classId = params.get('class_id');
                if (!classId) return;

                fetch(new URL(window.location.pathname + '?class_id=' + classId, window.location.origin)
                    .toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(res) {
                    if (!res.ok) throw new Error('Failed to load routine');
                    return res.text();
                }).then(function(html) {
                    var next = document.createElement('div');
                    next.innerHTML = html.trim();
                    var nextGrid = next.querySelector('#routine-grid');
                    if (!nextGrid) throw new Error('Invalid response');
                    grid.innerHTML = nextGrid.innerHTML;
                    activeClassId = Number(classId);
                }).catch(function() {
                    window.location.reload();
                });
            });
        })();
    </script>
@endsection