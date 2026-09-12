@extends('layouts.admin')

@section('title', 'বার্তা')

@section('content')
<div class="space-y-6" x-data="contactApp()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বার্তা</h1>
            <p class="text-sm text-gray-500 mt-1">যোগাযোগ ফর্ম থেকে প্রাপ্ত বার্তা পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-sm font-medium rounded-lg border border-amber-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                {{ $unreadCount }} টি অপঠিত
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.contact-messages.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম বা ফোন দিয়ে খুঁজুন..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থা</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল বার্তা</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>অপঠিত</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>পঠিত</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                        ফিল্টার করুন
                    </button>
                    <a href="{{ route('admin.contact-messages.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="gradient-logo">
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">নাম</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ইমেইল</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ফোন</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিষয়</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">তারিখ</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">অবস্থা</th>
                        <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($messages as $index => $msg)
                        <tr class="hover:bg-gray-50 transition-colors {{ $msg->is_read ? '' : 'bg-amber-50/40' }}">
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ ($messages->currentPage() - 1) * $messages->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ mb_substr($msg->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $msg->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $msg->email }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $msg->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap max-w-[200px] truncate">{{ $msg->subject }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $msg->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($msg->is_read)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">পঠিত</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">অপঠিত</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openViewModal({{ $msg->id }})" class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors" title="দেখুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.contact-messages.read', $msg) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors" title="{{ $msg->is_read ? 'অপঠিত করুন' : 'পঠিত করুন' }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $msg) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই বার্তাটি মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors" title="মুছুন">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো বার্তা পাওয়া যায়নি</p>
                                <p class="text-sm text-gray-400 mt-1">ফিল্টার পরিবর্তন করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('vendor.pagination.custom', ['paginator' => $messages])
    </div>

    {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showViewModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">বার্তা বিস্তারিত</h3>
                <button @click="showViewModal = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="viewData">
                    <div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 mb-1">নাম</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.name"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ইমেইল</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.email"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">ফোন</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.phone || '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">বিষয়</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.subject"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 mb-1">তারিখ</dt>
                                <dd class="font-medium text-gray-900" x-text="viewData.created_at"></dd>
                            </div>
                        </dl>
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <dt class="text-gray-500 mb-2 text-sm">বার্তা</dt>
                            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="viewData.message"></div>
                        </div>
                        <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end">
                            <button @click="showViewModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বন্ধ করুন</button>
                        </div>
                    </div>
                </template>
                <template x-if="viewLoading">
                    <div class="py-12 text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <p class="text-gray-400 text-sm">লোড হচ্ছে...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>

@section('scripts')
<script>
function contactApp() {
    return {
        showViewModal: false,
        viewData: null,
        viewLoading: false,

        async openViewModal(id) {
            this.showViewModal = true;
            this.viewLoading = true;
            this.viewData = null;
            try {
                const res = await fetch(`{{ url('admin/contact-messages') }}/${id}`);
                this.viewData = await res.json();
            } catch (e) {
                this.showViewModal = false;
                alert('তথ্য লোড করতে সমস্যা হয়েছে।');
            } finally {
                this.viewLoading = false;
            }
        },
    }
}
</script>
@endsection
@endsection
