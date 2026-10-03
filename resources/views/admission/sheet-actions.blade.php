{{--
     Actions for the shared A4 admission sheet: print, and save the file without
     a dialog.

     Expects: $admission.

     Included by both the admin print view and the applicant's token link so the
     two pages always offer the same actions. Kept free of inline event handlers
     and Alpine, because the public page has neither, and it binds with
     addEventListener under the CSP nonce instead.

     "প্রিন্ট করুন" hands off to the browser, which honours the @page/A4 rules in
     admission/sheet-head.blade.php. "ডাউনলোড" rasterises the same markup through
     html2pdf and saves straight to disk.

     html2pdf works on a clone of the document inside an iframe, and it does not
     wait for that clone to be ready. Three things have to be settled before it
     starts or the export comes out wrong:
       * the Bangla webfont. Rasterising before it loads makes the browser fall
         back to a font with no conjunct support, which drops the reph in ভর্তি
         and turns ফরম into ফ্রম, so the heading is silently misspelled.
       * every <img>. The student photo is a ~1 MB JPEG; if the clone has not
         decoded it, the photo area exports blank.
       * the scroll offsets. Without them html2canvas measures from the current
         scroll position, so the lower A4 page lands outside the rasterised
         region and exports empty.

     Colours also need hex values, defined in admission/sheet-head.blade.php,
     because html2canvas cannot parse Tailwind 4's oklch() and aborts the export.
--}}
<div class="no-print my-6 flex flex-wrap items-center justify-center gap-3">
    <button type="button" id="printSheetBtn"
        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded shadow transition cursor-pointer">
        প্রিন্ট করুন
    </button>
    <button type="button" id="downloadSheetBtn"
        class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded shadow transition cursor-pointer">
        ফরম ডাউনলোড করুন (PDF)
    </button>
    <p id="downloadSheetHint" class="w-full text-center text-xs text-gray-600" role="status" aria-live="polite"></p>
</div>

{{-- Self-hosted so exporting a form never depends on a third-party CDN. --}}
<script src="{{ asset('vendor/html2pdf.bundle.min.js') }}" defer></script>
<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {
        var printBtn = document.getElementById('printSheetBtn');
        var downloadBtn = document.getElementById('downloadSheetBtn');
        var hint = document.getElementById('downloadSheetHint');
        var sheet = document.getElementById('admissionSheet');

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

        // Resolves once the fonts and every image on the sheet are usable, so
        // the clone html2pdf builds starts from the same state the visitor sees.
        var settle = function () {
            var waits = [];

            if (document.fonts && document.fonts.ready) {
                waits.push(document.fonts.ready);
            }

            Array.prototype.forEach.call(document.images, function (img) {
                if (img.decode) {
                    // decode() waits for the bitmap, not just the response headers.
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
            // The export bundle did not load. Say so rather than leaving a
            // button that silently does nothing.
            downloadBtn.disabled = true;
            restore('ফরম ডাউনলোডের জন্য প্রয়োজনীয় ফাইলটি লোড হয়নি। প্রিন্ট অপশনটি ব্যবহার করুন।');

            return;
        }

        downloadBtn.addEventListener('click', function () {
            var filename = '{{ $admission->admission_no ? 'admission-form-' . $admission->admission_no : 'admission-form' }}.pdf';

            downloadBtn.disabled = true;
            downloadBtn.textContent = 'তৈরি হচ্ছে...';
            if (hint) {
                hint.textContent = '';
            }

            settle().then(function () {
                return window.html2pdf().set({
                    margin: 0,
                    filename: filename,
                    // JPEG, not PNG: encoding two 1588x2245 pages as PNG takes
                    // long enough that the button looks broken. At quality 0.98
                    // the ruled lines and small Bangla text still hold up.
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        logging: false,
                        // Always rasterise from the top of the sheet. Left to its
                        // own devices html2canvas measures from the current
                        // scroll position, which is what emptied the second page.
                        scrollX: 0,
                        scrollY: 0,
                        // html2canvas does not hand letter-spacing to the
                        // browser as a property of the run: it walks the glyphs
                        // and spaces them one by one, so the shaping of a Bangla
                        // cluster is thrown away and the conjuncts come apart in
                        // the PDF (the heading read "ভরতি আবেদন অফ্রম"). The
                        // tracking here is decorative, so drop it for the
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
                    // 'avoid-all' renders the sheet as one continuous flow and
                    // lets jsPDF cut it at the A4 boundaries, which is what the
                    // min-h-[297mm] pages are already sized for. Asking for
                    // explicit breaks instead makes html2pdf split the canvas
                    // into page groups, and because the sheet also carries a
                    // CSS break-after rule the two disagree: the result was
                    // three groups, the middle one empty, with the ভর্তি স্মারক
                    // pushed onto the third and then lost.
                    pagebreak: { mode: ['avoid-all'] },
                    enableLinks: false
                }).from(sheet)
                    .toContainer()
                    .toCanvas()
                    .toImg()
                    .toPdf()
                    .get('pdf')
                    .then(function (pdf) {
                        // html2pdf rounds the canvas height up, so a sheet that
                        // measures exactly two A4 pages can come back with a
                        // blank third. Drop trailing pages, never inner ones, so
                        // the receipt cannot be the page that gets removed.
                        var expected = sheet.querySelectorAll('.page-container').length;

                        while (pdf.internal.getNumberOfPages() > expected) {
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
