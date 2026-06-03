<div class="panel-stack">
    <section class="panel-stats-grid">
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.users') }}</span>
            <strong>{{ $stats['users'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.offers') }}</span>
            <strong>{{ $stats['offers'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.articles') }}</span>
            <strong>{{ $stats['articles'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.article_comments') }}</span>
            <strong>{{ $stats['article_comments'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.services') }}</span>
            <strong>{{ $stats['services'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.cvs') }}</span>
            <strong>{{ $stats['cvs'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.requests') }}</span>
            <strong>{{ $stats['requests'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.contacts') }}</span>
            <strong>{{ $stats['contacts'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.trainings') }}</span>
            <strong>{{ $stats['trainings'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.prayers') }}</span>
            <strong>{{ $stats['prayers'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.dashboard.applications') }}</span>
            <strong>{{ $stats['applications'] }}</strong>
        </article>
    </section>

    <section class="panel-grid-3">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_users') }}</h2>
            </div>
            <div class="panel-list">
                @foreach ($recentUsers as $user)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $user->fullName() }}</strong>
                            <span>{{ $user->email }}</span>
                        </div>
                        <span class="panel-badge">{{ $user->role?->libelle }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_requests') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentRequests as $request)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $request->prenom }} {{ $request->nom }}</strong>
                            <span>{{ $request->service?->titre ?? 'Service libre' }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $request->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucune demande pour le moment.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_applications') }}</h2>
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
                    <p class="panel-empty">Aucune candidature pour le moment.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_contacts') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentContacts as $contact)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $contact->fullName() }}</strong>
                            <span>{{ $contact->subjectLabel() }}</span>
                        </div>
                        <span class="panel-badge">{{ $contact->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun contact pour le moment.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_offers') }}</h2>
            </div>
            <div class="panel-list">
                @foreach ($recentOffers as $offer)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $offer->titre }}</strong>
                            <span>{{ $offer->organisation ?: 'Sans organisation' }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst($offer->statut) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_articles') }}</h2>
            </div>
            <div class="panel-list">
                @foreach ($recentArticles as $article)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $article->titre }}</strong>
                            <span>{{ $article->slug }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst($article->statut) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_article_comments') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentArticleComments as $comment)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $comment->auteur_nom }}</strong>
                            <span>{{ $comment->article?->titre ?: __('admin.article_comments.labels.article') }}</span>
                        </div>
                        <span class="panel-badge">{{ $comment->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun commentaire pour le moment.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_services') }}</h2>
            </div>
            <div class="panel-list">
                @foreach ($recentServices as $service)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $service->titre }}</strong>
                            <span>{{ __('admin.services.types.' . $service->type) }}</span>
                        </div>
                        <span class="panel-badge{{ $service->actif ? ' is-success' : ' is-muted' }}">
                            {{ $service->actif ? __('admin.users.active') : __('admin.users.inactive') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.dashboard.recent_cvs') }}</h2>
            </div>
            <div class="panel-list">
                @forelse ($recentCvs as $cvDepot)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $cvDepot->prenom }} {{ $cvDepot->nom }}</strong>
                            <span>{{ $cvDepot->titre_poste ?: __('admin.cv_depots.free_profile') }}</span>
                        </div>
                        <span class="panel-badge">{{ $cvDepot->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun service CV pour le moment.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
