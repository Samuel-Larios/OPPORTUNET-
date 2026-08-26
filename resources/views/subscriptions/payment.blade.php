<x-layouts.panel :title="app()->getLocale() === 'fr' ? 'Paiement de l’abonnement' : 'Subscription payment'">
    <div class="panel-stack">
        @error('payment')
            <div class="panel-alert-error">{{ $message }}</div>
        @enderror
        <section class="panel-card" style="max-width:760px;">
            <div class="panel-card-head"><h2>{{ app()->getLocale() === 'fr' ? 'Finaliser votre abonnement' : 'Complete your subscription' }}</h2></div>
            <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Vous avez choisi' : 'You selected' }} <strong>{{ $weeks }} {{ $weeks === 1 ? (app()->getLocale() === 'fr' ? 'semaine' : 'week') : (app()->getLocale() === 'fr' ? 'semaines' : 'weeks') }}</strong>.</p>
            <p style="font-size:2rem; font-weight:700; margin:18px 0;">{{ number_format($amount, 0, ',', ' ') }} FCFA</p>
            <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Vous serez redirigé vers FedaPay afin de choisir le moyen de paiement disponible dans votre pays. Votre accès est activé seulement après confirmation du paiement.' : 'You will be redirected to FedaPay to choose a payment method available in your country. Access is activated only after payment confirmation.' }}</p>
            @if (config('services.fedapay.environment') === 'sandbox')
                <div class="panel-alert-warning" style="margin-top:16px;">
                    {{ app()->getLocale() === 'fr' ? 'Mode test FedaPay : choisissez « Momo Test » et utilisez 64000001 ou 66000001 pour simuler un paiement réussi.' : 'FedaPay test mode: choose “Momo Test” and use 64000001 or 66000001 to simulate a successful payment.' }}
                </div>
            @endif
            <form method="POST" action="{{ route('subscriptions.checkout') }}" class="subscription-action-group" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').textContent='{{ app()->getLocale() === 'fr' ? 'Redirection vers FedaPay…' : 'Redirecting to FedaPay…' }}';">
                @csrf
                <input type="hidden" name="weeks" value="{{ $weeks }}">
                <button class="panel-primary-btn" type="submit">{{ app()->getLocale() === 'fr' ? 'Procéder au paiement FedaPay' : 'Proceed to FedaPay payment' }}</button>
                <a class="panel-secondary-btn" href="{{ route('subscriptions.plans') }}">{{ app()->getLocale() === 'fr' ? 'Changer de forfait' : 'Change plan' }}</a>
            </form>
        </section>
    </div>
</x-layouts.panel>
