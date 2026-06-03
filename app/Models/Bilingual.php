<?php

namespace App\Models;

trait Bilingual
{
    /**
     * Resolve a localized attribute using the "<field>_fr" / "<field>_en" convention.
     */
    protected function bilingual(string $base): mixed
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'fr';
        $fallbackLocale = $locale === 'fr' ? 'en' : 'fr';

        $primary = $this->getRawOriginal($base . '_' . $locale);
        if ($primary !== null && $primary !== '') {
            return $primary;
        }

        $fallback = $this->getRawOriginal($base . '_' . $fallbackLocale);
        if ($fallback !== null && $fallback !== '') {
            return $fallback;
        }

        $legacy = $this->getRawOriginal($base);

        return $legacy !== '' ? $legacy : null;
    }

    protected function bilingualAttributes(): array
    {
        return property_exists($this, 'bilingual') ? $this->bilingual : [];
    }

    public function getAttribute($key): mixed
    {
        if (
            is_string($key)
            && in_array($key, $this->bilingualAttributes(), true)
            && ! $this->hasGetMutator($key)
            && ! $this->hasAttributeGetMutator($key)
        ) {
            return $this->bilingual($key);
        }

        return parent::getAttribute($key);
    }
}
