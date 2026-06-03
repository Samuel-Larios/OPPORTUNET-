@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+229XXXXXXXXX';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $whatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappHref = $whatsappBase . '?text=' . urlencode($whatsappMessage);
    $location = trim(($opportunity->lieu ? $opportunity->lieu . ', ' : '') . ($opportunity->pays ?: ''));
    $uploadTooLargeMessage = app()->getLocale() === 'fr'
        ? 'Les fichiers envoyes sont trop volumineux. Gardez chaque fichier sous 5 Mo et le total de la candidature sous 64 Mo.'
        : 'The uploaded files are too large. Keep each file under 5 MB and the full application under 64 MB.';
    $uploadLimitsHint = app()->getLocale() === 'fr'
        ? 'Chaque fichier doit rester sous 5 Mo. Pour cette candidature, gardez le total des fichiers sous 64 Mo.'
        : 'Each file must stay under 5 MB. For this application, keep the total upload under 64 MB.';
@endphp

<x-layouts.app
    :title="$opportunity->titre"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="offers-detail-page">
        <section class="offers-detail-hero">
            <div class="container">
                <div class="offers-detail-hero-shell reveal">
                    <div class="offers-detail-copy">
                        <a href="{{ route('offers.index') }}" class="offers-detail-back">{{ __('offers.detail.back') }}</a>
                        <div class="offers-detail-badges">
                            <span class="opportunity-type">{{ __('home.opportunity_types.' . $opportunity->type) }}</span>
                            @if ($opportunity->urgent)
                                <span class="opportunity-urgent">{{ __('offers.badges.urgent') }}</span>
                            @endif
                            @if ($opportunity->teletravail)
                                <span class="offer-remote-badge">{{ __('offers.badges.remote') }}</span>
                            @endif
                        </div>
                        <h1 class="section-title">{{ $opportunity->titre }}</h1>
                        <p class="section-sub">{{ $opportunity->description }}</p>

                        <div class="offers-detail-meta">
                            <span>{{ $opportunity->organisation ?: __('offers.card.organization_fallback') }}</span>
                            <span>{{ $location !== '' ? $location : __('offers.card.location_fallback') }}</span>
                            @if ($opportunity->contrat)
                                <span>{{ __('offers.contracts.' . $opportunity->contrat) }}</span>
                            @endif
                            @if ($opportunity->date_publication)
                                <span>{{ __('offers.card.published') }} {{ $opportunity->date_publication->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                            @endif
                            @if ($opportunity->date_expiration)
                                <span>{{ __('offers.card.deadline') }} {{ $opportunity->date_expiration->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>
                    </div>

                    <aside class="offers-detail-side">
                        <article class="offers-detail-action-card">
                            <span>{{ __('offers.detail.action_label') }}</span>
                            <strong>{{ __('offers.detail.action_title') }}</strong>
                            <p>{{ __('offers.detail.action_text') }}</p>

                            <div class="offers-detail-actions">
                                <a href="{{ route('offers.apply.entry', $opportunity->slug) }}" class="solid-submit">{{ __('offers.application.apply_now') }}</a>

                                @if ($opportunity->lien_candidature)
                                    <a href="{{ $opportunity->lien_candidature }}" class="ghost-submit" target="_blank" rel="noopener">{{ __('offers.detail.external_link') }}</a>
                                @else
                                    <a href="{{ $whatsappHref }}" class="ghost-submit" target="_blank" rel="noopener">{{ __('offers.card.ask_more') }}</a>
                                @endif
                            </div>
                        </article>

                        <article class="offers-detail-stat-card">
                            <span>{{ __('offers.detail.views') }}</span>
                            <strong>{{ number_format((int) $opportunity->vues, 0, ',', ' ') }}</strong>
                        </article>
                    </aside>
                </div>
            </div>
        </section>

        <section class="offers-detail-content">
            <div class="container">
                <div class="offers-detail-layout">
                    <article class="offers-detail-main reveal">
                        <div class="offers-detail-section">
                            <h2>{{ __('offers.detail.sections.description') }}</h2>
                            <p>{{ $opportunity->description }}</p>
                        </div>

                        @if ($opportunity->profil_recherche)
                            <div class="offers-detail-section">
                                <h2>{{ __('offers.detail.sections.profile') }}</h2>
                                <p>{{ $opportunity->profil_recherche }}</p>
                            </div>
                        @endif

                        @if ($opportunity->avantages)
                            <div class="offers-detail-section">
                                <h2>{{ __('offers.detail.sections.benefits') }}</h2>
                                <p>{{ $opportunity->avantages }}</p>
                            </div>
                        @endif

                        <div class="offers-detail-section" id="application-form">
                            <h2>{{ __('offers.application.title') }}</h2>
                            <p>{{ __('offers.application.subtitle') }}</p>

                            @if (session('offer_application_success'))
                                <div class="home-alert success">{{ session('offer_application_success') }}</div>
                            @endif

                            @if (request('upload_error') === 'post_too_large')
                                <div class="home-alert error">
                                    <strong>{{ __('home.forms.errors_title') }}</strong>
                                    <ul>
                                        <li>{{ $uploadTooLargeMessage }}</li>
                                    </ul>
                                </div>
                            @endif

                            @auth
                                @if ($currentApplication)
                                    <div class="offers-application-status-card">
                                        <strong>{{ __('offers.application.already_applied_title') }}</strong>
                                        <p>{{ __('offers.application.already_applied_text') }}</p>
                                        <span class="offers-application-status-pill">{{ __('admin.applications.statuses.' . $currentApplication->statut) }}</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('offers.apply.store', $opportunity->slug) }}" enctype="multipart/form-data" class="contact-form-card offers-application-form">
                                        @csrf
                                        <div class="field-row">
                                            <input type="text" name="telephone" value="{{ old('telephone', auth()->user()->telephone) }}" placeholder="{{ __('offers.application.fields.phone') }}" />
                                            <input type="text" name="whatsapp" value="{{ old('whatsapp', auth()->user()->whatsapp) }}" placeholder="{{ __('offers.application.fields.whatsapp') }}" />
                                        </div>

                                        <input type="text" name="pays" value="{{ old('pays', auth()->user()->pays) }}" placeholder="{{ __('offers.application.fields.country') }}" />

                                        <div class="offers-application-upload-grid">
                                            <label class="offers-upload-field">
                                                <span>{{ __('offers.application.fields.letter') }}</span>
                                                <input type="file" name="lettre_motivation" accept=".pdf,.doc,.docx" required />
                                            </label>

                                            <label class="offers-upload-field">
                                                <span>{{ __('offers.application.fields.diplomas') }}</span>
                                                <input type="file" name="diplomes[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple required />
                                            </label>

                                            <label class="offers-upload-field">
                                                <span>{{ __('offers.application.fields.certificates') }}</span>
                                                <input type="file" name="attestations[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple required />
                                            </label>
                                        </div>

                                        <p class="panel-auth-alt">{{ $uploadLimitsHint }}</p>

                                        <textarea name="message" rows="5" placeholder="{{ __('offers.application.fields.message') }}">{{ old('message') }}</textarea>

                                        @if ($errors->any())
                                            <div class="home-alert error">
                                                <strong>{{ __('home.forms.errors_title') }}</strong>
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="offers-application-actions">
                                            <button type="submit" class="solid-submit">{{ __('offers.application.submit') }}</button>
                                            <a href="{{ route('offers.index') }}" class="ghost-submit">{{ __('offers.detail.all_offers') }}</a>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <div class="offers-application-login-card">
                                    <p>{{ __('offers.application.login_prompt') }}</p>
                                    <div class="offers-application-actions">
                                        <a href="{{ route('offers.apply.entry', $opportunity->slug) }}" class="solid-submit">{{ __('offers.application.login_to_apply') }}</a>
                                        <a href="{{ route('register.user') }}" class="ghost-submit">{{ __('offers.application.create_account') }}</a>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </article>

                    <aside class="offers-detail-aside reveal reveal-delay-1">
                        <article class="offers-detail-panel">
                            <h2>{{ __('offers.detail.summary') }}</h2>
                            <ul class="offers-detail-summary">
                                <li>
                                    <span>{{ __('offers.detail.labels.organization') }}</span>
                                    <strong>{{ $opportunity->organisation ?: __('offers.card.organization_fallback') }}</strong>
                                </li>
                                <li>
                                    <span>{{ __('offers.detail.labels.type') }}</span>
                                    <strong>{{ __('home.opportunity_types.' . $opportunity->type) }}</strong>
                                </li>
                                @if ($opportunity->contrat)
                                    <li>
                                        <span>{{ __('offers.detail.labels.contract') }}</span>
                                        <strong>{{ __('offers.contracts.' . $opportunity->contrat) }}</strong>
                                    </li>
                                @endif
                                <li>
                                    <span>{{ __('offers.detail.labels.location') }}</span>
                                    <strong>{{ $location !== '' ? $location : __('offers.card.location_fallback') }}</strong>
                                </li>
                                @if ($opportunity->date_expiration)
                                    <li>
                                        <span>{{ __('offers.detail.labels.deadline') }}</span>
                                        <strong>{{ $opportunity->date_expiration->locale(app()->getLocale())->translatedFormat('d M Y') }}</strong>
                                    </li>
                                @endif
                            </ul>
                        </article>
                    </aside>
                </div>
            </div>
        </section>

        @if ($relatedOpportunities->isNotEmpty())
            <section class="offers-detail-related">
                <div class="container">
                    <div class="home-section-head reveal">
                        <span class="section-label">{{ __('offers.detail.related_label') }}</span>
                        <h2 class="section-title">{{ __('offers.detail.related_title') }}</h2>
                    </div>

                    <div class="opportunity-grid">
                        @foreach ($relatedOpportunities as $index => $relatedOpportunity)
                            <article class="opportunity-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                                <div class="opportunity-card-top">
                                    <span class="opportunity-type">{{ __('home.opportunity_types.' . $relatedOpportunity->type) }}</span>
                                    @if ($relatedOpportunity->urgent)
                                        <span class="opportunity-urgent">{{ __('offers.badges.urgent') }}</span>
                                    @endif
                                </div>
                                <h3>{{ $relatedOpportunity->titre }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($relatedOpportunity->description, 145) }}</p>
                                <div class="opportunity-meta">
                                    <span>{{ $relatedOpportunity->organisation ?: __('offers.card.organization_fallback') }}</span>
                                    <span>{{ trim(($relatedOpportunity->lieu ? $relatedOpportunity->lieu . ', ' : '') . ($relatedOpportunity->pays ?: '')) ?: __('offers.card.location_fallback') }}</span>
                                </div>
                                <a href="{{ route('offers.show', $relatedOpportunity->slug) }}" class="opportunity-link">
                                    {{ __('offers.card.view_details') }}
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
</x-layouts.app>
