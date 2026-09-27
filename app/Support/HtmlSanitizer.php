<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allowlist HTML sanitiser for rich text authored in the browser.
 *
 * Notices are composed with a WYSIWYG editor, so their content is real HTML and
 * is rendered unescaped on the public website. Without sanitising it, any staff
 * account can store script that runs in every visitor's browser, so content is
 * cleaned on write and again on read.
 */
class HtmlSanitizer
{
    /**
     * Tags that survive sanitisation. Everything else is dropped, though the
     * text inside a dropped tag is kept so no content silently disappears.
     */
    private const ALLOWED_TAGS = [
        'a', 'abbr', 'b', 'blockquote', 'br', 'code', 'dd', 'div', 'dl', 'dt',
        'em', 'figcaption', 'figure', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i', 'li', 'ol', 'p', 'pre', 's', 'small', 'span', 'strong',
        'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr',
        'u', 'ul',
    ];

    /**
     * @var array<string, list<string>> Tag => additional permitted attributes.
     */
    private const ALLOWED_ATTRIBUTES = [
        '*' => ['class', 'dir', 'lang', 'style', 'title'],
        'a' => ['href', 'target', 'rel'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
    ];

    /**
     * Inline styles permitted from the editor. Kept narrow so a style attribute
     * cannot be used to exfiltrate data or load remote content.
     */
    private const ALLOWED_STYLE_PROPERTIES = [
        'background-color', 'color', 'font-size', 'font-style',
        'font-weight', 'line-height', 'text-align', 'text-decoration',
    ];

    /**
     * URL schemes permitted in href attributes.
     */
    private const ALLOWED_URL_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    private const IMG_TAG = 'img';

    /**
     * Tags removed together with their contents, because their contents are code
     * or markup rather than prose and must never be rendered as text.
     */
    private const DISCARDED_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'applet',
        'form', 'input', 'button', 'select', 'textarea', 'link', 'meta',
        'base', 'svg', 'math', 'template', 'noscript', 'frame', 'frameset',
    ];

    /**
     * Sanitise a fragment of HTML, returning only allowlisted markup.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return (string) $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><body>'.$html.'</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMNode) {
            return e(strip_tags($html));
        }

        self::cleanChildren($body);

        $output = '';

        foreach (iterator_to_array($body->childNodes) as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Recursively sanitise a node and its children.
     */
    private static function cleanNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            self::cleanNode($child);
        }

        if ($node instanceof DOMElement) {
            self::cleanElement($node);
        }
    }

    private static function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            self::cleanNode($child);
        }
    }

    private static function cleanElement(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);

        if (in_array($tag, self::DISCARDED_TAGS, true)) {
            $element->parentNode?->removeChild($element);

            return;
        }

        // Images are the one tag allowed to load remote content, and only from
        // http(s) so it cannot become a data: or javascript: vector.
        $isImage = $tag === self::IMG_TAG;

        if (! $isImage && ! in_array($tag, self::ALLOWED_TAGS, true)) {
            // Unwrap: the tag disappears but its text content survives.
            self::unwrap($element);

            return;
        }

        $permitted = array_merge(
            self::ALLOWED_ATTRIBUTES['*'],
            self::ALLOWED_ATTRIBUTES[$tag] ?? [],
            $isImage ? ['src', 'alt', 'width', 'height'] : [],
        );

        foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = trim($attribute->nodeValue ?? '');

            $keep = in_array($name, $permitted, true);

            if ($keep && $name === 'href') {
                $keep = self::isSafeUrl($value);
            }

            if ($keep && $name === 'src') {
                $keep = self::isSafeUrl($value, ['http', 'https']);
            }

            if ($keep && $name === 'style') {
                $value = self::sanitizeStyle($value);
                $keep = $value !== '';
            }

            if ($keep) {
                $element->setAttribute($name, $value);
            } else {
                $element->removeAttribute($name);
            }
        }

        // A link that opens a new tab must not hand window.opener to the target.
        if ($tag === 'a' && $element->getAttribute('target') !== '') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    /**
     * Replace an element with its children, discarding the element itself.
     */
    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent instanceof DOMNode) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    /**
     * Keep only allowlisted declarations, rejecting values that can execute or
     * fetch remote resources.
     */
    private static function sanitizeStyle(string $style): string
    {
        $kept = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = explode(':', $declaration, 2);

            $property = strtolower(trim($property));
            $value = trim($value);

            if (! in_array($property, self::ALLOWED_STYLE_PROPERTIES, true)) {
                continue;
            }

            if (str_contains($value, '\\')) {
                continue;
            }

            if (preg_match('/(url\s*\(|expression|javascript:|@import)/i', $value)) {
                continue;
            }

            $kept[] = $property.': '.$value;
        }

        return implode('; ', $kept);
    }

    /**
     * Allow relative paths and known-good schemes only. Protocol-relative URLs
     * are rejected because they inherit an attacker-controlled host.
     *
     * @param  list<string>  $schemes
     */
    private static function isSafeUrl(string $url, array $schemes = self::ALLOWED_URL_SCHEMES): bool
    {
        if ($url === '' || str_starts_with($url, '//')) {
            return false;
        }

        // Strip characters used to smuggle a scheme past this check.
        $normalised = strtolower(preg_replace('/[\s\x00-\x1F]+/', '', $url) ?? '');

        if (! preg_match('/^([a-z][a-z0-9+.\-]*):/', $normalised, $matches)) {
            return true;
        }

        return in_array($matches[1], $schemes, true);
    }
}
