@extends('layouts.admin')

@section('title', 'QR কোড জেনারেটর')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">QR কোড জেনারেটর</h1>
        <p class="text-sm text-gray-500 mt-1">যেকোনো লিংক থেকে ব্র্যান্ড রঙ ও স্কুল লোগোসহ QR কোড তৈরি করুন</p>
    </div>

    <div class="grid lg:grid-cols-5 gap-6" x-data="qrGenerator()">

        {{-- Controls --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-6">
            <form @submit.prevent="updateUrl()" class="space-y-5">

                {{-- Link --}}
                <div>
                    <label for="qdata" class="block text-sm font-medium text-gray-700 mb-1.5">লিংক / টেক্সট</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 015.656 0l2.344 2.343a4 4 0 01-5.657 5.657l-1.172 1.172a4 4 0 01-5.657-5.657" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.172 13.828a4 4 0 015.656 0l-2.344-2.343a4 4 0 01-5.657-5.657l1.172-1.172a4 4 0 015.657 5.657" />
                            </svg>
                        </span>
                        <input type="text" id="qdata" x-model="link"
                            class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all"
                            placeholder="যেমন: https://www.example.com">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">https:// নেই থাকলে স্বয়ংক্রিয়ভাবে যোগ হবে।</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">

                    {{-- Color --}}
                    <div>
                        <label for="qcolor" class="block text-sm font-medium text-gray-700 mb-1.5">ব্র্যান্ড রঙ</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="qcolor" x-model="color"
                                class="w-12 h-10 rounded-lg border border-gray-300 cursor-pointer bg-gray-50">
                            <span class="text-sm text-gray-500 font-mono" x-text="color.toUpperCase()"></span>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3">
                            <button type="button" @click="color = '#a31c42'"
                                class="w-7 h-7 rounded-full border-2 border-gray-200 transition-transform hover:scale-110"
                                style="background:#a31c42"
                                :class="color === '#a31c42' && 'ring-2 ring-offset-2 ring-ris-primary'"></button>
                            <button type="button" @click="color = '#8c1d30'"
                                class="w-7 h-7 rounded-full border-2 border-gray-200 transition-transform hover:scale-110"
                                style="background:#8c1d30"
                                :class="color === '#8c1d30' && 'ring-2 ring-offset-2 ring-ris-primary'"></button>
                            <button type="button" @click="color = '#d8353e'"
                                class="w-7 h-7 rounded-full border-2 border-gray-200 transition-transform hover:scale-110"
                                style="background:#d8353e"
                                :class="color === '#d8353e' && 'ring-2 ring-offset-2 ring-ris-primary'"></button>
                            <button type="button" @click="color = '#111827'"
                                class="w-7 h-7 rounded-full border-2 border-gray-200 transition-transform hover:scale-110"
                                style="background:#111827"
                                :class="color === '#111827' && 'ring-2 ring-offset-2 ring-ris-primary'"></button>
                        </div>
                    </div>

                    {{-- Size --}}
                    <div>
                        <label for="qsize" class="block text-sm font-medium text-gray-700 mb-1.5">আকার</label>
                        <select id="qsize" x-model="size"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                            <option value="200">ছোট (200px)</option>
                            <option value="300">মাঝারি (300px)</option>
                            <option value="400">বড় (400px)</option>
                            <option value="500">খুব বড় (500px)</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    QR কোড তৈরি করুন
                </button>
            </form>
        </div>

        {{-- Preview --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col items-center">
                <span class="text-sm font-medium text-gray-700 mb-4">প্রিভিউ</span>

                <div id="qr-preview"
                    class="w-56 h-56 sm:w-64 sm:h-64 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300 overflow-hidden">
                    <template x-if="url">
                        <img :src="url" :alt="link" class="max-w-full max-h-full object-contain">
                    </template>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-center gap-3" x-show="url" x-cloak>
                    <a :href="url" download="ris-qrcode.png"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-dark text-white text-sm font-medium rounded-lg hover:bg-ris-primary/90 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L5 8m4-4v12" />
                        </svg>
                        ডাউনলোড
                    </a>
                    <button type="button" @click="copyLink()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span x-text="copied ? 'কপি হয়েছে!' : 'লিংক কপি'"></span>
                    </button>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-4 text-center">স্ক্যানযোগ্য নিশ্চিত করতে কেন্দ্রে লোগোর আকার ছোট রাখা হয়েছে।</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function qrGenerator() {
        return {
            link: '{{ route('home') }}',
            color: '#a31c42',
            size: '300',
            url: '',
            copied: false,
            init() {
                this.updateUrl();
            },
            updateUrl() {
                if (!this.link.trim()) return;
                this.url = '{{ route('admin.qrcode.generate') }}?data=' + encodeURIComponent(this.link) +
                    '&color=' + encodeURIComponent(this.color) +
                    '&size=' + encodeURIComponent(this.size);
            },
            copyLink() {
                if (this.url) {
                    navigator.clipboard.writeText(this.url).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 1500);
                    });
                }
            }
        }
    }
</script>
@endsection