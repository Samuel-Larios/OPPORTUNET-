<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class FormCaptcha
{
    private const SESSION_KEY = 'security_form_captcha';

    /**
     * @return array{left:int,right:int,answer:string,expires_at:int}
     */
    public static function ensureChallenge(?Request $request = null): array
    {
        $request ??= request();

        $challenge = $request->session()->get(self::SESSION_KEY);

        if (is_array($challenge) && ($challenge['expires_at'] ?? 0) > now()->timestamp) {
            return $challenge;
        }

        return self::regenerate($request);
    }

    public static function prompt(?Request $request = null): string
    {
        $challenge = self::ensureChallenge($request);

        return $challenge['left'] . ' + ' . $challenge['right'];
    }

    public static function isEnabled(): bool
    {
        return SecuritySettings::bool('security_captcha_enabled', true);
    }

    public static function validate(Request $request): void
    {
        if (! self::isEnabled()) {
            return;
        }

        $challenge = self::ensureChallenge($request);
        $submitted = trim((string) $request->input('captcha_answer'));

        $isValid = false;

        if ($submitted !== '') {
            try {
                $expected = (string) Crypt::decryptString((string) $challenge['answer']);
                $isValid = hash_equals($expected, $submitted);
            } catch (\Throwable) {
                $isValid = false;
            }
        }

        if (! $isValid) {
            SecurityMonitor::recordIncident(
                $request,
                'captcha_failed',
                'captcha_validation_failed',
                ['submitted' => $submitted === '' ? 'empty' : 'provided']
            );

            throw ValidationException::withMessages([
                'captcha_answer' => __('security.validation.captcha_invalid'),
            ]);
        }

        self::regenerate($request);
    }

    /**
     * @return array{left:int,right:int,answer:string,expires_at:int}
     */
    public static function regenerate(?Request $request = null): array
    {
        $request ??= request();

        $left = random_int(1, 9);
        $right = random_int(1, 9);
        $challenge = [
            'left' => $left,
            'right' => $right,
            'answer' => Crypt::encryptString((string) ($left + $right)),
            'expires_at' => now()->addMinutes(20)->timestamp,
        ];

        $request->session()->put(self::SESSION_KEY, $challenge);

        return $challenge;
    }
}
