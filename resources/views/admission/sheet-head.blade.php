{{--
     Print/PDF styling for the shared A4 admission sheet, used by both the
     admin print view and the applicant's token link so the two can never
     drift apart. Expects nothing.
 --}}
    {{-- Uses the project's compiled Tailwind 4 build; the v3 Play CDN does not
         ship v4-only utilities and fails entirely without internet. --}}
    @vite(['resources/css/app.css'])
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
        /* The app stylesheet pins the root to 18px so the website can use
           pixel-based breakpoints. This sheet is a fixed A4 layout measured
           against the standard 16px root, so pin it back or every rem utility
           grows 12.5% and the content overflows its page. */
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
            size: A4;
            margin: 0;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                gap: 0;
                padding: 0;
                margin: 0;
            }

            .page-container {
                box-shadow: none;
                border: none;
                margin: 0;
                min-height: 0 !important;
                height: auto !important;
                page-break-after: always;
            }

            .page-container:last-child {
                page-break-after: auto;
            }
        }
    </style>
    <style>
        /* html2canvas cannot parse Tailwind 4's oklch() colours and aborts the
           whole export, so every colour on the page needs a hex value. This
           covers the sheet and the toolbar, because html2canvas walks the
           cloned document and parses ancestors such as <body> too. Keep in
           step with the colour utilities in admission/sheet.blade.php,
           admission/sheet-actions.blade.php and the <body> of either view. */
        :root {
            --color-white: #ffffff;
            --color-black: #000000;
            --color-blue-600: #2563eb;
            --color-blue-700: #1d4ed8;
            --color-gray-50: #f9fafb;
            --color-gray-100: #f3f4f6;
            --color-gray-300: #d1d5db;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-gray-700: #374151;
            --color-green-600: #16a34a;
            --color-green-700: #15803d;
            --color-pink-700: #be185d;
        }
    </style>
