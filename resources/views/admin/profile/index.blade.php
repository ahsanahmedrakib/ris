@extends('layouts.admin')

@section('title', 'প্রোফাইল — অ্যাডমিন প্যানেল')

@section('content')
    <div class="space-y-6" x-data="profileApp()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">আমার প্রোফাইল</h1>
                <p class="text-sm text-gray-500 mt-1">ব্যক্তিগত তথ্য, ছবি ও পাসওয়ার্ড আপডেট করুন</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- ═══════ Profile Info ═══════ --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="gradient-logo px-6 py-4">
                    <h3 class="font-heading font-bold text-white text-lg">ব্যক্তিগত তথ্য</h3>
                    <p class="text-white/70 text-xs mt-0.5">নাম, যোগাযোগ ও প্রোফাইল ছবি আপডেট করুন</p>
                </div>
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Photo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">প্রোফাইল ছবি
                            <span class="text-gray-400 font-normal">(ঐচ্ছিক)</span></label>
                        <div class="flex items-center gap-5">
                            <div class="shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="প্রোফাইল ছবি"
                                        class="w-24 h-24 rounded-full object-cover ring-2 ring-ris-primary/30">
                                </template>
                                <template x-if="!photoPreview">
                                    <div
                                        class="w-24 h-24 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-3xl font-semibold ring-2 ring-ris-primary/10">
                                        {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1 space-y-2">
                                <input type="file" name="avatar" accept="image/*"
                                    @change="onPhotoChange($event)"
                                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ris-primary/10 file:text-ris-primary hover:file:bg-ris-primary/20 cursor-pointer">
                                <label class="inline-flex items-center gap-2 text-xs text-gray-500">
                                    <input type="checkbox" name="remove_avatar" value="1" @change="onRemovePhoto($event)"
                                        class="rounded border-gray-300 text-ris-primary focus:ring-ris-primary cursor-pointer">
                                    ছবি সরিয়ে ফেলুন
                                </label>
                                @error('avatar')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">পূর্ণ নাম <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name ?? '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ইউজারনেম</label>
                            <input type="text" name="username"
                                value="{{ old('username', Auth::user()->username ?? '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('username') border-red-400 @enderror">
                            @error('username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল</label>
                            <input type="tel" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('phone') border-red-400 @enderror"
                                placeholder="01XXXXXXXXX">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">তথ্য
                            আপডেট করুন</button>
                    </div>
                </form>
            </div>

            {{-- ═══════ Password ═══════ --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden h-fit">
                <div class="gradient-logo px-6 py-4">
                    <h3 class="font-heading font-bold text-white text-lg">পাসওয়ার্ড পরিবর্তন</h3>
                    <p class="text-white/70 text-xs mt-0.5">নিয়মিত পাসওয়ার্ড পরিবর্তন করুন</p>
                </div>
                <form action="{{ route('admin.profile.password') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">বর্তমান পাসওয়ার্ড <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="current_password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('current_password') border-red-400 @enderror">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">নতুন পাসওয়ার্ড <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors @error('password') border-red-400 @enderror"
                            placeholder="কমপক্ষে ৮ অক্ষর">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">নতুন পাসওয়ার্ড নিশ্চিত করুন <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">পাসওয়ার্ড
                            পরিবর্তন করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            function profileApp() {
                return {
                    photoPreview: {{ json_encode(Auth::user()->avatarUrl) }},
                    onPhotoChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.photoPreview = URL.createObjectURL(file);
                        }
                    },
                    onRemovePhoto(event) {
                        if (event.target.checked) {
                            this.photoPreview = null;
                        } else {
                            this.photoPreview = {{ json_encode(Auth::user()->avatarUrl) }};
                        }
                    }
                };
            }
        </script>
    @endsection
@endsection