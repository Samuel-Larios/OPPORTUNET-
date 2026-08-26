<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiredReminderMail;
use App\Models\JobSubscription;
use App\Models\JobSubscriptionReminder;
use App\Services\JobSubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendSubscriptionExpiryRemindersCommand extends Command
{
    protected $signature = 'subscriptions:send-expiry-reminders';

    protected $description = 'Sends one renewal email after a premium-job subscription expires.';

    public function handle(JobSubscriptionService $subscriptions): int
    {
        $now = now();
        $sent = 0;
        $processedUsers = [];

        JobSubscription::query()
            ->with('user')
            ->where('expires_at', '<=', $now)
            ->where('expires_at', '>=', $now->copy()->subDays(30))
            ->orderByDesc('expires_at')
            ->get()
            ->each(function (JobSubscription $subscription) use (&$sent, &$processedUsers, $subscriptions, $now): void {
                if (isset($processedUsers[$subscription->user_id])) {
                    return;
                }

                $processedUsers[$subscription->user_id] = true;
                $user = $subscription->user;

                if (! $user || ! $user->email || $subscriptions->hasActiveSubscription($user)) {
                    return;
                }

                if (JobSubscriptionReminder::query()->where([
                    'job_subscription_id' => $subscription->id,
                    'type' => 'expired',
                ])->exists()) {
                    return;
                }

                try {
                    Mail::to($user->email)->send(new SubscriptionExpiredReminderMail($subscription));
                    JobSubscriptionReminder::query()->create([
                        'job_subscription_id' => $subscription->id,
                        'user_id' => $user->id,
                        'type' => 'expired',
                        'sent_at' => $now,
                    ]);
                    $sent++;
                } catch (Throwable $exception) {
                    Log::warning('Subscription expiry reminder could not be sent.', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $user->id,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            });

        $this->info("Subscription expiry reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
