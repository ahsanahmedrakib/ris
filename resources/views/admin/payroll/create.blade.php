@extends('layouts.admin')

@section('title', 'বেতন প্রসেসিং')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.payroll.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বেতন প্রসেসিং</h1>
            <p class="text-sm text-gray-500 mt-1">কর্মচারীদের মাসিক বেতন তৈরি করুন</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</h3>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">মাস ও বছর</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">মাস <span class="text-red-500">*</span></label>
                        <select name="month" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', (int) $currentMonth) === $m ? 'selected' : '' }}>{{ $m }} নং মাস</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">বছর <span class="text-red-500">*</span></label>
                        <select name="year" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('year', (int) $currentYear) === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">কর্মচারী তালিকা</h2>
                <p class="text-xs text-gray-400 mt-1">যেসব কর্মচারীর জন্য বেতন তৈরি করতে চান সেগুলো টিক দিন</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">
                                <input type="checkbox" id="select-all" checked
                                       class="w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary">
                            </th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">পদবি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">মূল বেতন</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ভাতা</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাড়</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($staff as $index => $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                           class="staff-check w-4 h-4 rounded border-gray-300 text-ris-primary focus:ring-ris-primary" checked>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-900">{{ $member->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $member->employee_id }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $member->designation ?? '-' }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">৳{{ number_format($member->salary, 2) }}</td>
                                <td class="px-5 py-3.5 w-36">
                                    <input type="number" name="allowances[]" value="{{ old('allowances.' . $index, 0) }}" min="0" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </td>
                                <td class="px-5 py-3.5 w-36">
                                    <input type="number" name="deductions[]" value="{{ old('deductions.' . $index, 0) }}" min="0" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো কর্মচারী নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.payroll.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                বাতিল
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                বেতন তৈরি করুন
            </button>
        </div>
    </form>

</div>

<script>
    document.getElementById('select-all').addEventListener('change', function () {
        document.querySelectorAll('.staff-check').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection