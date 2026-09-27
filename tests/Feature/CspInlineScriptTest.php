<?php

namespace Tests\Feature;

use App\Core\Http\Middleware\SecurityHeaders;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Guards the failure that took the whole admin panel offline: a production
 * Content-Security-Policy without 'unsafe-inline' for scripts, combined with
 * inline <script> blocks that had no nonce. The browser blocked every one of
 * them, so Alpine lost createForm, risToasts, showViewModal and friends and
 * every binding on every page died with "x is not defined".
 */
class CspInlineScriptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The policy is only strict outside local, which is exactly the branch
        // these tests exist to protect.
        config(['security.csp.development' => false]);
    }

    #[Test]
    public function the_production_policy_omits_unsafe_inline_for_scripts(): void
    {
        $csp = $this->policy();

        preg_match('/script-src ([^;]+)/', $csp, $match);

        $this->assertNotEmpty($match, 'script-src is missing from the policy.');

        $this->assertStringNotContainsString(
            "'unsafe-inline'",
            $match[1],
            "script-src allows 'unsafe-inline', which re-enables script execution from injected markup: {$match[1]}",
        );

        // Alpine compiles its expressions with `new Function`.
        $this->assertStringContainsString("'unsafe-eval'", $match[1]);
    }

    #[Test]
    public function the_production_policy_carries_a_nonce(): void
    {
        $this->assertMatchesRegularExpression(
            '/script-src [^;]*\x27nonce-[A-Za-z0-9+\/=]+\x27/',
            $this->policy(),
            'script-src has no nonce, so no inline script can ever be allowed to run.',
        );
    }

    #[Test]
    public function the_policy_allows_the_third_parties_the_layouts_actually_load(): void
    {
        $csp = $this->policy();

        // Google Fonts stylesheet and font files, jsDelivr for the Quill editor
        // (both its stylesheet and its script), and the QR service on teacher
        // profile pages.
        foreach ([
            'script-src' => ['https://cdn.jsdelivr.net'],
            'style-src' => ['https://fonts.googleapis.com', 'https://cdn.jsdelivr.net'],
            'font-src' => ['https://fonts.gstatic.com'],
            'img-src' => ['https://api.qrserver.com'],
        ] as $directive => $origins) {
            $this->assertSame(
                1,
                preg_match('/'.preg_quote($directive, '/').' ([^;]+)/', $csp, $m),
                "{$directive} is missing from the policy: {$csp}",
            );

            foreach ($origins as $origin) {
                $this->assertStringContainsString(
                    $origin,
                    $m[1],
                    "{$directive} does not allow {$origin}, so the browser blocks it.",
                );
            }
        }
    }

    #[Test]
    #[DataProvider('adminPages')]
    public function no_rendered_page_contains_an_inline_script_without_the_nonce(string $route): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get($route)->assertSuccessful();

        $response = $this->actingAs($user)->get($route);
        $html = $response->getContent();
        $csp = (string) $response->headers->get('Content-Security-Policy');

        preg_match('/\x27nonce-([^\x27]+)\x27/', $csp, $nonce);

        $this->assertArrayHasKey(
            1,
            $nonce,
            "{$route} produced a policy with no nonce, so its inline scripts cannot run.",
        );

        // A bare <script> has neither src nor nonce, so the browser drops it.
        $this->assertDoesNotMatchRegularExpression(
            '/<script(?![^>]*\bsrc=)(?![^>]*\bnonce=)[^>]*>/i',
            $html,
            "{$route} renders an inline script with no nonce, which production CSP blocks.",
        );

        // Every inline script must echo the exact nonce the header advertises.
        preg_match_all('/<script(?![^>]*\bsrc=)[^>]*\bnonce="([^"]+)"/i', $html, $tags);

        foreach ($tags[1] as $found) {
            $this->assertSame(
                $nonce[1],
                $found,
                "{$route} renders an inline script whose nonce does not match the CSP header.",
            );
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function adminPages(): array
    {
        return [
            'students' => ['/admin/students'],
            'admission' => ['/admin/admission'],
            'teachers' => ['/admin/teachers'],
            'messages' => ['/admin/messages'],
            'gallery' => ['/admin/gallery'],
        ];
    }

    #[Test]
    public function no_view_file_contains_a_bare_script_tag(): void
    {
        // A new page with a plain <script> would ship broken the same way, so
        // the mistake is caught in a template rather than in a browser console.
        $offenders = [];

        foreach ($this->bladeFiles() as $file) {
            if (str_contains((string) file_get_contents($file), '<script>')) {
                $offenders[] = $file;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            'Add nonce="'.SecurityHeaders::NONCE_VARIABLE.'". '.implode(', ', $offenders),
        );
    }

    #[Test]
    public function every_external_script_the_views_load_is_allowed_by_the_policy(): void
    {
        $csp = $this->policy();

        preg_match('/script-src ([^;]+)/', $csp, $match);
        $this->assertNotEmpty($match);

        preg_match_all('/<script[^>]*\bsrc="(https?:\/\/[^"\/]+)/i', $this->allViewMarkup(), $scripts);

        $this->assertNotEmpty($scripts[1], 'No external scripts found; has the layout changed?');

        foreach (array_unique($scripts[1]) as $host) {
            $this->assertStringContainsString(
                $host,
                $match[1],
                "A view loads a script from {$host}, but script-src does not allow it, so production blocks it.",
            );
        }
    }

    /**
     * @return list<string>
     */
    private function bladeFiles(): array
    {
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'))
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function policy(): string
    {
        $response = $this->get('/');

        return (string) $response->headers->get('Content-Security-Policy');
    }

    /**
     * Concatenated raw markup of every view, so script sources are checked
     * statically instead of depending on which routes a test happens to render.
     */
    private function allViewMarkup(): string
    {
        $markup = '';

        foreach ($this->bladeFiles() as $file) {
            $markup .= file_get_contents($file);
        }

        return $markup;
    }
}
