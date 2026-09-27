<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function responses_carry_the_baseline_security_headers(): void
    {
        $response = $this->get(route('home'))->assertSuccessful();

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeaderMissing('X-Powered-By');
    }

    #[Test]
    public function responses_carry_a_content_security_policy(): void
    {
        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    }

    #[Test]
    public function the_production_policy_permits_the_eval_that_alpine_requires(): void
    {
        // Livewire ships Alpine, and Alpine compiles x-data/x-model/x-show with
        // `new Function`. Without 'unsafe-eval' on script-src, production
        // silently loses every Alpine binding on the site. Development already
        // allows it, so this has to assert the production branch specifically.
        config()->set('security.csp.development', false);

        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("script-src 'self' 'unsafe-eval'", $csp);

        // The only things permitted beyond 'self' and 'unsafe-eval' are the
        // nonce, which is how the inline Alpine factories run without handing
        // 'unsafe-inline' back to injected markup, and jsDelivr for the Quill
        // editor the admin layout loads.
        $this->assertSame(
            1,
            preg_match(
                "/^script-src 'self' 'unsafe-eval' https:\/\/cdn\.jsdelivr\.net 'nonce-[A-Za-z0-9+\\/=]+'$/",
                trim($this->scriptSource($csp)),
            ),
            'Unexpected production script-src: '.trim($this->scriptSource($csp)),
        );
    }

    #[Test]
    public function the_policy_never_allows_inline_script(): void
    {
        // 'unsafe-inline' is the directive that actually turns injected markup
        // into running script, so it must never appear outside development.
        config()->set('security.csp.development', false);

        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        $this->assertStringNotContainsString("'unsafe-inline'", $this->scriptSource($csp));
    }

    #[Test]
    public function production_does_not_upgrade_insecure_requests_locally(): void
    {
        // The Vite dev server only speaks HTTP, so upgrade-insecure-requests
        // would break hot reload on a plain HTTP request.
        config()->set('security.csp.development', true);

        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        $this->assertStringNotContainsString('upgrade-insecure-requests', $csp);
    }

    #[Test]
    public function development_permits_the_vite_dev_server_for_every_asset_type(): void
    {
        // With `npm run dev` the page is served from the app origin while CSS,
        // fonts and images all come from the Vite origin. 'self' does not cover
        // those, so a strict policy silently strips all styling from the site.
        config()->set('security.csp.development', true);

        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        foreach (['style-src', 'font-src', 'img-src', 'script-src', 'connect-src'] as $directive) {
            $this->assertStringContainsString(
                'http:',
                $this->directive($csp, $directive),
                "{$directive} blocks the Vite dev server in development",
            );
        }
    }

    #[Test]
    public function production_never_permits_plain_http(): void
    {
        config()->set('security.csp.development', false);

        $csp = $this->get(route('home'))->assertSuccessful()->headers->get('Content-Security-Policy');

        foreach (['style-src', 'font-src', 'img-src', 'script-src', 'connect-src'] as $directive) {
            $this->assertStringNotContainsString('http:', $this->directive($csp, $directive));
        }
    }

    /**
     * Isolates one directive from the policy.
     */
    private function directive(?string $csp, string $name): string
    {
        foreach (explode(';', (string) $csp) as $directive) {
            if (str_starts_with(trim($directive), $name)) {
                return $directive;
            }
        }

        return '';
    }

    /**
     * Isolates the script-src directive so style-src's inline allowance, which
     * is deliberate, cannot mask a regression here.
     */
    private function scriptSource(?string $csp): string
    {
        return $this->directive($csp, 'script-src');
    }

    #[Test]
    public function the_deployed_php_runtime_stops_advertising_its_version(): void
    {
        // The middleware cannot do this on its own: PHP re-adds X-Powered-By
        // after the response is built, so asserting on the Response object
        // passes while the header is still on the wire. The suppression has to
        // come from the SAPI, which is what public/.user.ini configures.
        $ini = public_path('.user.ini');

        $this->assertFileExists($ini, 'public/.user.ini is missing, so X-Powered-By leaks on deploy.');
        $this->assertMatchesRegularExpression(
            '/^\s*expose_php\s*=\s*Off\s*$/mi',
            (string) file_get_contents($ini),
        );
    }

    #[Test]
    public function upload_directories_never_execute_scripts(): void
    {
        // Uploads are served straight from the web root, so the folders holding
        // them must refuse to run anything that lands there.
        foreach (['images', 'files'] as $folder) {
            $rules = (string) file_get_contents(public_path($folder.'/.htaccess'));

            $this->assertStringContainsString('php_flag engine off', $rules, "{$folder} does not disable the PHP engine");
            $this->assertStringContainsString('Require all denied', $rules, "{$folder} does not deny access");
            $this->assertMatchesRegularExpression(
                '/FilesMatch.*php.*\.htaccess/is',
                $rules,
                "{$folder} does not block script and override extensions",
            );
        }
    }

    #[Test]
    public function the_upload_folder_protections_are_actually_committed(): void
    {
        // The upload folders are gitignored because they hold user uploads, but
        // that same ignore used to swallow their .htaccess. Git cannot re-include
        // a file whose parent directory is excluded, so the protections existed
        // locally and silently never deployed. Guard the ignore shape instead.
        $ignore = (string) file_get_contents(base_path('.gitignore'));
        $patterns = array_map(
            static fn (string $line): string => trim($line),
            preg_split('/\R/', $ignore) ?: [],
        );

        foreach (['images', 'files'] as $folder) {
            $this->assertNotContains(
                "/public/{$folder}",
                $patterns,
                "Excluding /public/{$folder} as a directory also excludes its .htaccess, so the upload protections would never deploy.",
            );

            $this->assertContains(
                "!/public/{$folder}/.htaccess",
                $patterns,
                "public/{$folder}/.htaccess must be re-included or it is left untracked.",
            );
        }
    }

    #[Test]
    public function the_deploy_script_never_destroys_the_database(): void
    {
        $script = (string) file_get_contents(base_path('clevercloud/post_build.sh'));

        // These drop every table. Adopting one silently would wipe production.
        foreach (['migrate:fresh', 'migrate:reset', 'db:wipe', 'migrate:rollback'] as $destructive) {
            $this->assertStringNotContainsString(
                $destructive,
                $script,
                "clevercloud/post_build.sh runs {$destructive}, which deletes the production data.",
            );
        }
    }

    #[Test]
    public function the_deploy_script_persists_uploads_to_the_volume(): void
    {
        // Clever Cloud's filesystem is ephemeral outside addons, so uploads kept
        // in the container are lost on the next deploy. They have to be linked
        // onto the attached volume or every photo and PDF disappears.
        $script = (string) file_get_contents(base_path('clevercloud/post_build.sh'));

        $this->assertStringContainsString('UPLOAD_VOLUME', $script);
        $this->assertStringContainsString('ln -sfn', $script, 'Upload folders are not symlinked onto the volume.');

        foreach (['images', 'files'] as $folder) {
            $this->assertMatchesRegularExpression(
                '/link_uploads\s+'.preg_quote($folder, '/').'\b/',
                $script,
                "public/{$folder} is never linked onto the volume, so its uploads will not survive a deploy.",
            );
        }
    }

    #[Test]
    public function the_web_root_htaccess_still_allows_the_front_controller(): void
    {
        // A FilesMatch on .php would also match index.php and 403 the whole
        // site via DirectoryIndex, so the exception has to be a RewriteRule.
        $rules = (string) file_get_contents(public_path('.htaccess'));

        $this->assertMatchesRegularExpression('/RewriteRule\s+\^index\\\.php\$\s+-\s+\[L\]/', $rules);

        // No FilesMatch may match index.php, because that 403s the whole site
        // through DirectoryIndex. Asserting the behaviour is more reliable than
        // pattern matching: rules like server\.php are correctly narrow and
        // must not be confused with a blanket \.php block.
        preg_match_all('/<FilesMatch\s+"([^"]+)"/i', $rules, $matches);

        $this->assertNotEmpty($matches[1], 'Expected the web root to keep its FilesMatch guards.');

        foreach ($matches[1] as $pattern) {
            $this->assertSame(
                0,
                preg_match('#'.$pattern.'#', 'index.php'),
                "A FilesMatch in public/.htaccess would block the front controller: {$pattern}",
            );
        }
    }

    #[Test]
    public function the_jwt_cookie_is_http_only_and_never_plaintext_legible(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'Str0ng-Passw0rd!2026',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $admin->email,
            'password' => 'Str0ng-Passw0rd!2026',
        ]);

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === 'jwt_token');

        $this->assertNotNull($cookie, 'No jwt_token cookie was issued on login.');
        $this->assertTrue($cookie->isHttpOnly(), 'The JWT cookie must be HttpOnly.');
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertGreaterThan(0, $cookie->getExpiresTime() - time());
    }

    #[Test]
    public function logging_out_expires_the_jwt_cookie(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('logout'));

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === 'jwt_token');

        $this->assertNotNull($cookie, 'The jwt_token cookie was not cleared on logout.');
        $this->assertLessThan(time(), $cookie->getExpiresTime());
    }

    #[Test]
    public function the_activity_log_never_stores_a_password_hash(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $teacher = User::factory()->create(['role' => 'teacher', 'password' => 'Str0ng-Passw0rd!2026']);
        $teacher->update(['password' => 'An0ther-Str0ng-Pass!2026']);

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $teacher->id)
            ->where('action', 'update')
            ->latest()
            ->first();

        $this->assertNotNull($log, 'No update activity was recorded for the user.');
        $this->assertSame('[redacted]', $log->properties['password'] ?? null);
        $this->assertStringNotContainsString('$2y$', json_encode($log->properties));
    }

    #[Test]
    public function the_api_never_returns_a_password_hash(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'api')
            ->getJson(route('api.auth.me'))
            ->assertSuccessful()
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token');
    }

    #[Test]
    public function session_encryption_is_on_by_default(): void
    {
        // A local .env may still pin SESSION_ENCRYPT=false, so assert on the
        // shipped default rather than the resolved value.
        $config = (string) file_get_contents(config_path('session.php'));

        $this->assertStringContainsString(
            "'encrypt' => env('SESSION_ENCRYPT', true)",
            $config,
            'config/session.php must default SESSION_ENCRYPT to true.'
        );
    }
}
