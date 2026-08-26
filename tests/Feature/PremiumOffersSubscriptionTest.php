<?php

namespace Tests\Feature;

use App\Models\JobSubscription;
use App\Models\JobSubscriptionPayment;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\User;
use App\Services\JobSubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumOffersSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_offer_only_shows_preview_to_a_non_subscriber(): void
    {
        $offer = $this->premiumOffer();

        $this->get(route('offers.show', $offer))
            ->assertOk()
            ->assertSeeText('Premium content')
            ->assertDontSeeText('Information confidentielle réservée aux abonnés');
    }

    public function test_public_subscription_page_displays_all_plans(): void
    {
        $this->get(route('subscriptions.plans'))
            ->assertOk()
            ->assertSeeText('500 FCFA')
            ->assertSeeText('2 000 FCFA')
            ->assertSeeText('FedaPay')
            ->assertSeeText('Subscribe');
    }

    public function test_subscription_button_sends_a_guest_to_login_then_a_user_to_payment_page(): void
    {
        $this->get(route('subscriptions.begin', 2))->assertRedirect(route('login', [
            'redirect_to' => route('subscriptions.begin', 2),
        ]));

        $this->actingAs($this->user())->get(route('subscriptions.begin', 2))
            ->assertRedirect(route('subscriptions.payment', 2));
        $this->actingAs($this->user())->get(route('subscriptions.payment', 2))
            ->assertOk()
            ->assertSeeText('1 000 FCFA')
            ->assertSeeText('Proceed to FedaPay payment');
    }

    public function test_administrator_can_reach_the_payment_page(): void
    {
        $adminRole = Role::query()->firstOrCreate(['nom' => 'admin'], ['libelle' => 'Administrateur', 'permissions' => '[]', 'actif' => true]);
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'email_verified_at' => now(), 'actif' => true]);

        $this->actingAs($admin)->get(route('subscriptions.begin', 1))
            ->assertRedirect(route('subscriptions.payment', 1));
    }

    public function test_subscriber_payment_view_is_rendered_and_rejects_an_invalid_plan_server_side(): void
    {
        $user = $this->user();

        $this->actingAs($user)->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSeeText('500 FCFA')
            ->assertSee(route('subscriptions.payment', 1), false);

        $this->actingAs($user)->post(route('subscriptions.checkout'), ['weeks' => 5])
            ->assertSessionHasErrors('weeks');
    }

    public function test_payment_redirect_page_is_only_available_to_its_owner(): void
    {
        $user = $this->user();
        $payment = JobSubscriptionPayment::query()->create([
            'reference' => fake()->uuid(), 'user_id' => $user->id, 'weeks' => 1, 'amount' => 500,
            'checkout_url' => 'https://sandbox-process.fedapay.com/example',
        ]);

        $this->actingAs($user)->get(route('subscriptions.checkout.redirect', $payment))
            ->assertOk()
            ->assertSeeText('Open FedaPay payment');
        $this->actingAs($this->user())->get(route('subscriptions.checkout.redirect', $payment))
            ->assertForbidden();
    }

    public function test_pending_payment_has_clear_resume_and_status_actions(): void
    {
        $user = $this->user();
        JobSubscriptionPayment::query()->create([
            'reference' => fake()->uuid(), 'user_id' => $user->id, 'weeks' => 1, 'amount' => 500,
            'provider_transaction_id' => '123', 'checkout_url' => 'https://sandbox-process.fedapay.com/example',
        ]);

        $this->actingAs($user)->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSeeText('Payment pending')
            ->assertSeeText('Resume payment')
            ->assertSeeText('Check status');
    }

    public function test_active_subscription_unlocks_a_premium_offer_and_application(): void
    {
        $user = $this->user();
        $offer = $this->premiumOffer();
        JobSubscription::query()->create([
            'user_id' => $user->id,
            'weeks' => 1,
            'amount' => 500,
            'currency' => 'XOF',
            'starts_at' => now(),
            'expires_at' => now()->addWeek(),
        ]);

        $this->actingAs($user)->get(route('offers.show', $offer))
            ->assertOk()
            ->assertSeeText('Information confidentielle réservée aux abonnés');

        $this->actingAs($user)->get(route('offers.apply.entry', $offer))
            ->assertRedirect(route('offers.show', $offer->slug).'#application-form');
    }

    public function test_non_subscriber_is_sent_to_subscription_page_before_applying(): void
    {
        $this->actingAs($this->user())->get(route('offers.apply.entry', $this->premiumOffer()))
            ->assertRedirect(route('subscriptions.index'));
    }

    public function test_renewal_starts_after_the_current_subscription_expires(): void
    {
        $user = $this->user();
        JobSubscription::query()->create([
            'user_id' => $user->id, 'weeks' => 1, 'amount' => 500, 'currency' => 'XOF',
            'starts_at' => now(), 'expires_at' => now()->addWeek(),
        ]);
        $payment = JobSubscriptionPayment::query()->create([
            'reference' => fake()->uuid(), 'user_id' => $user->id, 'weeks' => 2, 'amount' => 1000,
            'currency' => 'XOF', 'status' => 'paid',
        ]);

        $subscription = app(JobSubscriptionService::class)->activate($payment);

        $this->assertTrue($subscription->starts_at->greaterThan(now()->addDays(6)));
        $this->assertTrue($subscription->expires_at->greaterThan(now()->addWeeks(2)));
    }

    public function test_webhook_rejects_requests_without_a_configured_or_valid_signature(): void
    {
        $this->postJson(route('subscriptions.webhook'), ['name' => 'transaction.approved'])
            ->assertStatus(503);

        config()->set('services.fedapay.webhook_secret', 'wh_sandbox_test_secret');
        $this->postJson(route('subscriptions.webhook'), ['name' => 'transaction.approved'], [
            'X-FEDAPAY-SIGNATURE' => 't='.time().',s=invalid',
        ])->assertStatus(400);
    }

    private function user(): User
    {
        $role = Role::query()->firstOrCreate(['nom' => 'user'], ['libelle' => 'Utilisateur', 'permissions' => '[]', 'actif' => true]);

        return User::factory()->create(['role_id' => $role->id, 'email_verified_at' => now(), 'actif' => true]);
    }

    private function premiumOffer(): Opportunite
    {
        return Opportunite::query()->create([
            'titre' => 'Analyste données', 'slug' => 'analyste-donnees-'.fake()->unique()->slug(),
            'type' => 'emploi', 'description' => 'Aperçu public de cette opportunité. Information confidentielle réservée aux abonnés.',
            'statut' => 'publie', 'date_publication' => now(), 'is_premium' => true,
        ]);
    }
}
