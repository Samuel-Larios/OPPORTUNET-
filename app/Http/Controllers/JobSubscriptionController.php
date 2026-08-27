<?php

namespace App\Http\Controllers;

use App\Mail\BookDeliveryMail;
use App\Models\BookOrder;
use App\Models\Donation;
use App\Models\JobSubscriptionPayment;
use App\Services\BookPdfDeliveryService;
use App\Services\JobSubscriptionService;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use FedaPay\Webhook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobSubscriptionController extends Controller
{
    public function plans(): View
    {
        return view('subscriptions.plans', [
            'plans' => JobSubscriptionService::PLANS,
        ]);
    }

    public function begin(Request $request, int $weeks): RedirectResponse
    {
        abort_unless(array_key_exists($weeks, JobSubscriptionService::PLANS), 404);

        if (! $request->user()) {
            return redirect()->route('login', [
                'redirect_to' => route('subscriptions.begin', $weeks),
            ]);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->route('subscriptions.payment', $weeks);
    }

    public function payment(int $weeks): View
    {
        abort_unless(array_key_exists($weeks, JobSubscriptionService::PLANS), 404);

        return view('subscriptions.payment', [
            'weeks' => $weeks,
            'amount' => JobSubscriptionService::PLANS[$weeks],
        ]);
    }

    public function index(Request $request, JobSubscriptionService $subscriptions): View
    {
        $user = $request->user();

        return view('subscriptions.index', [
            'plans' => JobSubscriptionService::PLANS,
            'activeUntil' => $subscriptions->activeUntil($user),
            'payments' => $user->jobSubscriptionPayments()->latest()->take(10)->get(),
        ]);
    }

    public function checkout(Request $request, JobSubscriptionService $subscriptions): RedirectResponse
    {
        $validated = $request->validate(['weeks' => ['required', 'integer', 'in:1,2,3,4']]);
        $weeks = (int) $validated['weeks'];

        // A double click or a return from the payment page must not create a new
        // FedaPay transaction each time. Re-open the recent checkout instead.
        if (! $subscriptions->hasActiveSubscription($request->user())) {
            $pendingPayment = JobSubscriptionPayment::query()
                ->where('user_id', $request->user()->id)
                ->where('weeks', $weeks)
                ->where('status', 'pending')
                ->whereNotNull('checkout_url')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->latest()
                ->first();

            if ($pendingPayment) {
                return redirect()->route('subscriptions.checkout.redirect', $pendingPayment)
                    ->with('subscription_pending', 'Un paiement est déjà en attente. Reprenez-le depuis FedaPay.');
            }
        }

        $payment = JobSubscriptionPayment::query()->create([
            'reference' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'customer_name' => $request->user()->fullName(),
            'customer_email' => $request->user()->email,
            'customer_phone' => $request->user()->telephone,
            'customer_country' => $request->user()->pays,
            'weeks' => $weeks,
            'amount' => JobSubscriptionService::PLANS[$weeks],
        ]);

        if (! config('services.fedapay.secret_key')) {
            $payment->update(['status' => 'configuration_error']);

            return back()->withErrors(['payment' => 'Le paiement est momentanément indisponible. Veuillez réessayer plus tard.']);
        }

        try {
            FedaPay::setApiKey((string) config('services.fedapay.secret_key'));
            FedaPay::setEnvironment((string) config('services.fedapay.environment', 'sandbox'));

            $transaction = Transaction::create([
                'amount' => $payment->amount,
                'currency' => ['iso' => 'XOF'],
                'description' => "Abonnement offres premium — {$payment->weeks} semaine(s)",
                'callback_url' => route('subscriptions.callback', $payment),
                'merchant_reference' => $payment->reference,
                'custom_metadata' => [
                    'subscription_payment_reference' => $payment->reference,
                    'subscription_weeks' => $payment->weeks,
                ],
                'customer' => [
                    'firstname' => $request->user()->prenom ?: $request->user()->fullName(),
                    'lastname' => $request->user()->nom ?: '.',
                    'email' => $request->user()->email,
                ],
            ]);
            $checkout = $transaction->generateToken();
            $payment->update([
                'provider_transaction_id' => (string) $transaction->id,
                'provider_reference' => $transaction->reference ?? null,
                'checkout_url' => $checkout->url,
                'provider_payload' => ['transaction_id' => $transaction->id, 'reference' => $transaction->reference ?? null],
            ]);

            return redirect()->route('subscriptions.checkout.redirect', $payment);
        } catch (\Throwable $exception) {
            Log::warning('FedaPay subscription checkout could not be created.', ['payment' => $payment->reference, 'exception' => $exception->getMessage()]);
            $payment->update(['status' => 'failed']);

            return back()->withErrors(['payment' => 'La transaction FedaPay n’a pas pu être créée. Réessayez dans quelques instants.']);
        }
    }

    public function callback(JobSubscriptionPayment $payment, JobSubscriptionService $subscriptions): RedirectResponse
    {
        return $this->confirm($payment, $subscriptions);
    }

    public function redirectToFeda(Request $request, JobSubscriptionPayment $payment): View|RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        if (! $payment->checkout_url) {
            return redirect()->route('subscriptions.payment', $payment->weeks)
                ->withErrors(['payment' => 'Le lien de paiement FedaPay est indisponible. Veuillez recommencer.']);
        }

        return view('subscriptions.redirect', ['payment' => $payment]);
    }

    public function refresh(Request $request, JobSubscriptionPayment $payment, JobSubscriptionService $subscriptions): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        return $this->confirm($payment, $subscriptions);
    }

    public function webhook(Request $request, JobSubscriptionService $subscriptions, BookPdfDeliveryService $bookDelivery, DonationController $donations)
    {
        $webhookSecret = (string) config('services.fedapay.webhook_secret');

        if ($webhookSecret === '') {
            return response()->json(['message' => 'Webhook FedaPay non configuré.'], 503);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('X-FEDAPAY-SIGNATURE'),
                $webhookSecret,
            );
        } catch (\Throwable $exception) {
            Log::warning('FedaPay webhook signature rejected.', ['exception' => $exception->getMessage()]);

            return response()->json(['message' => 'Signature webhook invalide.'], 400);
        }

        if (! in_array((string) ($event->name ?? ''), ['transaction.approved', 'transaction.transferred'], true)) {
            return response()->json(['received' => true]);
        }

        $payment = JobSubscriptionPayment::query()
            ->where('provider_transaction_id', (string) ($event->object_id ?? ''))
            ->first();

        if ($payment) {
            $this->syncPayment($payment, $subscriptions);
        }

        $bookOrder = BookOrder::query()
            ->with(['book', 'user'])
            ->where('provider_transaction_id', (string) ($event->object_id ?? ''))
            ->first();

        if ($bookOrder && ! $bookOrder->delivered_at) {
            $bookOrder->update(['status' => 'paid', 'paid_at' => now()]);
            $bookDelivery->secure($bookOrder->fresh('book'));
            Mail::to($bookOrder->user->email)->send(new BookDeliveryMail($bookOrder->fresh(['book', 'user'])));
            $bookOrder->update(['delivered_at' => now()]);
        }

        $donation = Donation::query()
            ->where('provider_transaction_id', (string) ($event->object_id ?? ''))
            ->first();

        if ($donation) {
            $donations->sync($donation);
        }

        return response()->json(['received' => true]);
    }

    private function confirm(JobSubscriptionPayment $payment, JobSubscriptionService $subscriptions): RedirectResponse
    {
        if ($payment->status === 'paid') {
            return redirect()->route('subscriptions.index')->with('subscription_success', 'Votre abonnement est déjà actif.');
        }

        if ($this->syncPayment($payment, $subscriptions)) {
            return redirect()->route('subscriptions.index')->with('subscription_success', 'Paiement confirmé : votre accès premium est actif.');
        }

        $payment->refresh();
        $message = in_array($payment->status, ['failed', 'declined', 'canceled'], true)
            ? 'FedaPay indique que ce paiement n’a pas été validé. Vous pouvez recommencer le paiement.'
            : 'FedaPay confirme que ce paiement est toujours en attente. Reprenez le paiement ou vérifiez à nouveau son statut.';

        return redirect()->route('subscriptions.index')->with('subscription_pending', $message);
    }

    private function syncPayment(JobSubscriptionPayment $payment, JobSubscriptionService $subscriptions): bool
    {
        try {
            FedaPay::setApiKey((string) config('services.fedapay.secret_key'));
            FedaPay::setEnvironment((string) config('services.fedapay.environment', 'sandbox'));
            $transaction = Transaction::retrieve($payment->provider_transaction_id);
            $payload = method_exists($transaction, 'toArray') ? $transaction->toArray() : ['status' => $transaction->status];
            $payment->update(array_filter([
                'status' => (string) $transaction->status,
                'provider_status' => (string) $transaction->status,
                'status_checked_at' => now(),
                'payment_method' => data_get($payload, 'payment_method') ?: data_get($payload, 'mode'),
                'customer_phone' => data_get($payload, 'customer.phone_number') ?: data_get($payload, 'customer.phone') ?: $payment->customer_phone,
                'customer_email' => data_get($payload, 'customer.email') ?: $payment->customer_email,
                'customer_country' => data_get($payload, 'customer.country') ?: $payment->customer_country,
                'provider_payload' => $payload,
            ], static fn ($value) => $value !== null));

            if ($transaction->wasPaid()) {
                $payment->update(['status' => 'paid', 'paid_at' => now()]);
                $subscriptions->activate($payment->fresh());

                return true;
            }
        } catch (\Throwable $exception) {
            Log::warning('FedaPay subscription payment verification failed.', ['payment' => $payment->reference, 'exception' => $exception->getMessage()]);
        }

        return false;
    }
}
