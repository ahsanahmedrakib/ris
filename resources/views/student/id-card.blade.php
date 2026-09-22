<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>রেশমা ইন্টারন্যাশনাল স্কুল — পরিচয়পত্র</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: "Noto Sans Bengali", "Kalpurush", "SolaimanLipi", Arial, sans-serif;
            background: #e5e7eb;
        }
        body { min-height: 100vh; }

        :root {
            --primary: #a31c42;
            --dark: #8c1d30;
            --light: #ea3c3a;
        }

        .card {
            /* Standard ID-1 card size */
            width: 85.6mm;
            height: 54mm;
            background: #fff;
            border-radius: 3mm;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
        }

        /* ── Header band ── */
        .card-header {
            background: linear-gradient(90deg, #3e0d16, #5a1220 45%, #8c1d30);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2mm 3.5mm;
            flex-shrink: 0;
        }
        .card-header .brand { display: flex; align-items: center; gap: 2mm; }
        .card-header .brand img { width: 7mm; height: 7mm; object-fit: contain; }
        .card-header .bn-name { font-size: 10px; font-weight: 700; line-height: 1.2; }
        .card-header .en-name {
            font-size: 6px; color: rgba(255, 255, 255, 0.75);
            letter-spacing: 1px; text-transform: uppercase; margin-top: 0.4mm;
        }

        /* ── Body ── */
        .card-body {
            flex: 1;
            display: flex;
            padding: 2.5mm 3.5mm;
            gap: 3.2mm;
            min-height: 0;
        }
        .photo {
            width: 27mm;
            height: 36mm;
            border-radius: 1.2mm;
            border: 1px solid #e2d3d6;
            background: #faf4f5;
            flex-shrink: 0;
            overflow: hidden;
        }
        .photo img { width: 100%; height: 100%; object-fit: cover; }
        .photo .placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: #a31c42;
            font-size: 26px; font-weight: 700;
        }

        .info {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .info .student-name {
            font-size: 12px; font-weight: 800; color: #1f1416; line-height: 1.25;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .info .student-id {
            font-size: 7px; color: var(--primary); font-weight: 700;
            letter-spacing: 0.4px; margin-top: 0.5mm;
        }
        .info table { width: 100%; border-collapse: collapse; margin-top: 0.6mm; }
        .info table td { font-size: 7px; padding: 0.55mm 0; vertical-align: top; }
        .info table td.k { color: #8b7c80; white-space: nowrap; width: 1%; padding-right: 2mm; }
        .info table td.v { color: #2b1d20; font-weight: 600; }

        /* ── QR row (bottom of the info column, always visible) ── */
        .qr-row {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2mm;
            border-top: 0.3mm solid #f0e6e8;
            padding-top: 1.4mm;
        }
        .qr-box {
            background: #fff;
            border: 0.3mm solid #eee;
            border-radius: 1.2mm;
            padding: 0.7mm;
            flex-shrink: 0;
        }
        .qr-box img { width: 12mm; height: 12mm; display: block; }
        .qr-caption {
            font-size: 6.5px; color: #7a6a6e; line-height: 1.5; text-align: right;
        }
        .qr-caption strong { color: var(--primary); font-size: 7px; display: block; }

        /* ── Page / print ── */
        .page {
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 5mm; padding: 8mm;
        }
        .hint { font-size: 12px; color: #6b7280; text-align: center; }

        @page { size: 85.6mm 54mm; margin: 0; }
        @media print {
            body { background: #fff; }
            .page { min-height: auto; padding: 0; gap: 0; }
            .hint { display: none; }
            .card {
                box-shadow: none; border-radius: 0;
                width: 85.6mm; height: 54mm;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            {{-- Header --}}
            <div class="card-header">
                <div class="brand">
                    <img src="{{ asset('logo-white.png') }}" alt="লোগো">
                    <div>
                        <div class="bn-name">রেশমা ইন্টারন্যাশনাল স্কুল</div>
                        <div class="en-name">Resma International School</div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">
                <div class="photo">
                    @if($student->user?->avatar)
                        <img src="{{ asset('storage/'.$student->user->avatar) }}" alt="ছবি">
                    @else
                        <div class="placeholder">
                            {{ mb_substr($student->user?->name ?? 'ছ', 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="info">
                    <div class="student-name">{{ mb_strtoupper($student->user?->name ?? '-', 'UTF-8') }}</div>
                    <div class="student-id">ID: {{ $student->id }} &middot; {{ $student->admission_no ?? '-' }}</div>
                    <table>
                        <tr>
                            <td class="k">শ্রেণি</td>
                            <td class="v">{{ $student->classRoom?->name ?? '-' }} @if($student->section)({{ $student->section }})@endif</td>
                        </tr>
                        <tr>
                            <td class="k">রোল নং</td>
                            <td class="v">{{ $student->roll_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="k">জন্ম</td>
                            <td class="v">{{ $student->date_of_birth?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="k">রক্ত</td>
                            <td class="v">{{ $student->blood_group ?? '-' }}</td>
                        </tr>
                    </table>

                    {{-- QR always visible at the bottom --}}
                    <div class="qr-row">
                        <div class="qr-box">
                            <img src="{{ $qrCode }}" alt="QR">
                        </div>
                        <div class="qr-caption">
                            ইস্যু: {{ now()->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="hint">প্রিন্ট করতে Ctrl+P চাপুন — পৃষ্ঠার আকার স্বয়ংক্রিয়ভাবে কার্ডের আকারে সেট হয়ে যাবে।</p>
    </div>
</body>
</html>