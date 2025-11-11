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
        // Remove HTML tags
        $text = strip_tags($htmlContent);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Replace multiple newlines with double newlines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

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
        $appName = config('app.name', 'Stellar');

        return [
            'X-Mailer' => $appName,
            'Precedence' => 'bulk',
            'List-Unsubscribe' => $appUrl . '/unsubscribe',
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
            $text = Str::limit($text, $maxLength);
        }

        return $text;
    }

    /**
     * Add UTM parameters to a URL for tracking.
     */
    public function addUtmParameters(string $url, string $source = 'email', string $medium = 'email', string $campaign = 'transactional'): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query([
            'utm_source' => $source,
            'utm_medium' => $medium,
            'utm_campaign' => $campaign,
        ]);
    }
}
