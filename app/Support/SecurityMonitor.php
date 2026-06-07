<?php

namespace App\Support;

use App\Models\SecurityIncident;
use App\Models\SecurityIpBlock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecurityMonitor
{
    public static function ensureRequestAllowed(Request $request): void
    {
        self::ensureIpIsNotBlocked($request);
        self::ensureCountryIsAllowed($request);
    }

    public static function ensureIpIsNotBlocked(Request $request): void
    {
        $ipAddress = self::normalizeIp($request->ip());

        if ($ipAddress === null || ! Schema::hasTable('security_ip_blocks')) {
            return;
        }

        $block = SecurityIpBlock::query()
            ->where('ip_address', $ipAddress)
            ->first();

        if ($block === null) {
            return;
        }

        if (! $block->isActive()) {
            return;
        }

        throw new HttpException(403, __('security.access.ip_blocked'));
    }

    public static function ensureCountryIsAllowed(Request $request): void
    {
        $mode = strtolower(SecuritySettings::string('security_geo_mode', 'off'));

        if (! in_array($mode, ['allowlist', 'blocklist'], true)) {
            return;
        }

        $countryCode = self::countryCode($request);

        if ($countryCode === null) {
            return;
        }

        $countries = SecuritySettings::countries();

        if ($countries === []) {
            return;
        }

        $isBlocked = ($mode === 'allowlist' && ! in_array($countryCode, $countries, true))
            || ($mode === 'blocklist' && in_array($countryCode, $countries, true));

        if (! $isBlocked) {
            return;
        }

        self::recordIncident(
            $request,
            'geo_blocked',
            'country_restricted',
            [
                'mode' => $mode,
                'country' => $countryCode,
            ],
            false,
            'critical'
        );

        throw new HttpException(403, __('security.access.country_blocked'));
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function recordIncident(
        Request $request,
        string $type,
        string $reason,
        array $metadata = [],
        bool $countForAutoBlock = true,
        string $severity = 'warning'
    ): SecurityIncident {
        if (! Schema::hasTable('security_incidents')) {
            return new SecurityIncident();
        }

        $incident = SecurityIncident::query()->create([
            'user_id' => Auth::id(),
            'ip_address' => self::normalizeIp($request->ip()),
            'country_code' => self::countryCode($request),
            'type' => $type,
            'severity' => $severity,
            'reason' => $reason,
            'route_name' => $request->route()?->getName(),
            'path' => '/' . ltrim($request->path(), '/'),
            'method' => $request->method(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
            'metadata' => $metadata,
            'count_for_auto_block' => $countForAutoBlock,
            'created_at' => now(),
        ]);

        if ($countForAutoBlock && $incident->ip_address !== null) {
            self::maybeAutoBlockIp($incident->ip_address, $reason);
        }

        return $incident;
    }

    public static function manualBlock(string $ipAddress, string $reason, ?User $admin = null): void
    {
        $normalizedIp = self::normalizeIp($ipAddress);

        if ($normalizedIp === null || ! Schema::hasTable('security_ip_blocks')) {
            return;
        }

        SecurityIpBlock::query()->updateOrCreate(
            ['ip_address' => $normalizedIp],
            [
                'reason' => $reason,
                'blocked_until' => null,
                'is_manual' => true,
                'created_by' => $admin?->id,
                'last_triggered_at' => now(),
            ]
        );
    }

    public static function unblock(SecurityIpBlock $block): void
    {
        if (! Schema::hasTable('security_ip_blocks')) {
            return;
        }

        $block->delete();
    }

    public static function countryCode(Request $request): ?string
    {
        $configuredHeader = SecuritySettings::string('security_geo_header', 'CF-IPCountry');
        $headers = array_values(array_unique(array_filter([
            $configuredHeader,
            'CF-IPCountry',
        ])));

        foreach ($headers as $header) {
            $value = strtoupper(trim((string) $request->headers->get($header, '')));

            if (preg_match('/^[A-Z]{2}$/', $value) === 1) {
                return $value;
            }
        }

        return null;
    }

    private static function maybeAutoBlockIp(string $ipAddress, string $reason): void
    {
        if (! Schema::hasTable('security_incidents') || ! Schema::hasTable('security_ip_blocks')) {
            return;
        }

        $threshold = SecuritySettings::int('security_ip_auto_block_threshold', 5);
        $windowMinutes = SecuritySettings::int('security_ip_auto_block_window_minutes', 60);
        $blockDurationMinutes = SecuritySettings::int('security_ip_block_duration_minutes', 1440);

        $incidentsCount = SecurityIncident::query()
            ->where('ip_address', $ipAddress)
            ->where('count_for_auto_block', true)
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->count();

        if ($incidentsCount < $threshold) {
            return;
        }

        SecurityIpBlock::query()->updateOrCreate(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'blocked_until' => now()->addMinutes($blockDurationMinutes),
                'is_manual' => false,
                'incidents_count' => $incidentsCount,
                'last_triggered_at' => now(),
            ]
        );
    }

    private static function normalizeIp(?string $ipAddress): ?string
    {
        $ipAddress = trim((string) $ipAddress);

        return $ipAddress !== '' ? $ipAddress : null;
    }
}
