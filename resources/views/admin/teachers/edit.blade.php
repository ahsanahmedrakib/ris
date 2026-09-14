@extends('layouts.admin')

@section('title', 'শিক্ষক সম্পাদনা — ' . $teacher->name)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.teachers.index') }}"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">শিক্ষক সম্পাদনা</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $teacher->name }}-এর তথ্য আপডেট করুন</p>
            </div>
        </div>

        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-xl border border-gray-200 p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Basic Info --}}
                <div class="sm:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">মৌলিক তথ্য</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                        class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                        class="w-full px-4 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ফোন</label>
                    <input type="tel" name="phone" value="{{ old('phone', $teacher->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>
                    <input type="file" name="photo" accept="image/*"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    @if ($teacher->teacherProfile?->photo)
                        <p class="mt-1 text-xs text-gray-500">বর্তমান ছবি আছে। নতুন দিলে পুরোনো মুছে যাবে।</p>
                    @endif
                </div>

                {{-- Professional Info --}}
                <div class="sm:col-span-2 pt-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">পেশাদার তথ্য</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">পদবি</label>
                    <select name="designation"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">-- নির্বাচন করুন --</option>
                        @foreach ($designations as $value)
                            <option value="{{ $value }}"
                                {{ old('designation', $teacher->teacherProfile?->designation) === $value ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">বিষয়</label>
                    <input type="text" name="subject" value="{{ old('subject', $teacher->teacherProfile?->subject) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">সর্বোচ্চ শিক্ষাগত যোগ্যতা</label>
                    <input type="text" name="qualification"
                        value="{{ old('qualification', $teacher->teacherProfile?->qualification) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শিক্ষাপ্রতিষ্ঠান</label>
                    <input type="text" name="institute"
                        value="{{ old('institute', $teacher->teacherProfile?->institute) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RIS-এ যোগদানের তারিখ</label>
                    <input type="text" data-date-mask name="joining_date"
                        value="{{ old('joining_date', $teacher->teacherProfile?->joining_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">স্ট্যাটাস</label>
                    <select name="is_active"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="1" {{ old('is_active', $teacher->is_active) ? 'selected' : '' }}>সক্রিয়
                        </option>
                        <option value="0" {{ !old('is_active', $teacher->is_active) ? 'selected' : '' }}>ডিলিট
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ফিচার্ড</label>
                    <select name="is_featured"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="0">না</option>
                        <option value="1"
                            {{ old('is_featured', $teacher->teacherProfile?->is_featured) ? 'selected' : '' }}>হ্যাঁ
                        </option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ববর্তী প্রতিষ্ঠানসমূহ</label>
                    <textarea name="previous_institutions" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('previous_institutions', $teacher->teacherProfile?->previous_institutions) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">পরিচিতি</label>
                    <textarea name="bio" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('bio', $teacher->teacherProfile?->bio) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">অভিজ্ঞতা</label>
                    <textarea name="experience" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('experience', $teacher->teacherProfile?->experience) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">অর্জনসমূহ</label>
                    <textarea name="achievements" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none">{{ old('achievements', $teacher->teacherProfile?->achievements) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
                <a href="{{ route('admin.teachers.index') }}"
                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">বাতিল</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">আপডেট
                    করুন</button>
            </div>
        </form>
    </div>
@endsection
