<?php

namespace Tests\Feature;

use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_form_validates_required_fields(): void
    {
        $this->post(route('donations.checkout'), [])
            ->assertSessionHasErrors(['amount', 'name', 'email', 'phone']);
    }

    public function test_donation_form_normalizes_a_benin_mobile_number_before_checkout(): void
    {
        config()->set('services.fedapay.secret_key', '');

        $this->from(route('home'))->post(route('donations.checkout'), [
            'amount' => 1000, 'name' => 'Donateur test', 'email' => 'don@test.example', 'phone' => '0140308625',
        ])->assertRedirect(route('home'))
            ->assertSessionHasErrors('donation');

        $this->assertDatabaseHas('donations', [
            'donor_email' => 'don@test.example',
            'donor_phone' => '2290140308625',
            'currency' => 'XOF',
            'status' => 'configuration_error',
        ]);
    }

    public function test_donation_amount_cannot_exceed_the_fedapay_account_limit(): void
    {
        $this->post(route('donations.checkout'), [
            'amount' => 5001, 'name' => 'Donateur test', 'email' => 'don@test.example', 'phone' => '0140308625',
        ])->assertSessionHasErrors('amount');
    }
}
