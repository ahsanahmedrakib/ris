<?php

namespace App\Support;

use App\Models\SchoolStatistic;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Turns what a page knows about itself into the handful of tags a search engine,
 * a link preview and a knowledge panel actually read.
 *
 * The site is written in Bengali and searched for in English, so the metadata
 * here is English even where the page is not. A Bengali page that also carries
 * an English description is matchable by both, and the description is what Google
 * shows under the link, so it has to be written for a human rather than stuffed
 * with the keywords somebody searched for.
 *
 * Every page gets a canonical URL, because without one the same notice is
 * reachable at /notices, /notices?page=2 and /notices?utm_source=facebook and
 * Google splits its ranking across three copies of one page.
 */
class Seo implements Htmlable
{
    /**
     * Roughly the length that fills a result without being truncated. Google
     * rewrites anything past roughly 160 characters on a mobile SERP, so a longer
     * description is a description nobody reads.
     */
    protected const DESCRIPTION_LIMIT = 160;

    /**
     * Google measures titles in pixels rather than characters, and roughly 580
     * pixels is where a desktop result starts truncating. 70 characters is the
     * character count that fits in that space for a title in this weight, which
     * is enough room for a bilingual "বাংলা | English" title plus the school name.
     */
    protected const TITLE_LIMIT = 70;

    public function __construct(
        protected string $title,
        protected string $description,
        protected ?string $canonical = null,
        protected ?string $image = null,
        protected ?string $imageAlt = null,
        protected string $type = 'website',
        protected bool $noindex = false,
        protected array $schema = [],
        protected array $breadcrumbs = [],
    ) {}

    /**
     * A page that has said nothing about itself. Used as the floor by the view
     * composer so a page added later is still indexable, just not optimised.
     */
    public static function defaults(string $title = '', string $description = ''): self
    {
        return new self(
            title: $title !== '' ? $title : self::siteName(),
            description: $description,
        );
    }

    public static function siteName(): string
    {
        return (string) config('seo.name');
    }

    /**
     * Builds the metadata for whichever page is being rendered, from the entry
     * in config/seo.php that matches the current route.
     *
     * Centralising it means a page cannot ship with a missing description by
     * accident, and a fix to one page's wording is a config change rather than an
     * edit inside a blade template that also holds the markup.
     *
     * @param  array<string, mixed>  $overrides  title, description, image,
     *                                           image_alt, type, noindex and
     *                                           extra schema, for the pages
     *                                           whose content is in the database
     */
    public static function forCurrentPage(array $overrides = []): self
    {
        $routeName = request()->route()?->getName() ?? '';
        $pages = (array) config('seo.pages', []);
        $page = (array) ($pages[$routeName] ?? []);

        // Not every public route is worth indexing. Anything the robots.txt file
        // blocks should also say noindex in the response, because a crawler that
        // reaches a blocked URL anyway has to be told by the page itself.
        $noindex = (bool) ($overrides['noindex'] ?? $page['noindex'] ?? false)
            || static::isUnindexableRoute($routeName);

        $breadcrumbs = self::buildBreadcrumbs($routeName, $page);

        $seo = new self(
            title: (string) ($overrides['title'] ?? $page['title'] ?? ''),
            description: (string) ($overrides['description'] ?? $page['description'] ?? ''),
            image: isset($overrides['image']) ? (string) $overrides['image'] : null,
            imageAlt: isset($overrides['image_alt']) ? (string) $overrides['image_alt'] : null,
            type: (string) ($overrides['type'] ?? 'website'),
            noindex: $noindex,
            breadcrumbs: $breadcrumbs,
        );

        // A null entry is a node that decided it had nothing true to say, which is how
        // the FAQ builder declines when the school has no questions published.
        $extra = array_values(array_filter((array) ($overrides['schema'] ?? [])));

        $seo->schema = array_merge([static::schoolNode(), static::websiteNode()], $extra);

        return $seo;
    }

