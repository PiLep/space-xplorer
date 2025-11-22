<?php

use App\Services\EmailService;

beforeEach(function () {
    $this->service = new EmailService;
});

it('generates plain text version from HTML content', function () {
    $html = '<h1>Title</h1><p>This is a paragraph with <strong>bold</strong> text.</p>';

    $text = $this->service->generateTextVersion($html);

    expect($text)->toBe('Title This is a paragraph with bold text.')
        ->and($text)->not->toContain('<h1>')
        ->and($text)->not->toContain('<p>')
        ->and($text)->not->toContain('<strong>')
        ->and($text)->not->toContain('</strong>');
});

it('decodes HTML entities in text version', function () {
    $html = '<p>Price: &euro;100 &amp; Tax: &quot;20%&quot;</p>';

    $text = $this->service->generateTextVersion($html);

    expect($text)->toContain('€')
        ->and($text)->toContain('&')
        ->and($text)->toContain('"');
});

it('cleans up whitespace in text version', function () {
    $html = '<p>Text   with    multiple     spaces</p>';

    $text = $this->service->generateTextVersion($html);

    expect($text)->toBe('Text with multiple spaces');
});

it('replaces multiple newlines with double newlines', function () {
    $html = "<p>First paragraph</p>\n\n\n\n<p>Second paragraph</p>";

    $text = $this->service->generateTextVersion($html);

    expect($text)->not->toContain("\n\n\n")
        ->and($text)->toContain("\n\n");
});

it('trims text version', function () {
    $html = '   <p>Content</p>   ';

    $text = $this->service->generateTextVersion($html);

    expect($text)->not->toStartWith(' ')
        ->and($text)->not->toEndWith(' ');
});

it('handles empty HTML content', function () {
    $text = $this->service->generateTextVersion('');

    expect($text)->toBe('');
});

it('handles HTML with only tags', function () {
    $html = '<div></div><span></span>';

    $text = $this->service->generateTextVersion($html);

    expect($text)->toBe('');
});

it('returns default email headers', function () {
    config(['app.url' => 'https://example.com']);
    config(['app.name' => 'Test App']);

    $headers = $this->service->getDefaultHeaders();

    expect($headers)->toBeArray()
        ->and($headers['X-Mailer'])->toBe('Test App')
        ->and($headers['Precedence'])->toBe('bulk')
        ->and($headers['List-Unsubscribe'])->toBe('https://example.com/unsubscribe')
        ->and($headers['List-Unsubscribe-Post'])->toBe('List-Unsubscribe=One-Click');
});

it('uses default app name when not configured', function () {
    config(['app.name' => null]);

    $headers = $this->service->getDefaultHeaders();

    expect($headers['X-Mailer'])->toBe('Stellar');
});

it('generates preheader from content', function () {
    $content = '<p>This is a short preheader text</p>';

    $preheader = $this->service->generatePreheader($content);

    expect($preheader)->toBe('This is a short preheader text')
        ->and($preheader)->not->toContain('<p>')
        ->and($preheader)->not->toContain('</p>');
});

it('truncates preheader when exceeding max length', function () {
    $content = '<p>'.str_repeat('A', 150).'</p>';

    $preheader = $this->service->generatePreheader($content, 100);

    expect(strlen($preheader))->toBeLessThanOrEqual(100);
});

it('uses default max length of 100 for preheader', function () {
    $content = '<p>'.str_repeat('A', 150).'</p>';

    $preheader = $this->service->generatePreheader($content);

    expect(strlen($preheader))->toBeLessThanOrEqual(100);
});

it('allows custom max length for preheader', function () {
    $content = '<p>'.str_repeat('A', 200).'</p>';

    $preheader = $this->service->generatePreheader($content, 50);

    expect(strlen($preheader))->toBeLessThanOrEqual(50);
});

it('decodes HTML entities in preheader', function () {
    $content = '<p>Price: &euro;100</p>';

    $preheader = $this->service->generatePreheader($content);

    expect($preheader)->toContain('€');
});

it('trims preheader content', function () {
    $content = '   <p>Content</p>   ';

    $preheader = $this->service->generatePreheader($content);

    expect($preheader)->not->toStartWith(' ')
        ->and($preheader)->not->toEndWith(' ');
});

it('adds UTM parameters to URL without query string', function () {
    $url = 'https://example.com/page';

    $urlWithUtm = $this->service->addUtmParameters($url);

    expect($urlWithUtm)->toContain('utm_source=email')
        ->and($urlWithUtm)->toContain('utm_medium=email')
        ->and($urlWithUtm)->toContain('utm_campaign=transactional')
        ->and($urlWithUtm)->toStartWith('https://example.com/page?');
});

it('adds UTM parameters to URL with existing query string', function () {
    $url = 'https://example.com/page?existing=param';

    $urlWithUtm = $this->service->addUtmParameters($url);

    expect($urlWithUtm)->toContain('utm_source=email')
        ->and($urlWithUtm)->toContain('utm_medium=email')
        ->and($urlWithUtm)->toContain('utm_campaign=transactional')
        ->and($urlWithUtm)->toContain('existing=param')
        ->and($urlWithUtm)->toContain('&utm_source');
});

it('allows custom UTM source', function () {
    $url = 'https://example.com/page';

    $urlWithUtm = $this->service->addUtmParameters($url, 'newsletter');

    expect($urlWithUtm)->toContain('utm_source=newsletter');
});

it('allows custom UTM medium', function () {
    $url = 'https://example.com/page';

    $urlWithUtm = $this->service->addUtmParameters($url, 'email', 'sms');

    expect($urlWithUtm)->toContain('utm_medium=sms');
});

it('allows custom UTM campaign', function () {
    $url = 'https://example.com/page';

    $urlWithUtm = $this->service->addUtmParameters($url, 'email', 'email', 'welcome');

    expect($urlWithUtm)->toContain('utm_campaign=welcome');
});

it('handles URL with hash fragment', function () {
    $url = 'https://example.com/page#section';

    $urlWithUtm = $this->service->addUtmParameters($url);

    expect($urlWithUtm)->toContain('utm_source=email')
        ->and($urlWithUtm)->toContain('#section');
});

it('handles URL with both query string and hash', function () {
    $url = 'https://example.com/page?param=value#section';

    $urlWithUtm = $this->service->addUtmParameters($url);

    expect($urlWithUtm)->toContain('utm_source=email')
        ->and($urlWithUtm)->toContain('param=value')
        ->and($urlWithUtm)->toContain('#section');
});

