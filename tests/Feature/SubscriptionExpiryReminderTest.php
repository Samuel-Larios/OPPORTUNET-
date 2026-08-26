<?php

namespace Tests\Feature;

use App\Mail\SubscriptionExpiredReminderMail;
use App\Models\JobSubscription;
use App\Models\JobSubscriptionPayment;
use App\Models\JobSubscriptionReminder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubscriptionExpiryReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_one_email_after_expiry_and_records_the_reminder(): void
    {
        Mail::fake();
        $user = $this->user();
        $subscription = JobSubscription::query()->create([
            'user_id' => $user->id, 'weeks' => 1, 'amount' => 500, 'currency' => 'XOF',
            'starts_at' => now()->subWeeks(2), 'expires_at' => now()->subDay(),
        ]);

        $this->artisan('subscriptions:send-expiry-reminders')->assertSuccessful();

        Mail::assertSent(SubscriptionExpiredReminderMail::class, fn ($mail) => $mail->hasTo($user->email));
        $this->assertDatabaseHas('job_subscription_reminders', [
            'job_subscription_id' => $subscription->id, 'user_id' => $user->id, 'type' => 'expired',
        ]);

        $this->artisan('subscriptions:send-expiry-reminders')->assertSuccessful();
        Mail::assertSent(SubscriptionExpiredReminderMail::class, 1);
    }

    public function test_payment_details_can_be_saved_with_the_payment_record(): void
    {
        $user = $this->user();
        $payment = JobSubscriptionPayment::query()->create([
            'reference' => fake()->uuid(), 'user_id' => $user->id, 'weeks' => 1, 'amount' => 500,
            'customer_name' => 'Awa Kossi', 'customer_email' => 'awa@example.test',
            'customer_phone' => '64000001', 'customer_country' => 'Bénin',
            'payment_method' => 'momo', 'provider_status' => 'approved', 'status' => 'paid',
        ]);

        $this->assertSame('64000001', $payment->customer_phone);
        $this->assertSame('momo', $payment->payment_method);
        $this->assertSame('approved', $payment->provider_status);
    }

    private function user(): User
    {
        $role = Role::query()->firstOrCreate(['nom' => 'user'], ['libelle' => 'Utilisateur', 'permissions' => '[]', 'actif' => true]);

        return User::factory()->create(['role_id' => $role->id, 'email_verified_at' => now(), 'actif' => true]);
    }
}
