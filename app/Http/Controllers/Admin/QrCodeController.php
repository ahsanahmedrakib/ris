<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class QrCodeController extends Controller
{
    public function index(): View
    {
        return view('admin.qrcode.index');
    }

    public function generate(Request $request): Response
    {
        $validated = $request->validate([
            'data' => 'required|string|max:1200',
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'size' => ['nullable', 'integer', 'between:150,600'],
        ]);

        $data = $validated['data'];

        if (! preg_match('#^https?://#i', $data)) {
            $data = 'https://'.$data;
        }

        $rgb = $this->hexToRgb($validated['color'] ?? '#a31c42');
        $target = (int) ($validated['size'] ?? 300);

        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::H,
            'addQuietzone' => true,
            'quietzoneSize' => 4,
            'scale' => 3,
            'outputBase64' => false,
            'bgColor' => [255, 255, 255],
            'drawLightModules' => false,
            'moduleValues' => [
                // dark modules → brand color
                QRMatrix::M_DARKMODULE => $rgb,
                QRMatrix::M_DATA_DARK => $rgb,
                QRMatrix::M_FINDER_DARK => $rgb,
                QRMatrix::M_SEPARATOR_DARK => $rgb,
                QRMatrix::M_ALIGNMENT_DARK => $rgb,
                QRMatrix::M_TIMING_DARK => $rgb,
                QRMatrix::M_FORMAT_DARK => $rgb,
                QRMatrix::M_VERSION_DARK => $rgb,
                QRMatrix::M_QUIETZONE_DARK => $rgb,
                QRMatrix::M_LOGO_DARK => $rgb,
                QRMatrix::M_FINDER_DOT => $rgb,
            ],
        ]);

        // Two-pass: resolve the matrix module count first so the scale keeps
        // the final image close to the requested pixel size.
        $probe = new QRCode($options);
        $probe->addByteSegment($data);
        $matrix = $probe->getQRMatrix();

        $options->scale = max(3, intdiv($target, max($matrix->getSize(), 1)));
        $png = (new QRCode($options))->render($data);

        $output = $this->compositeLogo($png);

        return response($output, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store');
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return [163, 28, 66];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private function compositeLogo(string $png): string
    {
        $qr = imagecreatefromstring($png);

        if ($qr === false) {
            return $png;
        }

        $logoPath = public_path('logo-small.png');
        $logo = @imagecreatefrompng($logoPath);

        if ($logo === false) {
            imagedestroy($qr);

            return $png;
        }

        $qrSize = imagesx($qr);

        imagealphablending($qr, true);
        imagesavealpha($qr, true);

        // Logo only — no box, just the brand mark centered over the modules
        $logoSize = (int) round($qrSize * 0.24);
        $drawX = (int) round(($qrSize - $logoSize) / 2);
        $drawY = $drawX;

        $srcW = imagesx($logo);
        $srcH = imagesy($logo);

        $ratio = min($logoSize / $srcW, $logoSize / $srcH);
        $drawW = (int) round($srcW * $ratio);
        $drawH = (int) round($srcH * $ratio);
        $drawX += (int) round(($logoSize - $drawW) / 2);
        $drawY += (int) round(($logoSize - $drawH) / 2);

        imagecopyresampled($qr, $logo, $drawX, $drawY, 0, 0, $drawW, $drawH, $srcW, $srcH);

        ob_start();
        imagepng($qr);
        $output = (string) ob_get_clean();

        imagedestroy($qr);
        imagedestroy($logo);

        return $output;
    }
}
