<div>
    {{-- ═══ Success (public) ═══ --}}
    @if ($saved && !$adminMode)
        <div class="bg-white rounded-2xl border border-ris-primary shadow-card p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="font-heading font-bold text-2xl text-gray-900">রেজিস্ট্রেশন সফল হয়েছে!</h3>
            <p class="mt-2 text-sm text-gray-500">আপনার মেধাবৃত্তি রেজিস্ট্রেশন সম্পন্ন হয়েছে। আপনার রেজিস্ট্রেশন
                নম্বর:</p>
            <div
                class="mt-5 inline-flex items-center gap-3 px-6 py-3 rounded-xl bg-ris-primary/10 border border-ris-primary/20">
                <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span
                    class="font-heading font-bold text-xl text-ris-primary tracking-wider">{{ $registrationNo }}</span>
            </div>
            <div class="mt-5 text-sm text-gray-500 leading-relaxed">
                রেজিস্ট্রেশন নম্বরটি কপি করে সংরক্ষণ করুন। প্রবেশপত্র তৈরির সময় এটি প্রয়োজন হবে।
            </div>
            <div class="mt-6">
                <button type="button" wire:click="resetForm" class="btn-secondary text-sm px-6 py-2.5">নতুন
                    রেজিস্ট্রেশন</button>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border shadow-card overflow-hidden">

            {{-- Card Header --}}
            <div class="gradient-logo px-6 py-6 text-center">
                <h2 class="font-heading font-bold text-white text-xl sm:text-2xl leading-snug">আক্‌রামুন্নেছা-জলিল ও
                    রেশমা</h2>
                <h2 class="font-heading font-bold text-white text-xl sm:text-2xl mt-1">রেজাউল মেধাবৃত্তি ২০২৬</h2>
                <p class="mt-2 text-white/80 text-sm">রেজিস্ট্রেশন ফরম</p>
            </div>

            <form wire:submit.prevent="submit" class="p-6 sm:p-8 space-y-6" novalidate>

                {{-- Registration No --}}
                <div
                    class="bg-gray-50 border border-dashed border-ris-primary/30 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-2 text-ris-primary shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span class="text-sm font-semibold">রেজিস্ট্রেশন নং</span>
                    </div>
                    <div class="flex-1 min-w-0 text-center sm:text-left">
                        <div wire:loading wire:target="classNo"
                            class="inline-block h-5 w-44 sm:w-52 rounded-md bg-ris-primary/15 animate-pulse"></div>
                        <span wire:loading.remove wire:target="classNo"
                            class="font-heading font-bold text-ris-primary tracking-wider text-sm sm:text-base">
                            {{ $registrationNo ?: 'শ্রেণি নির্বাচন করলে স্বয়ংক্রিয়ভাবে তৈরি হবে' }}
                        </span>
                    </div>
                </div>

                {{-- Student & Parents --}}
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="studentName" class="block text-sm font-medium text-gray-700 mb-1.5">শিক্ষার্থীর নাম
                            <span class="text-ris-primary">*</span></label>
                        <input type="text" id="studentName" wire:model.live="studentName" autocomplete="name"
                            placeholder="শিক্ষার্থীর সম্পূর্ণ নাম লিখুন"
                            class="@error('studentName') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        @error('studentName')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="fatherName" class="block text-sm font-medium text-gray-700 mb-1.5">পিতার নাম
                                <span class="text-ris-primary">*</span></label>
                            <input type="text" id="fatherName" wire:model.live="fatherName" autocomplete="off"
                                placeholder="পিতার সম্পূর্ণ নাম লিখুন"
                                class="@error('fatherName') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('fatherName')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="motherName" class="block text-sm font-medium text-gray-700 mb-1.5">মাতার নাম
                                <span class="text-ris-primary">*</span></label>
                            <input type="text" id="motherName" wire:model.live="motherName" autocomplete="off"
                                placeholder="মাতার সম্পূর্ণ নাম লিখুন"
                                class="@error('motherName') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            @error('motherName')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="schoolName" class="block text-sm font-medium text-gray-700 mb-1.5">স্কুলের নাম <span
                                class="text-ris-primary">*</span></label>
                        <input type="text" id="schoolName" wire:model.live="schoolName" autocomplete="off"
                            placeholder="স্কুলের সম্পূর্ণ নাম লিখুন"
                            class="@error('schoolName') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        @error('schoolName')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Class, Roll, Mobile --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="classNo" class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণি <span
                                class="text-ris-primary">*</span></label>
                        <select id="classNo" wire:model.live="classNo"
                            class="@error('classNo') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                            <option value="">-- শ্রেণি নির্বাচন করুন --</option>
                            @foreach ($classOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('classNo')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rollNo" class="block text-sm font-medium text-gray-700 mb-1.5">রোল নং <span
                                class="text-ris-primary">*</span></label>
                        <input type="text" id="rollNo" wire:model.live="rollNo" inputmode="numeric"
                            placeholder="রোল নম্বর লিখুন"
                            class="@error('rollNo') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        @error('rollNo')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobileNo" class="block text-sm font-medium text-gray-700 mb-1.5">মোবাইল নং <span
                                class="text-ris-primary">*</span></label>
                        <input type="tel" id="mobileNo" wire:model.live="mobileNo" inputmode="numeric"
                            autocomplete="tel" placeholder="01XXXXXXXXX"
                            class="@error('mobileNo') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        @error('mobileNo')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- bKash Payment --}}
                <div class="border border-ris-primary/20 bg-ris-primary/5 rounded-xl p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">💳</span>
                        <h3 class="font-heading font-semibold text-ris-dark">রেজিস্ট্রেশন ফি পরিশোধ। রেজিস্ট্রেশন ফি ২০০ টাকা।</h3>
                    </div>

                    @if ($adminMode)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">পেমেন্ট মাধ্যম</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    class="flex items-center gap-2.5 bg-white border rounded-lg px-4 py-3 cursor-pointer transition-colors has-checked:border-ris-primary has-checked:bg-ris-primary/5">
                                    <input type="radio" value="bkash" wire:model.live="paymentMethod"
                                        class="accent-ris-primary">
                                    <span class="text-sm font-medium text-gray-700">বিকাশ</span>
                                </label>
                                <label
                                    class="flex items-center gap-2.5 bg-white border rounded-lg px-4 py-3 cursor-pointer transition-colors has-checked:border-ris-primary has-checked:bg-ris-primary/5">
                                    <input type="radio" value="cash" wire:model.live="paymentMethod"
                                        class="accent-ris-primary">
                                    <span class="text-sm font-medium text-gray-700">ক্যাশ</span>
                                </label>
                            </div>
                            @error('paymentMethod')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if (!$adminMode || $paymentMethod !== 'cash')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1.5">বিকাশ (পার্সোনাল) নম্বরে টাকা পাঠান:</p>
                                <div x-data="{ copied: false }" class="flex items-center gap-3">
                                    <div
                                        class="flex-1 bg-white border border-dashed border-ris-primary/40 rounded-lg px-4 py-3 font-heading font-bold text-ris-primary text-lg tracking-wider text-center select-all">
                                        01618197972
                                    </div>
                                    <button type="button"
                                        @click="navigator.clipboard.writeText('01618197972').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg bg-ris-primary text-white text-xs font-medium hover:bg-ris-dark transition-colors"
                                        x-text="copied ? 'কপি হয়েছে' : 'কপি করুন'"></button>
                                </div>
                            </div>
                            <div>
                                <label for="bkashNo" class="block text-sm font-medium text-gray-700 mb-1.5">যে বিকাশ
                                    নম্বর থেকে টাকা পাঠিয়েছেন <span class="text-ris-primary">*</span></label>
                                <input type="tel" id="bkashNo" wire:model.live="bkashNo" inputmode="numeric"
                                    placeholder="01XXXXXXXXX"
                                    class="@error('bkashNo') border-red-400 @enderror w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                                @error('bkashNo')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1.5 text-xs text-gray-400">টাকা পাঠানোর পর যে নম্বর থেকে পাঠিয়েছেন সেটি
                                    লিখুন।</p>
                            </div>
                        </div>
                    @else
                        <div
                            class="bg-white border border-emerald-200 rounded-lg px-4 py-3 text-sm text-gray-600 flex items-center gap-3">
                            <span class="text-lg">💵</span>
                            <span>ক্যাশ পদ্ধতিতে পরিশোধ করা হয়েছে। অফিসে টাকা জমা নেওয়া হয়েছে।</span>
                        </div>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary w-full sm:w-auto text-sm px-8 py-3">
                        যাচাই করে জমা দিন
                    </button>
                    <p class="text-xs text-gray-400">জমা দেওয়ার আগে তথ্য যাচাইয়ের জন্য একটি পপ-আপ দেখানো হবে।</p>
                </div>
            </form>
        </div>

        {{-- ═══ Confirmation Modal ═══ --}}
        @if ($confirming)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60" wire:click="cancelConfirmation"></div>
                <div
                    class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto animate-slide-up">
                    <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                        <h3 class="font-heading font-bold text-white text-lg">তথ্য যাচাই করুন</h3>
                        <button type="button" wire:click="cancelConfirmation"
                            class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div
                            class="rounded-xl border border-ris-primary/20 bg-ris-primary/5 px-4 py-3 mb-5 text-center">
                            <p class="text-xs text-gray-500 mb-1">রেজিস্ট্রেশন নং</p>
                            <p class="font-heading font-bold text-lg text-ris-primary tracking-wider">
                                {{ $registrationNo }}</p>
                        </div>

                        <dl class="space-y-3 text-sm">
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">শিক্ষার্থীর নাম</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $studentName }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">পিতার নাম</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $fatherName }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">মাতার নাম</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $motherName }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">স্কুলের নাম</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $schoolName }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">শ্রেণি</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $classOptions[$classNo] ?? '-' }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">রোল নং</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $rollNo ?: '-' }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">মোবাইল নং</dt>
                                <dd class="flex-1 font-medium text-gray-900">{{ $mobileNo }}</dd>
                            </div>
                            <div class="flex gap-4">
                                <dt class="w-36 shrink-0 text-gray-500">পেমেন্ট মাধ্যম</dt>
                                <dd class="flex-1 font-medium text-gray-900">
                                    {{ $paymentMethod === 'cash' ? 'ক্যাশ' : 'বিকাশ' }}</dd>
                            </div>
                            @if ($paymentMethod !== 'cash')
                                <div class="flex gap-4">
                                    <dt class="w-36 shrink-0 text-gray-500">বিকাশ (পাঠিয়েছেন)</dt>
                                    <dd class="flex-1 font-medium text-gray-900">{{ $bkashNo }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div
                            class="mt-5 p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-500 leading-relaxed">
                            @if ($paymentMethod === 'cash')
                                পেমেন্টটি <span class="font-semibold text-ris-primary">ক্যাশ</span> হিসেবে রেকর্ড
                                করা হবে।
                            @else
                                টাকা পাঠানো হয়েছে: <span class="font-semibold text-ris-primary">বিকাশ পার্সোনাল
                                    ০১৬১৮১৯৭৯৭২</span>
                            @endif
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="button" wire:click="cancelConfirmation"
                                class="btn-secondary w-full sm:w-1/2">ফিরে যান</button>
                            <button type="button" wire:click="confirm" wire:loading.attr="disabled"
                                wire:loading.class="opacity-60" class="btn-primary w-full sm:w-1/2">
                                <span wire:loading wire:target="confirm" class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    জমা হচ্ছে...
                                </span>
                                <span wire:loading.remove wire:target="confirm">নিশ্চিত করে জমা দিন</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
