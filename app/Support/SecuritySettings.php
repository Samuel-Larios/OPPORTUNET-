<?php

namespace App\Support;

use App\Models\ParametreSite;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SecuritySettings
{
    protected static ?Collection $settings = null;

    public static function flush(): void
    {
        self::$settings = null;
    }

    public static function string(string $key, string $default = ''): string
    {
        return trim((string) (self::settings()->get($key)?->valeur ?? $default));
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::string($key, $default ? '1' : '0');

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function int(string $key, int $default): int
    {
        $value = self::string($key, (string) $default);

        return is_numeric($value) ? max(1, (int) $value) : $default;
    }

    /**
     * @return array<int, string>
     */
    public static function countries(string $key = 'security_geo_countries'): array
    {
        $raw = self::string($key);

        if ($raw === '') {
            return [];
        }

        return collect(preg_split('/[\s,;|]+/', strtoupper($raw)) ?: [])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected static function settings(): Collection
    {
        if (! Schema::hasTable('parametres_site')) {
            return collect();
        }

        if (self::$settings === null) {
            self::$settings = ParametreSite::query()
                ->where('groupe', 'securite')
                ->get()
                ->keyBy('cle');
        }

        return self::$settings;
    }
}
