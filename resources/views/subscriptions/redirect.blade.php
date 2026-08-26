<x-layouts.panel :title="app()->getLocale() === 'fr' ? 'Redirection vers FedaPay' : 'Redirecting to FedaPay'">
    <div class="panel-stack">
        <section class="panel-card" style="max-width:760px;">
            <div class="panel-card-head"><h2>{{ app()->getLocale() === 'fr' ? 'Ouverture du paiement sécurisé' : 'Opening secure payment' }}</h2></div>
            <div class="subscription-redirect-copy">
                <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Vous allez être redirigé vers FedaPay pour finaliser votre paiement.' : 'You are being redirected to FedaPay to complete your payment.' }}</p>
                <p class="panel-section-intro">{{ app()->getLocale() === 'fr' ? 'Si la page ne s’ouvre pas automatiquement, utilisez ce bouton.' : 'If the page does not open automatically, use this button.' }}</p>
            </div>
            <div class="subscription-action-group">
                <a id="fedapay-checkout-link" class="panel-primary-btn" href="{{ $payment->checkout_url }}">{{ app()->getLocale() === 'fr' ? 'Ouvrir le paiement FedaPay' : 'Open FedaPay payment' }}</a>
            </div>
        </section>
    </div>
    <script>
        window.setTimeout(() => window.location.assign(@json($payment->checkout_url)), 500);
    </script>
</x-layouts.panel>
