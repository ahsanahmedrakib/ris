<?php

namespace Tests\Unit;

use App\Support\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaUploadNamingTest extends TestCase
{
    #[Test]
    public function it_keeps_the_original_file_name(): void
    {
        Storage::fake('public');

        $path = Media::storeImage(UploadedFile::fake()->image('student photo.jpg'), 'admissions');

        $this->assertSame('admissions/student photo.jpg', $path);
        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function it_keeps_bangla_and_arabic_names_intact(): void
    {
        Storage::fake('public');
        $path = Media::storeImage(UploadedFile::fake()->createWithContent('আমার-ছবি.jpg', 'x'), 'admissions');

        // Bangla vowel signs are combining marks, not letters; a naive
        // alphanumeric filter silently destroys the name.
        $this->assertSame('admissions/আমার-ছবি.jpg', $path);
    }

    #[Test]
    public function a_name_is_never_allowed_to_escape_its_folder(): void
    {
        Storage::fake('public');

        foreach (['../../.htaccess', '..\\..\\evil.png', '/etc/passwd.png'] as $hostile) {
            $path = Media::storeImage(UploadedFile::fake()->createWithContent($hostile, 'x'), 'admissions');

            $this->assertStringStartsWith('admissions/', $path, "[{$hostile}] escaped the folder");
            $this->assertStringNotContainsString('..', $path, "[{$hostile}] kept a traversal sequence");
        }
    }

    #[Test]
    public function an_executable_extension_is_never_written_to_a_public_folder(): void
    {
        Storage::fake('public');

        foreach (['shell.php', 'x.phtml', 'run.PHAR', 'evil.php5'] as $hostile) {
            $path = Media::storeImage(UploadedFile::fake()->createWithContent($hostile, 'x'), 'uploads');

            $this->assertDoesNotMatchRegularExpression(
                '/\.(php\d*|phtml|phar)$/i',
                $path,
                "[{$hostile}] was stored with an executable extension",
            );
        }
    }

    #[Test]
    public function a_duplicate_name_does_not_overwrite_the_existing_upload(): void
    {
        Storage::fake('public');

        $first = Media::storeImage(UploadedFile::fake()->createWithContent('report.pdf', 'ORIGINAL'), 'files');
        $second = Media::storeImage(UploadedFile::fake()->createWithContent('report.pdf', 'REPLACEMENT'), 'files');

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($first);
        Storage::disk('public')->assertExists($second);
        $this->assertSame('ORIGINAL', Storage::disk('public')->get($first));
        $this->assertSame('REPLACEMENT', Storage::disk('public')->get($second));
    }

    #[Test]
    #[DataProvider('longNames')]
    public function a_long_name_still_fits_the_path_column(string $name): void
    {
        Storage::fake('public_files');

        $path = Media::storeFile(UploadedFile::fake()->createWithContent($name, 'x'), 'academic-calendars');

        // Path columns are varchar(255); a longer value would be truncated by
        // the database into a path that points at nothing.
        $this->assertLessThanOrEqual(255, strlen($path), "[{$name}] produced a path that does not fit the column");
        Storage::disk('public_files')->assertExists($path);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function longNames(): array
    {
        return [
            'ascii' => [str_repeat('a', 400).'.pdf'],
            'bangla multibyte' => [str_repeat('অ', 200).'.pdf'],
            'spaced' => [str_repeat('b c ', 100).'.pdf'],
        ];
    }

    #[Test]
    public function files_go_to_the_files_disk_and_images_to_the_images_disk(): void
    {
        Storage::fake('public');
        Storage::fake('public_files');

        $file = Media::storeFile(UploadedFile::fake()->createWithContent('syllabus.pdf', 'x'), 'academic-calendars');

        Storage::disk('public_files')->assertExists($file);
        Storage::disk('public')->assertMissing($file);
    }
}
