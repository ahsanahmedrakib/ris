{{--
    Class routine skeleton — shown while another class routine is fetched
    in place (no full page navigation).
--}}
<div id="routine-skeleton" class="hidden" aria-hidden="true">
    {{-- Heading --}}
    <div class="text-center mb-6">
        <div class="skeleton h-7 w-72 mx-auto"></div>
    </div>

    {{-- Timetable --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-225">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center px-4 py-3.5 w-40">
                            <div class="skeleton h-3.5 w-16 mx-auto"></div>
                        </th>
                        @for ($day = 0; $day < 6; $day++)
                            <th class="px-4 py-3.5">
                                <div class="skeleton h-3.5 w-20 mx-auto"></div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @for ($slot = 0; $slot < 6; $slot++)
                        <tr>
                            <td class="px-3 py-3 text-center bg-gray-50 border border-gray-200">
                                <div class="skeleton h-3 w-20 mx-auto"></div>
                            </td>
                            @for ($day = 0; $day < 6; $day++)
                                <td class="px-3 py-3 text-center border border-gray-200">
                                    <div class="rounded-lg border border-gray-100 bg-gray-50/70 px-3 py-2.5 space-y-2">
                                        <div class="skeleton h-3.5 w-3/4 mx-auto"></div>
                                        <div class="skeleton h-3 w-1/2 mx-auto"></div>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
