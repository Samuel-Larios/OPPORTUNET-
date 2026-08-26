<x-layouts.panel title="Redirection vers FedaPay">
    <section class="panel-card">
        <h1>Paiement sécurisé</h1>
        <p>Vous allez être redirigé vers FedaPay pour finaliser votre don.</p>
        <a id="fedapay-checkout-link" class="panel-primary-btn" href="{{ $donation->checkout_url }}">Ouvrir le paiement FedaPay</a>
    </section>
    <script>window.setTimeout(() => window.location.assign(@json($donation->checkout_url)), 350);</script>
</x-layouts.panel>
