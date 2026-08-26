<x-layouts.panel :title="app()->getLocale() === 'fr' ? 'Mon abonnement' : 'My subscription'">
    <div class="panel-stack">
        @if (session('subscription_success')) <div class="panel-alert-success">{{ session('subscription_success') }}</div> @endif
        @if (session('subscription_pending')) <div class="panel-alert-warning">{{ session('subscription_pending') }}</div> @endif
        @error('payment') <div class="panel-alert-error">{{ $message }}</div> @enderror

        <section class="panel-card">
            <div class="panel-card-head"><h2>{{ app()->getLocale() === 'fr' ? 'Accès aux offres premium' : 'Premium job access' }}</h2></div>
            @if ($activeUntil)
                <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Votre accès premium est actif jusqu’au' : 'Your premium access is active until' }} <strong>{{ $activeUntil->locale(app()->getLocale())->translatedFormat('d F Y à H:i') }}</strong>.</p>
            @else
                <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Choisissez une durée pour consulter intégralement les offres premium et y postuler.' : 'Choose a duration to view and apply to premium job offers.' }}</p>
            @endif
            <div class="panel-grid-2">
                @foreach ($plans as $weeks => $amount)
                    <article class="panel-card subscription-plan-card" style="margin:0;">
                        <strong>{{ $weeks }} {{ $weeks === 1 ? (app()->getLocale() === 'fr' ? 'semaine' : 'week') : (app()->getLocale() === 'fr' ? 'semaines' : 'weeks') }}</strong>
                        <p style="font-size:1.4rem; margin:12px 0;">{{ number_format($amount, 0, ',', ' ') }} FCFA</p>
                        <div class="subscription-action-group">
                            <a class="panel-primary-btn" href="{{ route('subscriptions.payment', $weeks) }}">{{ app()->getLocale() === 'fr' ? 'Continuer vers le paiement' : 'Continue to payment' }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="panel-section-intro" style="margin-top:16px;">{{ app()->getLocale() === 'fr' ? 'Paiement en FCFA via FedaPay, accessible depuis le Bénin et les pays africains pris en charge par FedaPay. Mobile Money et autres moyens disponibles sont proposés par FedaPay selon le pays.' : 'Pay in XOF via FedaPay, available from Benin and FedaPay-supported African countries. Available methods are presented by FedaPay based on country.' }}</p>
        </section>

        <section class="panel-card"><div class="panel-card-head"><h2>{{ app()->getLocale() === 'fr' ? 'Historique des paiements' : 'Payment history' }}</h2></div>
            <div class="panel-table-wrap"><table class="panel-table"><thead><tr><th>{{ app()->getLocale() === 'fr' ? 'Durée' : 'Duration' }}</th><th>Montant</th><th>Statut</th><th>Date</th><th>{{ app()->getLocale() === 'fr' ? 'Vérification' : 'Check' }}</th><th></th></tr></thead><tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->weeks }} sem.</td>
                    <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if ($payment->status === 'paid')
                            {{ app()->getLocale() === 'fr' ? 'Payé' : 'Paid' }}
                        @elseif ($payment->status === 'pending')
                            {{ app()->getLocale() === 'fr' ? 'En attente de paiement' : 'Payment pending' }}
                        @else
                            {{ $payment->status }}
                        @endif
                    </td>
                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($payment->status_checked_at)
                            <small class="subscription-status-check">{{ app()->getLocale() === 'fr' ? 'FedaPay vérifié le' : 'FedaPay checked on' }} {{ $payment->status_checked_at->format('d/m/Y H:i') }}</small>
                        @else
                            <small class="subscription-status-check">{{ app()->getLocale() === 'fr' ? 'Pas encore vérifié' : 'Not checked yet' }}</small>
                        @endif
                    </td>
                    <td class="subscription-history-actions">
                        @if ($payment->status === 'pending' && $payment->checkout_url)
                            <a class="panel-primary-btn panel-small-btn" href="{{ route('subscriptions.checkout.redirect', $payment) }}">{{ app()->getLocale() === 'fr' ? 'Reprendre le paiement' : 'Resume payment' }}</a>
                        @endif
                        @if ($payment->status !== 'paid' && $payment->provider_transaction_id)
                            <form method="POST" action="{{ route('subscriptions.refresh', $payment) }}">
                                @csrf
                                <button class="panel-secondary-btn panel-small-btn">{{ app()->getLocale() === 'fr' ? 'Vérifier le statut' : 'Check status' }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">{{ app()->getLocale() === 'fr' ? 'Aucun paiement pour le moment.' : 'No payments yet.' }}</td></tr>
            @endforelse
            </tbody></table></div>
        </section>
    </div>
</x-layouts.panel>
