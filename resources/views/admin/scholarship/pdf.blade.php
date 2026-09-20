<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form & Admit Card - {{ $registration->registration_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tiro Bangla', serif;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        .print-page {
            width: 210mm !important;
            max-width: 210mm !important;
            box-sizing: border-box;
            background: white;
            margin: 0 auto;
            overflow: hidden;
        }

        @media screen {
            .print-page {
                min-height: 297mm;
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
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .print-page {
                width: 210mm !important;
                min-height: auto;
                box-shadow: none !important;
                border: none !important;
            }
        }

        .print-page {
            width: 210mm;
            min-height: auto;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen py-10">

    <div class="no-print mb-6 flex flex-wrap items-center justify-center gap-3">
        <button type="button" onclick="window.print()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
            Print - প্রিন্ট করুন
        </button>

        <button type="button" id="downloadPdfBtn" onclick="downloadAdmitPdf()"
            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded shadow transition">
            Download PDF - পিডিএফ ডাউনলোড করুন
        </button>
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
                <div class="border-t border-black pt-1 px-4 text-center">প্রধান শিক্ষক</div>
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

            <div class="flex justify-between items-center mt-8 text-base font-bold">
                <div class="border-t border-black pt-1 px-4 text-center invisible">শিক্ষার্থীর স্বাক্ষর</div>
                <p class="pt-12 italic">This is system generated admit card, no sign required.</p>
                <div class="border-t border-black pt-1 px-4 text-center">প্রধান শিক্ষক</div>
            </div>
        </div>

    </div>

    {{-- Download PDF Script --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAdmitPdf() {
            const element = document.getElementById('admit-card');
            const btn = document.getElementById('downloadPdfBtn');
            const fileName = 'admit-{{ $registration->registration_no }}.pdf';

            if (!element) {
                alert('Admit card element পাওয়া যায়নি।');
                return;
            }
            if (typeof html2pdf === 'undefined') {
                alert('html2pdf লোড হয়নি। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'ডাউনলোড হচ্ছে...';

            // Hide external images temporarily if they cause CORS errors
            const images = element.querySelectorAll('img');
            const prevSrc = [];
            images.forEach(function(img, i) {
                prevSrc[i] = img.src;
                // Optional: comment next line if logo must appear in PDF
                // img.style.visibility = 'hidden';
                img.crossOrigin = 'anonymous';
            });

            const opt = {
                filename: fileName,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: true, // see errors in F12 Console
                    scrollX: 0,
                    scrollY: 0,
                    backgroundColor: '#ffffff',
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait',
                },
                pagebreak: {
                    mode: ['avoid-all']
                },
                enableLinks: false,
            };

            html2pdf()
                .set(opt)
                .from(element)
                .toPdf()
                .get('pdf')
                .then(function(pdf) {
                    // Keep only first page (removes empty page 2)
                    const total = pdf.internal.getNumberOfPages();
                    for (let i = total; i > 1; i--) {
                        pdf.deletePage(i);
                    }
                    pdf.save(fileName);
                })
                .then(function() {
                    btn.disabled = false;
                    btn.textContent = 'Download PDF';
                })
                .catch(function(err) {
                    console.error('PDF error:', err);
                    btn.disabled = false;
                    btn.textContent = 'Download PDF';
                    alert('PDF ডাউনলোড ব্যর্থ: ' + (err && err.message ? err.message : String(err)));
                });
        }
    </script>


</body>

</html>
