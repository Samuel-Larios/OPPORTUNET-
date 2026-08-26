<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:100', 'max:5000'],
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $phone = preg_replace('/\D+/', '', $data['phone']);
        $phone = str_starts_with($phone, '0') ? '229'.$phone : $phone;

        $donation = Donation::create([
            'reference' => (string) Str::uuid(),
            'amount' => $data['amount'],
            'currency' => 'XOF',
            'donor_name' => $data['name'],
            'donor_email' => $data['email'],
            'donor_phone' => $phone,
        ]);

        if (! config('services.fedapay.secret_key')) {
            $donation->update(['status' => 'configuration_error']);

            return back()->withInput()->withErrors(['donation' => 'Le paiement est indisponible : la configuration FedaPay est incomplète.']);
        }

        try {
            FedaPay::setApiKey((string) config('services.fedapay.secret_key'));
            FedaPay::setEnvironment((string) config('services.fedapay.environment', 'sandbox'));

            $transaction = Transaction::create([
                'amount' => $donation->amount,
                'currency' => ['iso' => $donation->currency],
                'description' => 'Don Opportunet Mondiale',
                // The same confirmation route is always used, including locally.
                'callback_url' => route('donations.callback', $donation),
                'merchant_reference' => $donation->reference,
                'custom_metadata' => ['donation_reference' => $donation->reference],
                'customer' => [
                    'firstname' => $donation->donor_name,
                    'lastname' => '.',
                    'email' => $donation->donor_email,
                    'phone' => $donation->donor_phone,
                ],
            ]);
            $checkout = $transaction->generateToken();
            $donation->update([
                'provider_transaction_id' => (string) $transaction->id,
                'provider_reference' => $transaction->reference ?? null,
                'checkout_url' => $checkout->url,
                'provider_payload' => ['transaction_id' => $transaction->id, 'reference' => $transaction->reference ?? null],
            ]);

            return redirect()->route('donations.redirect', $donation);
        } catch (\Throwable $exception) {
            Log::warning('FedaPay donation checkout could not be created.', [
                'donation' => $donation->reference,
                'exception' => $exception->getMessage(),
                'type' => $exception::class,
                'response' => method_exists($exception, 'getJsonBody') ? $exception->getJsonBody() : null,
            ]);
            $donation->update(['status' => 'failed']);

            return back()->withInput()->withErrors(['donation' => 'La transaction FedaPay n’a pas pu être créée. Réessayez dans quelques instants.']);
        }
    }

    public function redirect(Donation $donation): View|RedirectResponse
    {
        if (! $donation->checkout_url) {
            return redirect()->route('home')->withErrors(['donation' => 'Le lien de paiement FedaPay est indisponible. Veuillez recommencer.']);
        }

        return view('donations.redirect', compact('donation'));
    }

    public function callback(Donation $donation): RedirectResponse
    {
        $this->sync($donation);

        return redirect()->route('home')->with(
            $donation->fresh()->status === 'paid' ? 'donation_success' : 'donation_pending',
            $donation->fresh()->status === 'paid'
                ? 'Merci : votre don a été confirmé par FedaPay.'
                : 'Votre paiement est en cours de confirmation. Nous mettrons le don à jour dès la confirmation de FedaPay.',
        );
    }

    public function sync(Donation $donation): bool
    {
        if (! $donation->provider_transaction_id || ! config('services.fedapay.secret_key')) {
            return false;
        }

        try {
            FedaPay::setApiKey((string) config('services.fedapay.secret_key'));
            FedaPay::setEnvironment((string) config('services.fedapay.environment', 'sandbox'));
            $transaction = Transaction::retrieve($donation->provider_transaction_id);
            $payload = method_exists($transaction, 'toArray') ? $transaction->toArray() : ['status' => $transaction->status];
            $donation->update([
                'provider_status' => (string) $transaction->status,
                'status_checked_at' => now(),
                'provider_payload' => $payload,
                'status' => $transaction->wasPaid() ? 'paid' : (string) $transaction->status,
                'paid_at' => $transaction->wasPaid() ? ($donation->paid_at ?? now()) : $donation->paid_at,
            ]);

            return $transaction->wasPaid();
        } catch (\Throwable $exception) {
            Log::warning('FedaPay donation verification failed.', ['donation' => $donation->reference, 'exception' => $exception->getMessage()]);

            return false;
        }
    }
}
