{{-- Public "add your testimonial" — modal form (submitted for admin approval) --}}
<div x-data="testimonialSubmit()" @if (old('_testimonial')) x-init="$nextTick(() => openForm())" @endif>
    <div class="flex justify-center mt-2">
        <button type="button" @click="openForm()"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-ris-primary text-white font-heading font-medium text-sm shadow-btn hover:bg-ris-dark hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4M3 8l4-4 4 4M5 4v4M21 10l-4-4-4 4M19 8v4" />
            </svg>
            আপনার মতামত দিন
        </button>
    </div>

    {{-- Modal --}}
    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
        class="fixed inset-0 z-9999 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80" @click="closeForm()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="gradient-logo px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="font-heading font-bold text-white text-lg">আপনার মতামত জানান</h3>
                <button type="button" @click="closeForm()"
                    class="text-white/80 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="testimonialForm" action="{{ route('testimonials.submit') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-4" novalidate>
                @csrf
                <input type="hidden" name="_testimonial" value="1">

                @if (session('success'))
                    <div
                        class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
                        {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                        {{ session('error') }}</div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">আপনার নাম <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="আপনার নাম লিখুন"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors {{ $errors->first('name') ? 'border-red-400' : 'border-gray-200' }}">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">পদবি / সম্পর্ক</label>
                    <input type="text" name="designation" value="{{ old('designation') }}"
                        placeholder="যেমন: অভিভাবক, শিক্ষার্থী"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">রেটিং</label>
                    <div class="flex items-center gap-1 py-1">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="rating = i"
                                class="cursor-pointer transition-transform hover:scale-110">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path :class="i <= rating ? 'text-amber-400' : 'text-gray-300'"
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">আপনার মন্তব্য <span
                            class="text-red-500">*</span></label>
                    <textarea name="message" rows="4" placeholder="আমাদের সম্পর্কে আপনার অভিজ্ঞতা লিখুন..."
                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors resize-none {{ $errors->first('message') ? 'border-red-400' : 'border-gray-200' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ছবি (ঐচ্ছিক)</label>
                    <input type="file" name="photo" accept="image/*"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white cursor-pointer">
                    @error('photo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG বা WEBP — সর্বোচ্চ ২ এমবি</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeForm()"
                        class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">বাতিল</button>
                    <button type="submit" id="testimonialSubmitBtn"
                        class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm cursor-pointer">জমা
                        দিন</button>
                </div>

                <p class="text-xs text-gray-400 text-center pt-1">জমা দেওয়া মতামত প্রশাসকের অনুমোদনের পর ওয়েবসাইটে
                    প্রকাশিত হবে।</p>
            </form>
        </div>
    </div>
</div>

<script>
    function testimonialSubmit() {
        return {
            open: false,
            rating: {{ (int) old('rating', 5) }},
            init() {
                window.addEventListener('testimonial-submit-success', () => {
                    this.open = false;
                });
                window.addEventListener('testimonial-rating-reset', () => {
                    this.rating = 5;
                });
            },
            openForm() {
                this.open = true;
            },
            closeForm() {
                this.open = false;
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('testimonialForm');
        if (!form) return;

        const pushToast = (type, message) => {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type,
                    message
                }
            }));
        };

        const requiredFields = {
            name: 'আপনার নাম লিখুন।',
            message: 'আপনার মন্তব্য লিখুন।',
        };

        const applyError = (input, add) => {
            input.classList.toggle('border-red-400', add);
            input.classList.toggle('border-gray-200', !add);
        };

        const showFieldError = (name, message) => {
            const input = form.querySelector(`[name="${name}"]`);
            if (!input) return;

            applyError(input, true);

            let p = form.querySelector(`[data-field-error="${name}"]`);
            if (!p) {
                p = document.createElement('p');
                p.setAttribute('data-field-error', name);
                p.className = 'mt-1 text-xs text-red-600';
                input.insertAdjacentElement('afterend', p);
            }
            p.textContent = message;
        };

        const clearErrors = () => {
            form.querySelectorAll('[data-field-error]').forEach(el => el.remove());
            form.querySelectorAll('[name]').forEach(el => applyError(el, false));
        };

        const validateFieldLocal = (name) => {
            const input = form.querySelector(`[name="${name}"]`);
            if (!input) return true;

            if (name === 'photo') {
                const file = input.files && input.files[0];
                if (!file) return true;

                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    showFieldError(name, 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।');
                    return false;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showFieldError(name, 'ছবির আকার ২ এমবির বেশি হতে পারবে না।');
                    return false;
                }
                return true;
            }

            if (!input.value.trim()) {
                showFieldError(name, requiredFields[name] || 'এই ঘরটি আবশ্যক।');
                return false;
            }

            return true;
        };

        const attachValidation = (name) => {
            const input = form.querySelector(`[name="${name}"]`);
            if (!input) return;
            input.addEventListener('blur', () => validateFieldLocal(name));
        };

        ['name', 'message'].forEach(attachValidation);

        const photoInput = form.querySelector('[name="photo"]');
        if (photoInput) {
            photoInput.addEventListener('change', () => validateFieldLocal('photo'));
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            clearErrors();

            let valid = true;
            ['name', 'message', 'photo'].forEach(name => {
                if (!validateFieldLocal(name)) {
                    valid = false;
                }
            });

            if (!valid) {
                const firstError = form.querySelector('[data-field-error]');
                if (firstError) {
                    pushToast('error', firstError.textContent);
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                } else {
                    pushToast('error', 'ফরমের প্রয়োজনীয় ঘরগুলো পূরণ করুন।');
                }
                return;
            }

            const submitBtn = document.getElementById('testimonialSubmitBtn');
            const submitLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'জমা হচ্ছে...';

            fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));

                    if (response.status === 422) {
                        Object.entries(data.errors || {}).forEach(([name, messages]) => {
                            showFieldError(name, messages[0]);
                        });
                        const firstError = form.querySelector('[data-field-error]');
                        if (firstError) {
                            pushToast('error', firstError.textContent);
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else {
                            pushToast('error', data.message ||
                                'মতামত জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                        }
                        return;
                    }

                    if (response.ok) {
                        pushToast('success', data.message ||
                            'আপনার মতামত জমা হয়েছে। প্রশাসকের অনুমোদনের পরে প্রকাশিত হবে।'
                            );
                        form.reset();
                        window.dispatchEvent(new CustomEvent('testimonial-rating-reset'));
                        window.dispatchEvent(new CustomEvent('testimonial-submit-success'));
                        return;
                    }

                    pushToast('error', data.message ||
                        'মতামত জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                })
                .catch(() => {
                    pushToast('error', 'মতামত জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitLabel;
                });
        });
    });
</script>
