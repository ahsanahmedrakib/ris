<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form & Admit Card - {{ $registration->registration_no }}</title>
    {{-- Uses the project's compiled Tailwind 4 build; the v3 Play CDN does not
         ship utilities such as w-62.5 and fails entirely without internet. --}}
    @vite(['resources/css/app.css'])
    {{-- Self-hosted so the sheet never depends on Google Fonts being reachable. --}}
    <style>
        @font-face {
            font-family: 'Tiro Bangla';
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url('{{ asset('fonts/tiro-bangla-bengali-400-normal.woff2') }}') format('woff2');
            unicode-range: U+0980-09FF;
        }

        @font-face {
            font-family: 'Tiro Bangla';
            font-style: italic;
            font-weight: 400;
            font-display: block;
            src: url('{{ asset('fonts/tiro-bangla-bengali-400-italic.woff2') }}') format('woff2');
            unicode-range: U+0980-09FF;
        }

        @font-face {
            font-family: 'Tiro Bangla';
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url('{{ asset('fonts/tiro-bangla-latin-400-normal.woff2') }}') format('woff2');
            unicode-range: U+0000-024F, U+2000-206F, U+2190-21BB;
        }
    </style>
    <style>
        /* html2canvas, bundled inside html2pdf, cannot parse the oklch() colours
           Tailwind 4 emits, and aborts the whole render on the first one.
           Restate the handful this sheet uses as hex, toolbar included, because
           html2canvas walks the cloned document and parses ancestors such as
           <body> too. Unlayered declarations win over Tailwind's @layer theme
           block. */
        :root {
            --color-white: #ffffff;
            --color-black: #000000;
            --color-gray-100: #f3f4f6;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-blue-600: #2563eb;
            --color-blue-700: #1d4ed8;
            --color-emerald-600: #059669;
            --color-emerald-700: #047857;
        }

        /* The app stylesheet pins the root to 18px so the website can use
           pixel-based breakpoints. This sheet is a fixed A4 layout measured
           against the standard 16px root, so pin it back or every rem utility
           grows 12.5% and the card spills onto a second sheet. */
        html {
            font-size: 16px;
            font-family: 'Tiro Bangla', serif;
        }

        /* The app's @layer base also swaps headings onto the website heading
           font, which the v3 CDN never did. Keep the whole sheet on one family. */
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Tiro Bangla', serif;
        }

        body {
            font-family: 'Tiro Bangla', serif;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        .print-page {
            width: 210mm !important;
            max-width: 210mm !important;
            min-height: 297mm;
            box-sizing: border-box;
            background: white;
            margin: 0 auto;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media screen {
            .print-page {
                border: 1px solid #ccc;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            html,
            body {
                width: 210mm;
                /* The screen layout centres the card with min-h-screen and a
                   py-10 gutter. Both resolve against the viewport, so in print
                   they push the sheet off the page and emit a blank second
                   sheet. */
                min-height: 0 !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .print-page {
                width: 210mm !important;
                min-height: 297mm;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen py-10">

    {{--
         This page is a standalone document rendered by both admin.scholarship.pdf
         and the applicant's token link, so it has no layout and therefore no
         Alpine. Buttons must bind with addEventListener under the CSP nonce, the
         same way resources/views/admission/sheet-actions.blade.php does; an
         @click directive here is inert markup and the button silently does
         nothing.
     --}}
    <div class="no-print my-4 flex flex-wrap items-center justify-center gap-3">
        <button type="button" id="printSheetBtn"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded shadow transition cursor-pointer">
            প্রিন্ট করুন - Print
        </button>

        <button type="button" id="downloadSheetBtn"
            class="bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded shadow transition cursor-pointer">
            পিডিএফ ডাউনলোড করুন - Download PDF
        </button>

        <p id="downloadSheetHint" class="w-full text-center text-xs text-gray-600" role="status" aria-live="polite"></p>
    </div>

    <div id="admit-card" class="print-page text-black text-sm relative px-6 py-3">

        <!-- ================= REGISTRATION FORM ================= -->
        <div class="relative pb-8 border-b-2 border-dashed border-gray-500">

            <div class="flex justify-between items-start mb-2">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                        class="h-14 lg:h-16 w-auto mx-auto mb-6 object-contain">
                </div>
                <div class="border-2 border-black px-4 py-1.5 text-base font-bold w-62.5">
                    রেজিস্ট্রেশন নং: {{ $registration->registration_no }}
                </div>
            </div>

            <div class="text-center my-3">
                <h1 class="text-3xl font-bold text-black tracking-normal">আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি
                    ২০২৬</h1>
                <h2 class="text-xl font-bold text-black underline decoration-2 underline-offset-4 mt-1">রেজিস্ট্রেশন ফরম
                </h2>
            </div>

            <div class="space-y-4 mt-4 text-base">
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">শিক্ষার্থীর নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->student_name }}</div>
                </div>
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">পিতার নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->father_name }}</div>
                </div>
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">মাতার নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->mother_name }}</div>
                </div>
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">স্কুলের নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->school_name }}</div>
                </div>
                <div class="flex items-end gap-4">
                    <div class="flex items-end flex-1">
                        <span class="font-bold whitespace-nowrap">শ্রেণিঃ</span>
                        <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                            {{ $classes[$registration->class_no] ?? '-' }}</div>
                    </div>
                    <div class="flex items-end flex-1">
                        <span class="font-bold whitespace-nowrap">রোল নং</span>
                        <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                            {{ $registration->roll_no ?? '-' }}</div>
                    </div>
                    <div class="flex items-end flex-1">
                        <span class="font-bold whitespace-nowrap">মোবাইল নং</span>
                        <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                            {{ $registration->mobile_no }}</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end mt-12 text-base font-bold">
                <div class="border-t border-black pt-1 px-4 text-center">শিক্ষার্থীর স্বাক্ষর</div>
                <div class="flex flex-col items-center min-w-30">
                    <img src="{{ asset('assets/signature.png') }}" alt="প্রধান শিক্ষকের স্বাক্ষর"
                        class="h-14 w-auto object-contain mb-1">
                    <div class="border-t border-black pt-1 px-4 text-center w-full">প্রধান শিক্ষক</div>
                </div>
            </div>
        </div>

        <!-- ================= ADMIT CARD ================= -->
        <div class="relative pt-4">

            <div class="flex justify-between items-start mb-2">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                        class="h-14 lg:h-16 w-auto mx-auto object-contain">
                </div>
                <div class="border-2 border-black px-4 py-1.5 text-base font-bold w-62.5">
                    রেজিস্ট্রেশন নং: {{ $registration->registration_no }}
                </div>
            </div>
            <div class="text-center my-3">
                <h1 class="text-3xl font-bold text-black tracking-normal">আক্‌রামুন্নেছা-জলিল ও রেশমা-রেফাউল মেধাবৃত্তি
                    ২০২৬</h1>
                <h2 class="text-xl font-bold text-black underline decoration-2 underline-offset-4 mt-1">প্রবেশপত্র</h2>
            </div>

            <div class="space-y-4 mt-4 text-base">
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">শিক্ষার্থীর নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->student_name }}</div>
                </div>
                <div class="flex items-end">
                    <span class="font-bold whitespace-nowrap">স্কুলের নামঃ</span>
                    <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                        {{ $registration->school_name }}</div>
                </div>
                <div class="flex items-end gap-6">
                    <div class="flex items-end flex-1">
                        <span class="font-bold whitespace-nowrap">শ্রেণিঃ</span>
                        <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                            {{ $classes[$registration->class_no] ?? '-' }}</div>
                    </div>
                    <div class="flex items-end flex-1">
                        <span class="font-bold whitespace-nowrap">রোল নং</span>
                        <div class="border-b-2 border-black grow ml-2 text-xl font-bold text-center">
                            {{ $registration->roll_no ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 space-y-2 text-base font-bold">
                <p class="underline decoration-1 underline-offset-2">পরীক্ষার তারিখ ও সময়ঃ</p>
                <p class="text-xl font-bold">
                    ৩০ অক্টোবর ২০২৬, শুক্রবার,
                    @if (in_array($registration->class_no, [1, 2]))
                        সকাল ৯:০০টা - ১০:০০টা
                    @else
                        সকাল ১০:৩০টা - দুপুর ১২:৩০টা
                    @endif
                </p>
                <p class="text-xl font-bold">স্থানঃ রেশমা ইন্টারন্যাশনাল স্কুল, গোপালগঞ্জ</p>
                <p class="text-xl font-bold">মোবাইলঃ ০১৬১৯ ০০৭ ০০৬</p>
            </div>

            <div class="flex justify-between items-end text-base font-bold">
                <div class="border-t border-black pt-1 px-4 text-center invisible">শিক্ষার্থীর স্বাক্ষর</div>
                <div class="flex flex-col items-center min-w-30">
                    <img src="{{ asset('assets/signature.png') }}" alt="প্রধান শিক্ষকের স্বাক্ষর"
                        class="h-14 w-auto object-contain mb-1">
                    <div class="border-t border-black pt-1 px-4 text-center w-full">প্রধান শিক্ষক</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Download PDF Script --}}

    {{-- Self-hosted so the download works without reaching cdnjs. --}}
    <script src="{{ asset('vendor/html2pdf.bundle.min.js') }}" defer></script>
    <script nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            var printBtn = document.getElementById('printSheetBtn');
            var downloadBtn = document.getElementById('downloadSheetBtn');
            var hint = document.getElementById('downloadSheetHint');
            var sheet = document.getElementById('admit-card');

            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    window.print();
                });
            }

            if (!downloadBtn || !sheet) {
                return;
            }

            var idleLabel = downloadBtn.textContent;

            var restore = function (message) {
                downloadBtn.disabled = false;
                downloadBtn.textContent = idleLabel;
                if (hint) {
                    hint.textContent = message || '';
                }
            };

            // Resolves once the fonts and every image on the sheet are usable.
            // html2pdf rasterises a clone of the document inside an iframe and
            // does not wait for that clone to be ready: rasterising before the
            // Bangla webfont loads makes the browser fall back to a font with no
            // conjunct support (the reph in ভর্তি is dropped and ফরম becomes
            // ফ্রম), and an undecoded logo/signature exports blank.
            var settle = function () {
                var waits = [];

                if (document.fonts && document.fonts.ready) {
                    waits.push(document.fonts.ready);
                }

                Array.prototype.forEach.call(document.images, function (img) {
                    if (img.decode) {
                        waits.push(img.decode().catch(function () { }));
                    } else if (!img.complete) {
                        waits.push(new Promise(function (resolve) {
                            img.addEventListener('load', resolve, { once: true });
                            img.addEventListener('error', resolve, { once: true });
                        }));
                    }
                });

                return Promise.all(waits);
            };

            if (typeof window.html2pdf !== 'function') {
                downloadBtn.disabled = true;
                restore('ফরম ডাউনলোডের জন্য প্রয়োজনীয় ফাইলটি লোড হয়নি। প্রিন্ট অপশনটি ব্যবহার করুন।');

                return;
            }

            downloadBtn.addEventListener('click', function () {
                var filename = 'admit-{{ $registration->registration_no }}.pdf';

                downloadBtn.disabled = true;
                downloadBtn.textContent = 'পিডিএফ ডাউনলোড হচ্ছে - Downloading PDF...';
                if (hint) {
                    hint.textContent = '';
                }

                settle().then(function () {
                    return window.html2pdf().set({
                        margin: 0,
                        filename: filename,
                        // JPEG, not PNG: at scale 2 this sheet rasterises to
                        // ~1600x2260 px and PNG encoding is slow enough that
                        // the button looks broken.
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: '#ffffff',
                            logging: false,
                            // Always rasterise from the top of the card. Left to
                            // its own devices html2canvas measures from the
                            // current scroll position, so the lower half of the
                            // admit card lands outside the rasterised region and
                            // exports empty.
                            scrollX: 0,
                            scrollY: 0,
                            // html2canvas does not hand letter-spacing to the
                            // browser as a property of the run: it walks the
                            // glyphs and spaces them one by one, so the shaping
                            // of a Bangla cluster is thrown away and the
                            // conjuncts come apart in the PDF. The tracking on
                            // screen is decorative, so drop it for the
                            // rasterised clone only -- the page on screen and the
                            // browser print keep their original spacing.
                            onclone: function (doc) {
                                var reset = doc.createElement('style');
                                reset.textContent =
                                    '*, *::before, *::after { letter-spacing: normal !important; word-spacing: normal !important; }';
                                doc.head.appendChild(reset);
                            }
                        },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        // 'avoid-all' renders the card as one continuous flow
                        // and lets jsPDF cut it at the A4 boundary, which is what
                        // the 210mm/297mm .print-page is already sized for.
                        pagebreak: { mode: ['avoid-all'] },
                        enableLinks: false
                    }).from(sheet)
                        .toContainer()
                        .toCanvas()
                        .toImg()
                        .toPdf()
                        .get('pdf')
                        .then(function (pdf) {
                            // html2pdf rounds the canvas height up, so a card
                            // measuring exactly one A4 page can come back with a
                            // blank second. Drop trailing pages only.
                            while (pdf.internal.getNumberOfPages() > 1) {
                                pdf.deletePage(pdf.internal.getNumberOfPages());
                            }

                            pdf.save(filename);
                            restore();
                        });
                }).catch(function (error) {
                    restore('ফরম ডাউনলোড করা যায়নি। প্রিন্ট অপশনটি ব্যবহার করুন।');

                    if (window.console) {
                        window.console.error(error);
                    }
                });
            });
        });
    </script>


</body>

</html>
