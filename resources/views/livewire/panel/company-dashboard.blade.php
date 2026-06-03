<div class="panel-stack">
    <section class="panel-stats-grid">
        <article class="panel-stat-card">
            <span>{{ __('admin.company_dashboard.offers') }}</span>
            <strong>{{ $stats['offers'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.company_dashboard.pending_offers') }}</span>
            <strong>{{ $stats['pendingOffers'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.company_dashboard.published_offers') }}</span>
            <strong>{{ $stats['publishedOffers'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.company_dashboard.proposed_profiles') }}</span>
            <strong>{{ $stats['proposedProfiles'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.company_dashboard.validated_profiles') }}</span>
            <strong>{{ $stats['validatedProfiles'] }}</strong>
        </article>
    </section>

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.company_dashboard.recent_offers') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentOffers as $offer)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $offer->titre }}</strong>
                            <span>{{ $offer->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ __('admin.offers.statuses.' . $offer->statut) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">{{ __('admin.company_dashboard.no_offers') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.company_dashboard.recent_profiles') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentApplications as $application)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $application->prenom }} {{ $application->nom }}</strong>
                            <span>{{ $application->opportunite?->titre }}</span>
                        </div>
                        <span class="panel-badge">{{ __('admin.applications.statuses.' . $application->statut) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">{{ __('admin.company_dashboard.no_profiles') }}</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
