<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Draws the social preview image.
 *
 * A link shared on Facebook, WhatsApp or iMessage renders as a bare grey box
 * unless the page supplies an image, and a grey box gets far fewer clicks than
 * the link it is standing in for. Every share of the school's admission page
 * goes through this file.
 *
 * It is drawn here rather than hand-made in a design tool because the wording on
 * it lives in config/seo.php. A new phone number or a corrected address should
 * not need someone who opens a design file to redraw a JPEG.
 *
 * 1200x630 is the size Facebook, LinkedIn and WhatsApp all crop to. Anything
 * else gets letterboxed or cropped through the text.
 */
class GenerateOgImage extends Command
{
    protected $signature = 'seo:og-image
                            {--path= : Where to write the image; defaults to the configured public path}
                            {--force : Overwrite an existing file without asking}';

    protected $description = 'Draw the 1200x630 social preview image from config/seo.php';

    /**
     * The canvas size every major platform crops to.
     */
    private const WIDTH = 1200;

    private const HEIGHT = 630;

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The gd extension is required to draw the preview image. Enable it, or add the file by hand.');

            return self::FAILURE;
        }

        $path = (string) ($this->option('path') ?: public_path((string) config('seo.og_image')));
        $fileExists = is_file($path);

        if ($fileExists && ! $this->option('force')) {
            $this->warn("{$path} already exists. Pass --force to redraw it.");

            return self::SUCCESS;
        }

        $image = $this->draw();

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagejpeg($image, $path, 88);
        imagedestroy($image);

        $this->info("Wrote {$path} (".self::WIDTH.'x'.self::HEIGHT.', '.(int) (filesize($path) / 1024).' KB).');

        return self::SUCCESS;
    }

    protected function draw(): \GdImage
    {
        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        // A gradient rather than a flat fill, because the flat version renders
        // as a grey slab at thumbnail size in a chat list.
        for ($y = 0; $y < self::HEIGHT; $y++) {
            $shade = (int) round($y / self::HEIGHT);
            $colour = imagecolorallocate(
                $canvas,
                (int) (0x8C + (0xA3 - 0x8C) * $shade),
                (int) (0x1D + (0x1C - 0x1D) * $shade),
                (int) (0x30 + (0x42 - 0x30) * $shade),
            );
            imageline($canvas, 0, $y, self::WIDTH, $y, $colour);
        }

        $white = imagecolorallocate($canvas, 0xFF, 0xFF, 0xFF);
        $muted = imagecolorallocate($canvas, 0xE6, 0xC4, 0xCE);
        $accent = imagecolorallocate($canvas, 0xF2, 0xC1, 0x4C);

        // The Bengali name is the brand on the school's own site and on every
        // notice it prints, so it is the largest text here. Nirmala UI is the
        // Windows font with Bengali coverage; the list falls back through the
        // usual suspects and finally to the built-in bitmap font.
        $bengaliFont = $this->firstFont([
            'C:/Windows/Fonts/NirmalaB.ttf',
            '/usr/share/fonts/truetype/noto/NotoSansBengali-Bold.ttf',
            '/usr/share/fonts/truetype/lohit-beng/Bengali.ttf',
        ]);

        $latinBold = $this->firstFont([
            'C:/Windows/Fonts/arialbd.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        ]);

        $logoPath = public_path('logo-white.png');

        if (is_file($logoPath)) {
            $logo = @imagecreatefromstring((string) file_get_contents($logoPath));

            if ($logo instanceof \GdImage) {
                $this->placeLogo($canvas, $logo);
                imagedestroy($logo);
            }
        }

        if ($bengaliFont !== null) {
            imagettftext($canvas, 44, 0, 90, 400, $white, $bengaliFont, (string) config('seo.name_bn'));
        } else {
            imagestring($canvas, 5, 90, 370, (string) config('seo.name_bn'), $white);
        }

        $latin = (string) config('seo.name');

        if ($latinBold !== null) {
            imagettftext($canvas, 26, 0, 90, 452, $muted, $latinBold, $latin);
            imagestring($canvas, 4, 90, 500, 'Gopalganj 8100, Bangladesh  |  +8801619007006', $accent);
            imagestring($canvas, 4, 90, 530, 'Admission and Merit Scholarship open', $muted);
        } else {
            imagestring($canvas, 4, 90, 440, $latin, $muted);
            imagestring($canvas, 3, 90, 500, 'Gopalganj 8100, Bangladesh', $accent);
        }

        return $canvas;
    }

    /**
     * Scales the logo to sit in the top left, keeping its aspect ratio. The logo
     * in this project is a wide wordmark rather than a square mark, so it is
     * placed across the top rather than beside the text.
     */
    protected function placeLogo(\GdImage $canvas, \GdImage $logo): void
    {
        $width = imagesx($logo);
        $height = imagesy($logo);

        if ($width === 0 || $height === 0) {
            return;
        }

        $targetHeight = 150;
        $targetWidth = (int) round($targetHeight * ($width / $height));

        $scaled = imagecreatetruecolor($targetWidth, $targetHeight);

        // Kept transparent, because the logo is a PNG with an alpha channel and
        // filling the new surface opaque first would draw a box around it. The
        // destination is then the canvas, not the scaled copy, because the scaled
        // copy exists only to hold the resize and is never shown on its own.
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        imagefill($scaled, 0, 0, imagecolorallocatealpha($scaled, 0, 0, 0, 127));

        imagecopyresampled($canvas, $scaled, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($scaled);
    }

    /**
     * @param  list<string>  $candidates
     */
    protected function firstFont(array $candidates): ?string
    {
        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}
