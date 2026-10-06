<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CampusEvent;
use App\Models\Notice;
use App\Models\TeacherProfile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * The XML sitemap.
 *
 * Without one, a search engine only finds the pages it happens to follow a link
 * to. The notice board and the campus events in particular are reachable only from
 * links inside the home page, and a notice published yesterday has almost no
 * chance of being crawled before somebody searches for it. Listing them here is
 * what makes them eligible at all.
 *
 * The document is generated rather than written to disk. A static file goes stale
 * the moment a notice is published, and a stale sitemap is worse than none,
 * because it advertises URLs that 404.
 */
class SitemapController extends Controller
{
    /**
     * How long a generated document is reused for. The notice board changes when
     * an administrator publishes something, which is a handful of times a term,
     * so a few minutes of caching removes almost all of the work without anyone
     * waiting to see a new notice.
     */
    private const CACHE_MINUTES = 30;

    public function index(): Response
    {
        $xml = Cache::remember(
            'seo:sitemap',
            now()->addMinutes(self::CACHE_MINUTES),
            fn (): string => $this->build(),
        );

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    protected function build(): string
    {
        $urls = array_merge(
            $this->staticUrls(),
            $this->noticeUrls(),
            $this->campusEventUrls(),
            $this->teacherUrls(),
        );

        $document = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $document .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $document .= "    <url>\n";
            $document .= '        <loc>'.e($url['loc'])."</loc>\n";

            if (! empty($url['lastmod'])) {
                $document .= '        <lastmod>'.$url['lastmod']."</lastmod>\n";
            }

            // priority and changefreq are read by nobody. Google states plainly
            // that it ignores both, and Bing ignores changefreq. Emitting them
            // would be decoration that implies a tuning nobody is doing, so only
            // lastmod is published.
            $document .= "    </url>\n";
        }

        $document .= '</urlset>'."\n";

        return $document;
    }

    /**
     * The fixed pages.
     *
     * The home page is first and carries today's date, because its own content
     * does change daily through the visitor counter and the notice teaser.
     *
     * @return list<array{loc: string, lastmod?: string}>
     */
    protected function staticUrls(): array
    {
        $pages = [
            ['route' => 'home', 'lastmod' => now()->toAtomString()],
            ['route' => 'about', 'lastmod' => now()->toAtomString()],
            ['route' => 'admission'],
            ['route' => 'scholarship'],
            ['route' => 'contact'],
            ['route' => 'notices'],
            ['route' => 'campus-events'],
            ['route' => 'teachers'],
            ['route' => 'gallery'],
            ['route' => 'testimonials'],
            ['route' => 'class-routine'],
            ['route' => 'academic.calendar'],
            ['route' => 'academic.fees'],
            ['route' => 'academic.facilities'],
        ];

        return array_map(fn (array $page): array => array_filter([
            'loc' => route($page['route']),
            'lastmod' => $page['lastmod'] ?? null,
        ]), $pages);
    }

    /**
     * Published notices.
     *
     * Only what the public listing already shows: active, and past its publish
     * date. A notice scheduled for next week is in the database and not yet on
     * the site, so listing it would give a crawler a page whose date is in the
     * future.
     *
     * @return list<array{loc: string, lastmod?: string}>
     */
    protected function noticeUrls(): array
    {
        return Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->select('slug', 'published_at', 'updated_at')
            ->get()
            ->map(fn (Notice $notice): array => array_filter([
                'loc' => route('notices.single', $notice->slug),
                'lastmod' => ($notice->published_at ?? $notice->updated_at)?->toAtomString(),
            ]))
            ->all();
    }

    /**
     * @return list<array{loc: string, lastmod?: string}>
     */
    protected function campusEventUrls(): array
    {
        return CampusEvent::where('is_active', true)
            ->orderByDesc('id')
            ->select('slug', 'date', 'updated_at')
            ->get()
            ->map(fn (CampusEvent $item): array => array_filter([
                'loc' => route('campus-events.single', $item->slug),
                'lastmod' => ($item->date ?? $item->updated_at)?->toAtomString(),
            ]))
            ->all();
    }

    /**
     * Teacher profiles. Only those the public profile route will actually serve,
     * which is the same set of conditions the controller applies.
     *
     * @return list<array{loc: string, lastmod?: string}>
     */
    protected function teacherUrls(): array
    {
        return TeacherProfile::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->select('slug', 'updated_at')
            ->get()
            ->map(fn (TeacherProfile $profile): array => array_filter([
                'loc' => route('teacher.single', $profile->slug),
                'lastmod' => $profile->updated_at?->toAtomString(),
            ]))
            ->all();
    }
}
