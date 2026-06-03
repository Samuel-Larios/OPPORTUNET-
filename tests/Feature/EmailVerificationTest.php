<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_sends_a_verification_email_notification(): void
    {
        Notification::fake();

        Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        $response = $this->post(route('register.user.store'), [
            'prenom' => 'Samuel',
            'nom' => 'Etudiant',
            'email' => 'samuel.etudiant@example.com',
            'telephone' => '0102030405',
            'pays' => 'Benin',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::query()->where('email', 'samuel.etudiant@example.com')->first();

        $response->assertRedirect(route('verification.notice'));
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_student_registration_handles_verification_email_delivery_failure_gracefully(): void
    {
        Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        $this->mock(ChannelManager::class, function ($mock): void {
            $mock->shouldReceive('send')
                ->once()
                ->andThrow(new TransportException('SMTP unreachable'));
            $mock->shouldReceive('sendNow')
                ->andThrow(new TransportException('SMTP unreachable'));
        });

        $response = $this->post(route('register.user.store'), [
            'prenom' => 'Samuel',
            'nom' => 'Etudiant',
            'email' => 'samuel.echec@example.com',
            'telephone' => '0102030405',
            'pays' => 'Benin',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::query()->where('email', 'samuel.echec@example.com')->first();

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHasErrors('verification_email');
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_unverified_user_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_resend_verification_email_shows_error_when_delivery_fails(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->mock(ChannelManager::class, function ($mock): void {
            $mock->shouldReceive('send')
                ->once()
                ->andThrow(new TransportException('SMTP unreachable'));
            $mock->shouldReceive('sendNow')
                ->andThrow(new TransportException('SMTP unreachable'));
        });

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHasErrors('verification_email');
    }
}
