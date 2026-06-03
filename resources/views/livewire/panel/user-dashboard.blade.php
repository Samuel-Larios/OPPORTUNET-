<div class="panel-stack">
    <section class="panel-stats-grid">
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.requests') }}</span>
            <strong>{{ $stats['requests'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.contacts') }}</span>
            <strong>{{ $stats['contacts'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.cvs') }}</span>
            <strong>{{ $stats['cvs'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.trainings') }}</span>
            <strong>{{ $stats['trainings'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.testimonials') }}</span>
            <strong>{{ $stats['testimonials'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.user_dashboard.prayers') }}</span>
            <strong>{{ $stats['prayers'] }}</strong>
        </article>
    </section>

    <section class="panel-grid-3">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_requests') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentRequests as $request)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $request->service?->titre ?? 'Demande libre' }}</strong>
                            <span>{{ $request->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $request->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucune demande enregistree.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_contacts') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentContacts as $contact)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ ucfirst($contact->sujet) }}</strong>
                            <span>{{ $contact->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $contact->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun message envoye.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_trainings') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentTrainings as $registration)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $registration->formation?->titre ?? 'Formation' }}</strong>
                            <span>{{ $registration->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $registration->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucune inscription enregistree.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_cvs') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentCvs as $cvDepot)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $cvDepot->titre_poste ?: __('admin.user_cv_depots.free_profile') }}</strong>
                            <span>{{ $cvDepot->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ $cvDepot->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun service CV enregistre.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_testimonials') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentTestimonials as $testimonial)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $testimonial->typeLabel() }}</strong>
                            <span>{{ $testimonial->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ $testimonial->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun temoignage enregistre.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_dashboard.recent_prayers') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentPrayers as $prayer)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $prayer->typeLabel() }}</strong>
                            <span>{{ $prayer->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge{{ $prayer->statut === 'approuve' ? ' is-success' : ' is-muted' }}">{{ $prayer->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucune priere enregistree.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
