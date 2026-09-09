@extends('layouts.admin')

@section('title', 'নোটিশ ব্যবস্থাপনা')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">নোটিশ ব্যবস্থাপনা</h1>
            <p class="text-sm text-gray-500 mt-1">সকল নোটিশ দেখুন ও পরিচালনা করুন</p>
        </div>
        <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন নোটিশ যোগ করুন
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিরোনাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ধরন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">লক্ষ্য</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">প্রকাশের তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মেয়াদোত্তীর্ণ</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($notices ?? []) as $index => $notice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $notice->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $notice->content }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($notice->type ?? '') === 'event' ? 'bg-blue-50 text-blue-700' : (($notice->type ?? '') === 'holiday' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ($notice->type ?? '') === 'event' ? 'অনুষ্ঠান' : (($notice->type ?? '') === 'holiday' ? 'ছুটি' : 'নোটিশ') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ ($notice->target_role ?? '') === 'all' ? 'সকল' : (($notice->target_role ?? '') === 'teacher' ? 'শিক্ষক' : (($notice->target_role ?? '') === 'parent' ? 'অভিভাবক' : 'ছাত্র')) }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $notice->published_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $notice->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.notices.edit', $notice) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="সম্পাদনা">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="মুছুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো নোটিশ নেই</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($notices) && $notices instanceof \Illuminate\Pagination\LengthAwarePaginator && $notices->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $notices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection