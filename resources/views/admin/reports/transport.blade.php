@extends('layouts.admin')

@section('title', 'পরিবহন রিপোর্ট')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">পরিবহন রিপোর্ট</h1>
        <p class="text-sm text-gray-500 mt-1">বাস, রুট ও ছাত্র বিতরণের রিপোর্ট দেখুন</p>
    </div>

    {{-- Bus List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">বাস তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">বাস নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রুট</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">চালক</th>
                        <th class="text-center px-5 py-3.5 font-medium text-gray-500">ধারণক্ষমতা</th>
                        <th class="text-center px-5 py-3.5 font-medium text-gray-500">নির্ধারিত ছাত্র</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($buses as $index => $bus)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($buses->currentPage() - 1) * $buses->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $bus->bus_no }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $bus->route_name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($bus->driver_name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-gray-900">{{ $bus->driver_name ?? '-' }}</span>
                                        <p class="text-xs text-gray-400">{{ $bus->driver_phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-600">{{ $bus->capacity ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ris-primary/10 text-ris-primary font-semibold">
                                    {{ $bus->student_transports_count ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8m0 0v8m0-8l-4 4m4-4l-4 4"/></svg>
                                <p class="text-gray-500 font-medium">কোনো বাস পাওয়া যায়নি</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($buses->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $buses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
