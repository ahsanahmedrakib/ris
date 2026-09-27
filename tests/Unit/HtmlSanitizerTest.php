<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    #[Test]
    public function it_strips_script_tags_and_their_contents(): void
    {
        $clean = HtmlSanitizer::clean('<p>আসসালামু আলাইকুম</p><script>alert(1)</script>');

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringContainsString('আসসালামু আলাইকুম', $clean);
    }

    #[Test]
    #[DataProvider('dangerousMarkup')]
    public function it_removes_dangerous_markup(string $html): void
    {
        $clean = HtmlSanitizer::clean($html);

        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('<object', $clean);
        $this->assertStringNotContainsString('<embed', $clean);
        $this->assertStringNotContainsString('<form', $clean);
        $this->assertStringNotContainsString('<svg', $clean);
        $this->assertStringNotContainsString('<style', $clean);
        $this->assertStringNotContainsString('<base', $clean);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function dangerousMarkup(): array
    {
        return [
            'iframe' => ['<iframe src="https://evil.test"></iframe>'],
            'object' => ['<object data="evil.swf"></object>'],
            'embed' => ['<embed src="evil.swf">'],
            'form' => ['<form action="/steal"><input name="pw"></form>'],
            'svg' => ['<svg><script>alert(1)</script></svg>'],
            'style' => ['<style>body{display:none}</style>'],
            'base' => ['<base href="https://evil.test/">'],
        ];
    }

    #[Test]
    public function it_strips_event_handler_attributes(): void
    {
        $clean = HtmlSanitizer::clean('<p onclick="steal()" onmouseover="x()">ঠিক আছে</p>');

        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringNotContainsString('steal', $clean);
    }

    #[Test]
    #[DataProvider('dangerousUrlSchemes')]
    public function it_rejects_dangerous_url_schemes(string $url): void
    {
        $clean = HtmlSanitizer::clean('<a href="'.$url.'">click</a>');

        $this->assertStringNotContainsString('href', $clean);
        $this->assertStringContainsString('click', $clean);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function dangerousUrlSchemes(): array
    {
        return [
            'javascript' => ['javascript:alert(1)'],
            'uppercase javascript' => ['JaVaScRiPt:alert(1)'],
            'data html' => ['data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='],
            'vbscript' => ['vbscript:msgbox(1)'],
            'file' => ['file:///etc/passwd'],
        ];
    }

    #[Test]
    public function it_keeps_safe_links(): void
    {
        $clean = HtmlSanitizer::clean('<a href="https://example.test/page">ভিজিট</a>');

        $this->assertStringContainsString('href="https://example.test/page"', $clean);
    }

    #[Test]
    public function it_keeps_allowlisted_formatting(): void
    {
        $clean = HtmlSanitizer::clean('<h2>শিরোনাম</h2><p><strong>গুরুত্বপূর্ণ</strong> <em>নকল</em></p><ul><li>এক</li></ul>');

        foreach (['<h2>', '<strong>', '<em>', '<ul>', '<li>'] as $tag) {
            $this->assertStringContainsString($tag, $clean);
        }
    }

    #[Test]
    public function it_keeps_bangla_text_intact(): void
    {
        $clean = HtmlSanitizer::clean('<p>আসসালামু আলাইকুম ওয়া রাহমাতুল্লাহ</p>');

        $this->assertStringContainsString('আসসালামু আলাইকুম ওয়া রাহমাতুল্লাহ', $clean);
    }

    #[Test]
    public function it_keeps_images_from_http_and_https_only(): void
    {
        $safe = HtmlSanitizer::clean('<img src="https://example.test/a.jpg" alt="a">');
        $this->assertStringContainsString('<img', $safe);
        $this->assertStringContainsString('example.test/a.jpg', $safe);

        $unsafe = HtmlSanitizer::clean('<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==">');
        $this->assertStringNotContainsString('data:text/html', $unsafe);
    }

    #[Test]
    public function it_filters_style_attributes_to_a_safe_subset(): void
    {
        $clean = HtmlSanitizer::clean('<p style="color: red; position: fixed; background-image: url(https://evil.test/x)">টেক্সট</p>');

        $this->assertStringContainsString('color', $clean);
        $this->assertStringNotContainsString('position', $clean);
        $this->assertStringNotContainsString('background-image', $clean);
        $this->assertStringNotContainsString('evil.test', $clean);
    }

    #[Test]
    public function it_drops_unknown_tags_but_keeps_their_text(): void
    {
        $clean = HtmlSanitizer::clean('<marquee>গুরুত্বপূর্ণ লেখা</marquee>');

        $this->assertStringNotContainsString('<marquee', $clean);
        $this->assertStringContainsString('গুরুত্বপূর্ণ লেখা', $clean);
    }

    #[Test]
    public function it_handles_null_and_empty_input(): void
    {
        $this->assertSame('', (string) HtmlSanitizer::clean(null));
        $this->assertSame('', (string) HtmlSanitizer::clean(''));
        $this->assertSame('   ', HtmlSanitizer::clean('   '));
    }

    #[Test]
    public function it_survives_malformed_markup(): void
    {
        $clean = HtmlSanitizer::clean('<p>অসমাপ্ত <b>বোল্ড <script>alert(1)');

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringContainsString('অসমাপ্ত', $clean);
    }

    #[Test]
    public function it_is_idempotent(): void
    {
        $once = HtmlSanitizer::clean('<p onclick="x()">ঠিকানা <a href="javascript:alert(1)">লিংক</a></p>');
        $twice = HtmlSanitizer::clean($once);

        $this->assertSame($once, $twice);
    }
}
