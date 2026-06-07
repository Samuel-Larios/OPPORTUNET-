<?php

namespace Tests;

use App\Support\SecuritySettings;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Crypt;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SecuritySettings::flush();
    }

    protected function withFormCaptcha(int $left = 4, int $right = 5): static
    {
        return $this->withSession([
            'security_form_captcha' => [
                'left' => $left,
                'right' => $right,
                'answer' => Crypt::encryptString((string) ($left + $right)),
                'expires_at' => now()->addMinutes(20)->timestamp,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function captchaPayload(array $payload, int $left = 4, int $right = 5): array
    {
        return array_merge($payload, [
            'captcha_answer' => (string) ($left + $right),
        ]);
    }
}
