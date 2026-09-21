<div id="routine-grid">
    {{-- Class filter --}}
    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-8">
        @foreach ($classes as $class)
            <a href="{{ route('class-routine', ['class_id' => $class->id]) }}" data-class-id="{{ $class->id }}"
                class="routine-class-btn px-4 py-2 rounded-full text-sm font-heading font-medium border transition-all duration-200 cursor-pointer
                    {{ (int) $selectedClassId === (int) $class->id
                        ? 'bg-ris-primary text-white border-ris-primary shadow-md shadow-ris-primary/25'
                        : 'bg-white text-gray-600 border-gray-200 hover:text-ris-primary hover:border-ris-primary/40' }}">
                {{ $class->name }}
            </a>
        @endforeach
    </div>

    @if ($selectedClass)
        <div class="text-center mb-6">
            <h2 class="font-heading font-bold text-2xl text-gray-900">
                {{ $selectedClass->name }} শ্রেণির সাপ্তাহিক সময়সূচি
            </h2>
        </div>
    @endif

    {{-- Weekly timetable --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-225">
                <thead>
                    <tr class="gradient-logo">
                        <th
                            class="text-center border border-white/25 px-4 py-3.5 font-medium text-white whitespace-nowrap w-40">
                            সময়</th>
                        @foreach ($days as $day)
                            <th
                                class="text-center border border-white/25 px-4 py-3.5 font-medium text-white whitespace-nowrap">
                                {{ $day->label() }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($slots as $slotKey => $slot)
                        <tr>
                            <td class="px-3 py-3 text-center bg-gray-50 border border-gray-200 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-ris-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $slot['label'] }}
                                </span>
                            </td>
                            @foreach ($days as $day)
                                @php $routine = $grid[$slotKey][$day->order()] ?? null; @endphp
                                <td class="px-3 py-3 text-center align-middle border border-gray-200">
                                    @if ($routine)
                                        <div class="rounded-lg border border-gray-100 bg-gray-50/70 px-3 py-2.5">
                                            <p class="font-heading font-semibold text-gray-900 leading-snug">
                                                {{ $routine->subject?->name ?? '-' }}
                                            </p>
                                            <p
                                                class="mt-1 text-xs text-gray-500 flex items-center justify-center gap-1">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $routine->teacher?->name ?? '-' }}
                                            </p>
                                            @if ($routine->room_no)
                                                <span
                                                    class="mt-1.5 inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-ris-primary/10 text-ris-primary">
                                                    কক্ষ: {{ $routine->room_no }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center py-4 text-gray-200">
                                            —</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($days) + 1 }}"
                                class="px-5 py-16 text-center text-gray-400 border border-gray-200">
                                এই শ্রেণির কোনো ক্লাশ রুটিন এখনো যোগ করা হয়নি
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