    /**
     * Routes that exist for a logged in user and must never enter an index.
     *
     * Mirrors the Disallow list in public/robots.txt. The two are kept in step
     * on purpose: robots.txt stops the crawl, and this stops the page being
     * indexed if a link to it escapes somewhere.
     *
     * @var list<string>
     */
    protected const UNINDEXABLE_PREFIXES = [
        'admin',
        'parent',
        'login',
        'logout',
        'password.',
        'student.',
    ];

    protected static function isUnindexableRoute(string $routeName): bool
    {
        foreach (self::UNINDEXABLE_PREFIXES as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        return in_array($routeName, ['academic.results'], true);
    }

    /**
     * The trail from the home page down to the current one. The last crumb is
     * the page itself and has no URL, because a breadcrumb pointing at the page
     * it is already on is a link to itself.
     *
     * @param  array<string, mixed>  $page
     * @return list<array{name: string, url?: string}>
     */
    protected static function buildBreadcrumbs(string $routeName, array $page): array
    {
        $crumbs = [];

        if ($routeName !== 'home') {
            $crumbs[] = ['name' => 'Home', 'url' => route('home')];
        }

        foreach ((array) ($page['breadcrumbs'] ?? []) as $label) {
            $crumbs[] = ['name' => $label];
        }

        return $crumbs;
    }

    /**
     * The "Site — Page" title format, with the site name dropped when the page
     * heading already is the site name, which happens on the home page.
     */
    public static function composeTitle(string $page): string
    {
        $page = trim($page);

        if ($page === '' || $page === config('seo.name') || $page === config('seo.name_bn')) {
            return self::homeTitle();
        }

        return self::limit($page.' | '.config('seo.name'), self::TITLE_LIMIT);
    }

    public static function homeTitle(): string
    {
        $pages = (array) config('seo.pages', []);

        return (string) ($pages['home']['title'] ?? config('seo.name'));
    }

    public function withCanonical(?string $url): self
    {
        $clone = clone $this;
        $clone->canonical = $url;

        return $clone;
    }

    public function withImage(?string $url, ?string $alt = null): self
    {
        $clone = clone $this;
        $clone->image = $url;
        $clone->imageAlt = $alt ?? $clone->imageAlt;

        return $clone;
    }

    /**
     * "article" on a notice or campus news page, "website" everywhere else. It is
     * a hint about what the content is, and it changes how the preview renders.
     */
    public function withType(string $type): self
    {
        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    public function withNoIndex(bool $noindex = true): self
    {
        $clone = clone $this;
        $clone->noindex = $noindex;

        return $clone;
    }

    public function withSchema(array $schema): self
    {
        $clone = clone $this;
        $clone->schema = $schema;

        return $clone;
    }

    /**
     * @param  list<array{name: string, url?: string}>  $breadcrumbs
     */
    public function withBreadcrumbs(array $breadcrumbs): self
    {
        $clone = clone $this;
        $clone->breadcrumbs = $breadcrumbs;

        return $clone;
    }

    /**
     * @param  list<array{name: string, url?: string}>  $breadcrumbs
     * @return list<array{name: string, url?: string}>
     */
    public function breadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Appends a crumb, so a single notice reads Home > Notices > <title> rather
     * than stopping one level short of itself.
     *
     * @param  array{name: string, url?: string}  $crumb
     */
    public function andCrumb(array $crumb): self
    {
        $clone = clone $this;

        // A crumb is replaced rather than appended when it is already the tail,
        // because the section label and the item title are often the same string
        // and "Notices > Notices" reads as a bug.
        $tail = $clone->breadcrumbs[array_key_last($clone->breadcrumbs)] ?? null;

        $clone->breadcrumbs = ($tail !== null && $tail['name'] === $crumb['name'])
            ? array_merge(array_slice($clone->breadcrumbs, 0, -1), [$crumb])
            : array_merge($clone->breadcrumbs, [$crumb]);

        return $clone;
    }

    public function title(): string
    {
        return self::limit($this->title, self::TITLE_LIMIT);
    }

    public function description(): string
    {
        return self::limit($this->description, self::DESCRIPTION_LIMIT);
    }

    public function canonicalUrl(): string
    {
        // The absolute form matters: a relative canonical is resolved against
        // whatever the current host happens to be, which includes preview
        // deployments and http origins.
        return $this->canonical ?: url()->current();
    }

    public function imageUrl(): string
    {
        $image = $this->image ?: config('seo.og_image');

        if (! $image) {
            return '';
        }

        return Str::startsWith($image, ['http://', 'https://', '//'])
            ? $image
            : asset($image);
    }

    public function imageAlt(): string
    {
        return $this->imageAlt ?: config('seo.og_image_alt');
    }

    public function noIndex(): bool
    {
        return $this->noindex;
    }

    /**
     * The rendered <head> fragment.
     */
    public function toHtml(): string
    {
        $canonical = $this->canonicalUrl();
        $image = $this->imageUrl();
        $description = $this->description();

        $tags = [];

        // The title is rendered by the layout, which also honours a per-view
        // @section('title'). Emitting it here as well would give the document
        // two <title> elements, and a search engine picks one of them at random.
        $tags[] = '<meta name="description" content="'.e($description).'">';

        $tags[] = '<link rel="canonical" href="'.e($canonical).'">';

        $robots = $this->noIndex() ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        $tags[] = '<meta name="robots" content="'.$robots.'">';

        // Open Graph is what Facebook, WhatsApp and LinkedIn read.
        $tags[] = '<meta property="og:type" content="'.e($this->type).'">';
        $tags[] = '<meta property="og:site_name" content="'.e(self::siteName()).'">';
        $tags[] = '<meta property="og:title" content="'.e($this->title()).'">';
        $tags[] = '<meta property="og:description" content="'.e($description).'">';
        $tags[] = '<meta property="og:url" content="'.e($canonical).'">';
        $tags[] = '<meta property="og:locale" content="'.e(str_replace('_', '-', (string) config('seo.locale'))).'">';

        $alternateLocale = config('seo.locale_alternate');
        if ($alternateLocale) {
            $tags[] = '<meta property="og:locale:alternate" content="'.e(str_replace('_', '-', (string) $alternateLocale)).'">';
        }

        if ($image !== '') {
            $tags[] = '<meta property="og:image" content="'.e($image).'">';
            $tags[] = '<meta property="og:image:alt" content="'.e($this->imageAlt()).'">';
            $tags[] = '<meta property="og:image:width" content="1200">';
            $tags[] = '<meta property="og:image:height" content="630">';
        }

        $tags[] = '<meta name="twitter:card" content="'.($image !== '' ? 'summary_large_image' : 'summary').'">';
        $tags[] = '<meta name="twitter:title" content="'.e($this->title()).'">';
        $tags[] = '<meta name="twitter:description" content="'.e($description).'">';

        if ($image !== '') {
            $tags[] = '<meta name="twitter:image" content="'.e($image).'">';
        }

        if ($twitterSite = config('seo.twitter_site')) {
            $tags[] = '<meta name="twitter:site" content="'.e('@'.ltrim((string) $twitterSite, '@')).'">';
        }

        foreach ($this->schemaGraphs() as $graph) {
            $script = JsonLd::script($graph);

            if ($script !== '') {
                $tags[] = $script;
            }
        }

        return implode("\n", array_filter($tags, fn (string $tag): bool => $tag !== ''));
    }

    public function toHtmlString(): HtmlString
    {
        return new HtmlString($this->toHtml());
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * Structured data, one @graph per page.
     *
     * Bundling the nodes into a single graph lets them reference each other by
     *
     * @id, so the School node is stated once and the breadcrumb points at it
     * rather than repeating it. Google reads a standalone script tag just as
     * happily, so this is tidiness rather than a requirement.
     *
     * @return list<array<string, mixed>>
     */
    protected function schemaGraphs(): array
    {
        // Nodes from this object carry no @context of their own because they are
        // merged into one graph. Nodes handed in by a controller were built as
        // complete documents, each with its own @context, and they cannot go
        // inside this graph. Those are emitted as separate script tags.
        $nested = [];
        $standalone = [];

        foreach (array_filter($this->schema) as $node) {
            if (array_key_exists('@context', $node)) {
                $standalone[] = $node;
            } else {
                $nested[] = $node;
            }
        }

        $graphs = [];

        if ($nested !== [] || $this->breadcrumbs !== []) {
            $graphs[] = [
                '@context' => 'https://schema.org',
                '@graph' => array_values(array_merge($nested, array_filter([$this->breadcrumbNode()]))),
            ];
        }

        return array_merge($graphs, $standalone);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function breadcrumbNode(): ?array
    {
        if ($this->breadcrumbs === []) {
            return null;
        }

        return JsonLd::breadcrumbs($this->breadcrumbs, $this->canonicalUrl());
    }

    /**
     * Cuts at a word boundary so a truncated description does not end mid-word,
     * and drops the trailing punctuation a cut leaves behind.
     */
    protected static function limit(string $value, int $limit): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        $clipped = mb_substr($value, 0, $limit);

        if (preg_match('/^(.*)\s+\S+$/u', $clipped, $matches) === 1) {
            $clipped = $matches[1];
        }

        return rtrim($clipped, " \t\n\r\0\x0B,;:-–—।");
    }

    /**
     * Strips markup and collapses whitespace before a value is used as a
     * description. Notice and campus news bodies are HTML, and a description
     * containing a <div> is a description Google cannot display.
     */
    public static function excerpt(?string $html, int $limit = self::DESCRIPTION_LIMIT): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $text = strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], ' ', $html));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return self::limit($text, $limit);
    }

    /**
     * The School node, published on the home page and referenced by id elsewhere.
     *
     * This is the node that can earn a knowledge panel, so it carries the things
     * a panel shows: name, address, phone, url, and the school type. Everything
     * here must agree with config/seo.php, because a NAP disagreement between
     * here and a Google Business Profile is the usual reason a school does not
     * rank for its own name.
     *
     * @return array<string, mixed>
     */
    public static function schoolNode(): array
    {
        $address = (array) config('seo.address');
        $geo = (array) config('seo.geo');
        $statistic = SchoolStatistic::first();

        $node = [
            '@type' => config('seo.type', 'School'),
            '@id' => url('/').'#school',
            'name' => config('seo.name'),
            'alternateName' => config('seo.name_bn'),
            'url' => url('/'),
            'description' => 'Resma International School is a school in Gopalganj, Bangladesh, offering admission, merit scholarships, a digital classroom and a science laboratory for students from nursery to secondary level.',
            'telephone' => config('seo.phone'),
            'email' => config('seo.email'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $address['street'] ?? null,
                'addressLocality' => $address['address_locality'] ?? null,
                'addressRegion' => $address['address_region'] ?? null,
                'postalCode' => $address['postal_code'] ?? null,
                'addressCountry' => $address['address_country'] ?? null,
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $geo['latitude'] ?? null,
                'longitude' => $geo['longitude'] ?? null,
            ],
            'priceRange' => config('seo.price_range'),
            'areaServed' => [
                '@type' => 'City',
                'name' => 'Gopalganj',
            ],
        ];

        if ($statistic) {
            // Not a documented schema.org property. It costs nothing to omit and
            // nothing to include, but it tells a reader how big the school is
            // without a second sentence of prose, so it stays.
            $node['numberOfStudents'] = [
                '@type' => 'QuantitativeValue',
                'value' => $statistic->total_students,
            ];
        }

        $socials = array_filter((array) config('seo.social'));
        if ($socials !== []) {
            $node['sameAs'] = array_values($socials);
        }

        return array_filter($node, fn ($value): bool => $value !== null && $value !== '' && $value !== []);
    }

    /**
     * The WebSite node, which is what makes Google's sitelinks search box appear
     * under the result for the school's name.
     *
     * @return array<string, mixed>
     */
    public static function websiteNode(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => config('seo.name'),
            'alternateName' => config('seo.name_bn'),
            'inLanguage' => 'bn-BD',
            'publisher' => ['@id' => url('/').'#school'],
        ];
    }
}
