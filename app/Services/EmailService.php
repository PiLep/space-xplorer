<?php

namespace App\Services;

use Illuminate\Support\Str;

class EmailService
{
    /**
     * Generate a plain text version from HTML content.
     */
    public function generateTextVersion(string $htmlContent): string
    {
        if (empty($htmlContent)) {
            return '';
        }

        // Replace closing block-level HTML tags with spaces to separate content
        $text = preg_replace('/<\/(h[1-6]|p|div|li|blockquote)[^>]*>/i', ' ', $htmlContent);

        // Replace opening block-level HTML tags with spaces
        $text = preg_replace('/<(h[1-6]|p|div|li|blockquote)[^>]*>/i', ' ', $text);

        // Replace <br> tags with newlines
        $text = preg_replace('/<br[^>]*>/i', "\n", $text);

        // Remove remaining HTML tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Replace multiple newlines (3+) with double newlines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // Clean up whitespace (multiple spaces become single space)
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // Clean up spaces around remaining newlines
        $text = preg_replace('/ *\n */', "\n", $text);

        // Trim whitespace
        $text = trim($text);

        return $text;
    }

    /**
     * Get default email headers for better deliverability.
     *
     * @return array<string, string>
     */
    public function getDefaultHeaders(): array
    {
        $appUrl = config('app.url');
        $appName = config('app.name') ?? 'Stellar';

        return [
            'X-Mailer' => $appName,
            'Precedence' => 'bulk',
            'List-Unsubscribe' => $appUrl.'/unsubscribe',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ];
    }

    /**
     * Generate a preheader text from content.
     */
    public function generatePreheader(string $content, int $maxLength = 100): string
    {
        $text = strip_tags($content);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = trim($text);

        if (Str::length($text) > $maxLength) {
            // Use mb_substr to ensure we don't exceed maxLength (Str::limit adds "...")
            $text = mb_substr($text, 0, $maxLength);
            $text = rtrim($text);
        }

        return $text;
    }

    /**
     * Add UTM parameters to a URL for tracking.
     */
    public function addUtmParameters(string $url, string $source = 'email', string $medium = 'email', string $campaign = 'transactional'): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.http_build_query([
            'utm_source' => $source,
            'utm_medium' => $medium,
            'utm_campaign' => $campaign,
        ]);
    }
}
