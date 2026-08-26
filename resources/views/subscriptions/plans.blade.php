<x-layouts.app :title="app()->getLocale() === 'fr' ? 'Abonnement offres premium' : 'Premium job subscription'" :description="app()->getLocale() === 'fr' ? 'Accédez aux offres premium et postulez depuis Opportunet Mondiale.' : 'Access premium job offers and apply with Opportunet Mondiale.'" :show-hero="false">
    <main class="offers-page">
        <section class="offers-hero">
            <div class="container">
                <div class="offers-hero-shell reveal visible">
                    <div class="offers-hero-copy">
                        <span class="section-label">{{ app()->getLocale() === 'fr' ? 'Abonnement premium' : 'Premium subscription' }}</span>
                        <h1 class="section-title">{{ app()->getLocale() === 'fr' ? 'Accédez aux offres sans limite' : 'Get full access to job offers' }}</h1>
                        <p class="section-sub">{{ app()->getLocale() === 'fr' ? 'Lisez les offres premium dans leur intégralité et postulez directement. Paiement sécurisé par FedaPay.' : 'Read premium offers in full and apply directly. Secure payment powered by FedaPay.' }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="offers-list-section">
            <div class="container">
                @if (session('subscription_account_notice'))
                    <div class="home-alert error">{{ session('subscription_account_notice') }}</div>
                @endif
                <div class="offers-grid">
                    @foreach ($plans as $weeks => $amount)
                        <article class="offer-list-card reveal visible">
                            <span class="opportunity-type">{{ app()->getLocale() === 'fr' ? 'Accès premium' : 'Premium access' }}</span>
                            <h2>{{ $weeks }} {{ $weeks === 1 ? (app()->getLocale() === 'fr' ? 'semaine' : 'week') : (app()->getLocale() === 'fr' ? 'semaines' : 'weeks') }}</h2>
                            <p>{{ app()->getLocale() === 'fr' ? 'Accès complet aux offres premium et candidature pendant toute la durée choisie.' : 'Full access to premium offers and applications throughout your selected period.' }}</p>
                            <p class="subscription-plan-price">{{ number_format($amount, 0, ',', ' ') }} FCFA</p>
                            <div class="offer-card-actions subscription-plan-actions">
                                <a class="solid-submit" href="{{ route('subscriptions.begin', $weeks) }}">{{ app()->getLocale() === 'fr' ? 'S’abonner' : 'Subscribe' }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="section-sub subscription-information">{{ app()->getLocale() === 'fr' ? 'Le paiement s’effectue en FCFA. FedaPay proposera les moyens disponibles selon votre pays africain.' : 'Payment is in XOF. FedaPay will display methods available for your African country.' }}</p>
            </div>
        </section>
    </main>
</x-layouts.app>
