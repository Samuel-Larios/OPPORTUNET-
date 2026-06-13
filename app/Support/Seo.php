<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    /**
     * @param  array<string, mixed>  $query
     */
    public static function localizedUrl(string $url, string $locale, array $query = []): string
    {
        unset($query['lang']);

        if (in_array($locale, ['fr', 'en'], true)) {
            $query['lang'] = $locale;
        }

        if ($query === []) {
            return $url;
        }

        ksort($query);

        return $url . '?' . http_build_query($query);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, string>
     */
    public static function alternateLocaleUrls(string $url, array $query = []): array
    {
        $frUrl = self::localizedUrl($url, 'fr', $query);
        $enUrl = self::localizedUrl($url, 'en', $query);

        return [
            'fr' => $frUrl,
            'fr-BJ' => $frUrl,
            'en' => $enUrl,
            'en-BJ' => $enUrl,
            'x-default' => $frUrl,
        ];
    }

    public static function description(?string $text, int $limit = 170): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');

        if ($normalized === '') {
            return '';
        }

        return Str::limit($normalized, $limit, '...');
    }

    public static function absoluteImageUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        if (str_starts_with($path, '//')) {
            return 'https:' . $path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return asset(ltrim($path, '/'));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function schema(string $type, array $payload = []): array
    {
        return self::stripEmpty(array_merge([
            '@context' => 'https://schema.org',
            '@type' => $type,
        ], $payload));
    }

    /**
     * @param  array<int, array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumb(array $items): array
    {
        $list = [];

        foreach (array_values($items) as $index => $item) {
            if (! isset($item['name'], $item['url'])) {
                continue;
            }

            $name = trim((string) $item['name']);
            $url = trim((string) $item['url']);

            if ($name === '' || $url === '') {
                continue;
            }

            $list[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $name,
                'item' => $url,
            ];
        }

        return self::schema('BreadcrumbList', [
            'itemListElement' => $list,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function stripEmpty(array $data): array
    {
        $cleaned = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::stripEmpty($value);
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            if (is_array($value) && $value === []) {
                continue;
            }

            $cleaned[$key] = $value;
        }

        return $cleaned;
    }
}
