<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubmissionGuard
{
    /**
     * @param  array<int, string>  $fields
     */
    public static function ensureSafeRequest(Request $request, array $fields, bool $requireCaptcha = false): void
    {
        self::ensureHoneypotIsEmpty($request);

        if ($requireCaptcha) {
            FormCaptcha::validate($request);
        }

        self::ensureSafePayload($request->all(), $fields, $request);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $fields
     */
    public static function ensureSafePayload(array $payload, array $fields, ?Request $request = null): void
    {
        $errors = [];

        foreach ($fields as $field) {
            $value = $payload[$field] ?? null;

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $reason = self::detectSuspiciousContent($value);

            if ($reason !== null) {
                $errors[$field] = $reason;
            }
        }

        if ($errors !== []) {
            if ($request !== null) {
                SecurityMonitor::recordIncident(
                    $request,
                    'payload_rejected',
                    implode('|', array_values($errors)),
                    [
                        'fields' => array_keys($errors),
                    ]
                );
            }

            throw ValidationException::withMessages($errors);
        }
    }

    public static function normalizeExternalUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $normalized = preg_match('#^https?://#i', $value)
            ? $value
            : 'https://' . ltrim($value, '/');

        if (! filter_var($normalized, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages([
                'url' => __('security.validation.invalid_external_url'),
            ]);
        }

        $scheme = strtolower((string) parse_url($normalized, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw ValidationException::withMessages([
                'url' => __('security.validation.invalid_external_url'),
            ]);
        }

        return $normalized;
    }

    private static function ensureHoneypotIsEmpty(Request $request): void
    {
        $honeypot = $request->input('website');

        if (is_string($honeypot) && trim($honeypot) !== '') {
            SecurityMonitor::recordIncident(
                $request,
                'honeypot_triggered',
                'honeypot_field_filled',
                [],
                true,
                'critical'
            );

            throw ValidationException::withMessages([
                'form' => __('security.validation.form_rejected'),
            ]);
        }
    }

    private static function detectSuspiciousContent(string $value): ?string
    {
        if (self::containsHtml($value)) {
            return __('security.validation.html_not_allowed');
        }

        if (self::containsLink($value)) {
            return __('security.validation.links_not_allowed');
        }

        if (self::containsPhishingLanguage($value)) {
            return __('security.validation.phishing_detected');
        }

        return null;
    }

    private static function containsHtml(string $value): bool
    {
        return trim(strip_tags($value)) !== trim($value);
    }

    private static function containsLink(string $value): bool
    {
        $valueWithoutEmails = preg_replace(
            '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i',
            ' ',
            $value
        );

        return preg_match(
            '/(?:https?:\/\/|www\.|(?:bit\.ly|tinyurl\.com|cutt\.ly|t\.co|rb\.gy|rebrand\.ly)\b|(?<!@)[a-z0-9.-]+\.(?:com|net|org|io|me|ly|gg|app|dev|shop|site|online|info|biz|click|link|live|world|fr|bj|tg|ci|sn)(?:\/|\b))/iu',
            $valueWithoutEmails ?? $value
        ) === 1;
    }

    private static function containsPhishingLanguage(string $value): bool
    {
        $normalized = Str::lower(
            Str::of($value)
                ->ascii()
                ->replaceMatches('/\s+/', ' ')
                ->trim()
                ->value()
        );

        $hasSensitiveKeyword = preg_match(
            '/\b(?:password|mot de passe|otp|pin|code de verification|verification code|identifiant|login|connexion|compte bancaire|bank account|carte bancaire|credit card|numero de carte|card number)\b/u',
            $normalized
        ) === 1;

        $hasActionKeyword = preg_match(
            '/\b(?:cliquer|cliquez|click|verify|verifier|verifiez|confirm|confirmer|update|mettre a jour|envoyer|send|transferer|transfert|payer|pay now|urgent|immediatement|immediately)\b/u',
            $normalized
        ) === 1;

        return $hasSensitiveKeyword && $hasActionKeyword;
    }
}
