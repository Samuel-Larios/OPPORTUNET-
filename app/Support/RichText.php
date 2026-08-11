<?php

namespace App\Support;

class RichText
{
    public static function sanitize(?string $html): string
    {
        $html = strip_tags((string) $html, '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><div><span>');

        return preg_replace_callback('/<(\\/?)([a-z0-9]+)(?:\\s+[^>]*)?>/i', function (array $matches): string {
            $closing = $matches[1] === '/';
            $tag = strtolower($matches[2]);

            if ($closing || ! in_array($tag, ['p', 'div', 'span', 'h2', 'h3', 'h4'], true)) {
                return '<' . ($closing ? '/' : '') . $tag . '>';
            }

            if (preg_match('/text-align\\s*:\\s*(left|center|right|justify)/i', $matches[0], $alignment)) {
                return '<' . $tag . ' style="text-align: ' . strtolower($alignment[1]) . '">';
            }

            return '<' . $tag . '>';
        }, $html) ?? '';
    }
}
