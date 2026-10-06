<?php

namespace App\Support;

/**
 * Renders a schema.org node as a JSON-LD script element.
 *
 * JSON-LD lives inside a <script> element, which means any string in the payload
 * containing "</script>" would close the element early and hand the rest of the
 * document to the browser as script source. Everything is escaped so that the
 * only "<" that can appear in the output is the one opening the tag itself.
 */
class JsonLd
{
    /**
     * @param  array<string, mixed>  $node
     */
    public static function script(array $node, string $indent = '    '): string
    {
        $json = json_encode(
            $node,
            JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_HEX_TAG
                | JSON_HEX_AMP
                | JSON_HEX_APOS
                | JSON_HEX_QUOT,
        );

        if ($json === false) {
            return '';
        }

        // json_encode already escapes "<" and ">", so "</" cannot be produced from
        // the data. This is the second of the two defences, kept because it costs
        // nothing and the failure it prevents is an XSS.
        $json = str_replace('</', '<\/', $json);

        return $indent.'<script type="application/ld+json">'.$json.'</script>';
    }

    /**
     * The FAQPage node.
     *
     * A page of questions and answers can appear in the results as expandable
     * rows under the link, which is more space than a normal result. Google only
     * shows it when the same questions are visible on the page, which they are:
     * this is called from the section that renders them.
     *
     * @param  iterable<int, object{question: string, answer: string}>  $faqs
     * @return array<string, mixed>|null null when there are no complete
     *                                   question and answer pairs to publish
     */
    public static function faqPage(iterable $faqs): ?array
    {
        $entities = [];

        foreach ($faqs as $faq) {
            $question = trim((string) $faq->question);
            $answer = trim((string) $faq->answer);

            if ($question === '' || $answer === '') {
                continue;
            }

            $entities[] = [
                '@type' => 'Question',
                'name' => $question,
                // Stripped of markup, because a schema answer is read as plain
                // text and an answer containing a <p> renders the tag literally
                // in the search result.
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => Seo::excerpt($answer, 600),
                ],
            ];
        }

        // An empty mainEntity is worse than no node: Google reads it as a page
        // that promises questions and shows none, which costs trust in the whole
        // site's structured data.
        if ($entities === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    /**
     * The BreadcrumbList node, published by the Seo object rather than here so
     * that the visible trail and the structured one cannot drift apart.
     *
     * @param  list<array{name: string, url?: string}>  $breadcrumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $breadcrumbs, string $pageUrl): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => $pageUrl.'#breadcrumb',
            'itemListElement' => array_map(
                static fn (array $crumb, int $index): array => array_filter([
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'] ?? null,
                ]),
                array_values($breadcrumbs),
                array_keys($breadcrumbs),
            ),
        ];
    }

    /**
     * The Article node used by notices and campus news.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function article(string $headline, ?string $body, ?string $image, ?string $publishedAt, array $overrides = []): array
    {
        $node = [
            '@type' => 'Article',
            'headline' => $headline,
            'description' => Seo::excerpt($body),
            'inLanguage' => 'bn-BD',
            'isPartOf' => ['@id' => url('/').'#website'],
            'publisher' => ['@id' => url('/').'#school'],
            'datePublished' => $publishedAt,
            'image' => $image,
        ];

        return array_filter(array_merge($node, $overrides), fn ($value): bool => $value !== null && $value !== '');
    }

    /**
     * The Course node used on the admission page.
     *
     * @return array<string, mixed>
     */
    public static function educationalProgram(): array
    {
        return [
            '@type' => 'Course',
            'name' => 'Secondary School Education',
            'description' => 'Nursery to secondary education at Resma International School, Gopalganj.',
            'inLanguage' => 'bn-BD',
            'provider' => ['@id' => url('/').'#school'],
            'hasCourseInstance' => [
                [
                    '@type' => 'CourseInstance',
                    'courseMode' => 'Onsite',
                    'courseWorkload' => 'Full-time',
                    'name' => 'Admission 2026',
                ],
            ],
        ];
    }

    /**
     * The ContactPage node for /contact.
     *
     * @return array<string, mixed>
     */
    public static function contactPage(): array
    {
        return [
            '@type' => 'ContactPage',
            'url' => route('contact'),
            'name' => 'Contact Resma International School, Gopalganj',
            'inLanguage' => 'bn-BD',
            'about' => ['@id' => url('/').'#school'],
        ];
    }

    /**
     * The CollectionPage node for the index pages: the notice board, the gallery,
     * the teacher list. It tells Google the page is a list of things and the
     * links in it are entries rather than incidental navigation.
     *
     * @param  list<string>  $itemUrls
     * @return array<string, mixed>
     */
    public static function collectionPage(string $url, string $name, string $description, array $itemUrls = []): array
    {
        $node = [
            '@type' => 'CollectionPage',
            '@id' => $url.'#collection',
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'inLanguage' => 'bn-BD',
            'isPartOf' => ['@id' => url('/').'#website'],
        ];

        if ($itemUrls !== []) {
            $node['mainEntity'] = [
                '@type' => 'ItemList',
                'itemListElement' => array_map(
                    static fn (string $itemUrl, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => $itemUrl,
                    ],
                    array_values($itemUrls),
                    array_keys($itemUrls),
                ),
            ];
        }

        return $node;
    }
}
