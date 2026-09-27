<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Notification links used to point at the JSON endpoint that feeds each
     * list view's modal, for example /admin/scholarship/69/show. Opening one
     * in the browser dumped raw JSON instead of the record, because that route
     * exists for fetch(), not for navigation.
     *
     * They now point at the listing page with ?view=<id>, which the page turns
     * into an open modal. Only the url inside the data payload changes.
     */
    public function up(): void
    {
        $this->rewrite(true);
    }

    public function down(): void
    {
        $this->rewrite(false);
    }

    private function rewrite(bool $toModal): void
    {
        DB::table('notifications')->orderBy('id')->chunkById(200, function ($notifications) use ($toModal): void {
            foreach ($notifications as $notification) {
                $data = json_decode((string) $notification->data, true);

                if (! is_array($data) || ! isset($data['url'], $data['type'])) {
                    continue;
                }

                $new = $this->convert((string) $data['type'], (string) $data['url'], $toModal);

                if ($new === null || $new === $data['url']) {
                    continue;
                }

                $data['url'] = $new;

                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
            }
        });
    }

    /**
     * Each type keeps the exact endpoint shape it had before, so down() can
     * restore it. The custom scholarship and admission routes carry an explicit
     * /show segment, while the testimonials and contact-messages resource
     * routes do not.
     *
     * @var array<string, string> listing path per notification type
     * @var array<string, string> segment the original endpoint used
     */
    private const TYPES = [
        'scholarship' => ['base' => '/admin/scholarship', 'suffix' => '/show'],
        'admission' => ['base' => '/admin/admission', 'suffix' => '/show'],
        'testimonial' => ['base' => '/admin/testimonials', 'suffix' => ''],
        'contact' => ['base' => '/admin/contact-messages', 'suffix' => ''],
    ];

    private function convert(string $type, string $url, bool $toModal): ?string
    {
        $config = self::TYPES[$type] ?? null;

        if ($config === null) {
            return null;
        }

        $base = $config['base'];
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $query = parse_url($url, PHP_URL_QUERY);

        if (! str_starts_with($path, $base)) {
            return null;
        }

        if ($toModal) {
            // /admin/scholarship/69/show or /admin/testimonials/11 -> ?view=69
            if (! preg_match('#^'.preg_quote($base, '#').'/(\d+)(/show)?$#', $path, $m)) {
                return null;
            }

            return $base.($query ? '?'.$query.'&view=' : '?view=').$m[1];
        }

        // /admin/scholarship?view=69 -> /admin/scholarship/69/show
        if ($query === null || ! preg_match('/(?:^|&)view=(\d+)(?:&|$)/', $query, $m)) {
            return null;
        }

        return $base.'/'.$m[1].$config['suffix'];
    }
};
