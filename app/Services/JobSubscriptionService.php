<?php

namespace App\Services;

use App\Models\JobSubscription;
use App\Models\JobSubscriptionPayment;
use App\Models\User;
use Illuminate\Support\Carbon;

class JobSubscriptionService
{
    public const PLANS = [1 => 500, 2 => 1000, 3 => 1500, 4 => 2000];

    public function hasActiveSubscription(?User $user): bool
    {
        return $user !== null && JobSubscription::query()->where('user_id', $user->id)->where('expires_at', '>', now())->exists();
    }

    public function activeUntil(User $user): ?Carbon
    {
        $expiresAt = JobSubscription::query()->where('user_id', $user->id)->where('expires_at', '>', now())->max('expires_at');

        return $expiresAt ? Carbon::parse($expiresAt) : null;
    }

    public function activate(JobSubscriptionPayment $payment): JobSubscription
    {
        return $payment->subscription()->firstOrCreate([], function () use ($payment) {
            $lastExpiry = JobSubscription::query()->where('user_id', $payment->user_id)->max('expires_at');
            $startsAt = $lastExpiry && Carbon::parse($lastExpiry)->isFuture() ? Carbon::parse($lastExpiry) : now();

            return [
                'user_id' => $payment->user_id,
                'weeks' => $payment->weeks,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'starts_at' => $startsAt,
                'expires_at' => $startsAt->copy()->addWeeks($payment->weeks),
            ];
        });
    }
}
