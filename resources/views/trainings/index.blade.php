@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+2290166441840';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $defaultWhatsappHref =
        $whatsappBase . '?text=' . urlencode($siteWhatsappMessage ?? __('home.forms.whatsapp_default'));
    $localizedTrainingsUrl = \App\Support\Seo::localizedUrl(route('trainings.index'), app()->getLocale());
    $seoTitle = $selectedTraining?->titre ?: __('trainings.meta.title');
    $seoDescription = \App\Support\Seo::description(
        $selectedTraining?->description_courte ?: __('trainings.page.subtitle'),
    );
    $seoCanonical = \App\Support\Seo::localizedUrl(
        route('trainings.index'),
        app()->getLocale(),
        $selectedTraining ? ['formation' => $selectedTraining->id] : [],
    );
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('trainings.page.label'), 'url' => $localizedTrainingsUrl],
        ]),
        \App\Support\Seo::schema($selectedTraining ? 'Course' : 'CollectionPage', [
            'name' => $seoTitle,
            'url' => $seoCanonical,
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
            'provider' => $selectedTraining
                ? [
                    '@type' => 'Organization',
                    'name' => $siteName,
                ]
                : null,
            'startDate' => $selectedTraining?->date_debut?->toDateString(),
            'endDate' => $selectedTraining?->date_fin?->toDateString(),
        ]),
    ];
@endphp

<x-layouts.app :title="$seoTitle" :description="$seoDescription" :canonical="$seoCanonical" :image="$selectedTraining?->publicCoverUrl()" :schema-data="$seoSchema"
    :site-name="$siteName" :site-slogan="$siteSlogan" :site-email="$siteEmail" :site-hours="$siteHours" :site-address="$siteAddress" :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage" :show-hero="false">
    <main class="trainings-page">
        <section class="trainings-hero">
            <div class="container">
                <div class="trainings-hero-shell reveal">
                    <div class="trainings-hero-copy">
                        <span class="section-label">{{ __('trainings.page.label') }}</span>
                        <h1 class="section-title">{{ __('trainings.page.title') }}</h1>
                        <p class="section-sub">{{ __('trainings.page.subtitle') }}</p>
                    </div>
                    <div class="trainings-hero-stat">
                        <span>{{ __('trainings.page.available') }}</span>
                        <strong>{{ $trainings->total() }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="trainings-list-section">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('trainings.list.label') }}</span>
                    <h2 class="section-title">{{ __('trainings.list.title') }}</h2>
                    <p class="section-sub">{{ __('trainings.list.subtitle') }}</p>
                </div>

                <div class="trainings-grid">
                    @forelse ($trainings as $index => $training)
                        @php
                            $trainingWhatsappHref =
                                $whatsappBase .
                                '?text=' .
                                urlencode(
                                    $training->whatsapp_message ?:
                                    __('trainings.whatsapp.default_message', ['formation' => $training->titre]),
                                );
                            $availabilityState = $training->availabilityState();
                        @endphp
                        <article class="training-card reveal reveal-delay-{{ min(($index % 4) + 1, 4) }}">
                            <div class="training-card-top">
                                <div class="offer-badges">
                                    <span class="opportunity-type">{{ __('trainings.modes.' . $training->mode) }}</span>
                                    @if ($training->gratuit)
                                        <span class="offer-remote-badge">{{ __('trainings.badges.free') }}</span>
                                    @endif
                                </div>
                                @if ($training->date_debut)
                                    <span class="offer-date">{{ __('trainings.card.starts') }}
                                        {{ $training->date_debut->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>

                            @if ($training->publicCoverUrl())
                                <img src="{{ $training->publicCoverUrl() }}" alt="{{ $training->titre }}"
                                    class="training-cover" />
                            @endif

                            <h3>{{ $training->titre }}</h3>
                            <p>{{ $training->description_courte }}</p>

                            <div class="offer-meta-list">
                                <span>{{ $training->gratuit ? __('trainings.card.free_price') : number_format((float) $training->prix, 0, ',', ' ') . ' ' . $training->devise }}</span>
                                <span>{{ $training->duree_heures ? __('trainings.card.duration_hours', ['hours' => $training->duree_heures]) : __('trainings.card.duration_tbd') }}</span>
                                <span>{{ $training->nb_seances ? __('trainings.card.sessions', ['count' => $training->nb_seances]) : __('trainings.card.sessions_tbd') }}</span>
                                <span>{{ $training->niveau ?: __('trainings.card.level_tbd') }}</span>
                            </div>

                            <div class="training-status-pill state-{{ $availabilityState }}">
                                {{ __('trainings.availability.' . $availabilityState) }}
                            </div>

                            <div class="training-card-actions">
                                <a href="{{ \App\Support\Seo::localizedUrl(route('trainings.index'), app()->getLocale(), ['formation' => $training->id]) }}#training-details"
                                    class="solid-submit">{{ __('trainings.list.details') }}</a>
                                <a href="{{ $trainingWhatsappHref }}" class="ghost-submit" target="_blank"
                                    rel="noopener">{{ __('trainings.card.whatsapp') }}</a>
                            </div>
                            <x-share-buttons :url="\App\Support\Seo::localizedUrl(route('trainings.index'), app()->getLocale(), [
                                'formation' => $training->id,
                            ]) . '#training-details'" :title="$training->titre" variant="compact" />
                        </article>
                    @empty
                        <article class="empty-card reveal">
                            <h3>{{ __('trainings.empty.title') }}</h3>
                            <p>{{ __('trainings.empty.text') }}</p>
                        </article>
                    @endforelse
                </div>

                @if ($trainings->hasPages())
                    <nav class="training-card-actions" aria-label="{{ __('trainings.pagination.label') }}" style="justify-content: center; margin-top: 2rem;">
                        @if ($trainings->onFirstPage())
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('trainings.pagination.previous') }}</span>
                        @else
                            <a href="{{ $trainings->previousPageUrl() }}" class="ghost-submit">{{ __('trainings.pagination.previous') }}</a>
                        @endif

                        <span class="section-sub" style="margin: 0;">
                            {{ __('trainings.pagination.summary', ['page' => $trainings->currentPage(), 'last' => $trainings->lastPage()]) }}
                        </span>

                        @if ($trainings->hasMorePages())
                            <a href="{{ $trainings->nextPageUrl() }}" class="ghost-submit">{{ __('trainings.pagination.next') }}</a>
                        @else
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('trainings.pagination.next') }}</span>
                        @endif
                    </nav>
                @endif
            </div>
        </section>

        <section class="training-details-section" id="training-details">
            <div class="container">
                @if ($selectedTraining)
                    @php
                        $selectedTrainingWhatsappHref =
                            $whatsappBase .
                            '?text=' .
                            urlencode(
                                $selectedTraining->whatsapp_message ?:
                                __('trainings.whatsapp.default_message', ['formation' => $selectedTraining->titre]),
                            );
                        $selectedAvailabilityState = $selectedTraining->availabilityState();
                    @endphp

                    <div class="training-details-shell reveal">
                        <div class="training-details-copy">
                            <span class="section-label">{{ __('trainings.details.label') }}</span>
                            <h2 class="section-title">{{ $selectedTraining->titre }}</h2>
                            <p class="section-sub">{{ $selectedTraining->description_courte }}</p>

                            <div class="training-status-pill state-{{ $selectedAvailabilityState }}">
                                {{ __('trainings.availability.' . $selectedAvailabilityState) }}
                            </div>

                            <div class="training-detail-grid">
                                <span><strong>{{ __('trainings.details.trainer') }}:</strong>
                                    {{ $selectedTraining->formateur?->fullName() ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.level') }}:</strong>
                                    {{ $selectedTraining->niveau ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.location') }}:</strong>
                                    {{ $selectedTraining->lieu ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.max_places') }}:</strong>
                                    {{ $selectedTraining->places_max ?? __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.remaining_places') }}:</strong>
                                    {{ $selectedTraining->places_restantes ?? __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.certificate') }}:</strong>
                                    {{ $selectedTraining->certificat ?: __('trainings.details.not_specified') }}</span>
                            </div>

                            <div class="training-detail-grid">
                                <span><strong>{{ __('trainings.details.date_start') }}:</strong>
                                    {{ $selectedTraining->date_debut?->locale(app()->getLocale())->translatedFormat('d M Y') ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.date_end') }}:</strong>
                                    {{ $selectedTraining->date_fin?->locale(app()->getLocale())->translatedFormat('d M Y') ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.time') }}:</strong>
                                    {{ $selectedTraining->heure_debut ?: __('trainings.details.not_specified') }}</span>
                                <span><strong>{{ __('trainings.details.timezone') }}:</strong>
                                    {{ $selectedTraining->fuseau_horaire ?: __('trainings.details.not_specified') }}</span>
                            </div>

                            <div class="training-detail-actions">
                                @if ($selectedTraining->lien_en_ligne)
                                    <a href="{{ $selectedTraining->lien_en_ligne }}" class="ghost-submit"
                                        target="_blank" rel="noopener">{{ __('trainings.details.open_link') }}</a>
                                @endif
                                <a href="{{ $selectedTrainingWhatsappHref }}" class="solid-submit" target="_blank"
                                    rel="noopener">{{ __('trainings.card.whatsapp') }}</a>
                            </div>
                            <x-share-buttons :url="\App\Support\Seo::localizedUrl(route('trainings.index'), app()->getLocale(), [
                                'formation' => $selectedTraining->id,
                            ]) . '#training-details'" :title="$selectedTraining->titre" />
                        </div>

                        <div class="training-details-panels">
                            @if ($selectedTraining->publicCoverUrl())
                                <img src="{{ $selectedTraining->publicCoverUrl() }}"
                                    alt="{{ $selectedTraining->titre }}" class="training-details-cover" />
                            @endif

                            @if ($selectedTraining->description_longue)
                                <article class="training-info-card">
                                    <h3>{{ __('trainings.details.selected') }}</h3>
                                    <p>{!! nl2br(e($selectedTraining->description_longue)) !!}</p>
                                </article>
                            @endif

                            @if ($selectedTraining->prerequis)
                                <article class="training-info-card">
                                    <h3>{{ __('trainings.details.prerequisites') }}</h3>
                                    <p>{!! nl2br(e($selectedTraining->prerequis)) !!}</p>
                                </article>
                            @endif

                            @if ($selectedTraining->objectifs)
                                <article class="training-info-card">
                                    <h3>{{ __('trainings.details.objectives') }}</h3>
                                    <p>{!! nl2br(e($selectedTraining->objectifs)) !!}</p>
                                </article>
                            @endif

                            @if ($selectedTraining->programme)
                                <article class="training-info-card">
                                    <h3>{{ __('trainings.details.program') }}</h3>
                                    <p>{!! nl2br(e($selectedTraining->programme)) !!}</p>
                                </article>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="training-registration-section" id="training-registration">
            <div class="container">
                @if (session('training_success'))
                    <div class="home-alert success reveal">{{ session('training_success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="home-alert error reveal">
                        <strong>{{ __('trainings.form.errors_title') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="training-registration-shell reveal">
                    <div class="training-registration-copy">
                        <span class="section-label">{{ __('trainings.form.label') }}</span>
                        <h2 class="section-title">{{ __('trainings.form.title') }}</h2>
                        <p class="section-sub">{{ __('trainings.form.subtitle') }}</p>

                        @if ($selectedTraining)
                            <div class="training-selected-card">
                                <span>{{ __('trainings.form.selected') }}</span>
                                <strong>{{ $selectedTraining->titre }}</strong>
                                <p>{{ $selectedTraining->description_courte }}</p>
                            </div>
                        @endif

                        @if ($currentTrainingRegistration)
                            <div class="training-selected-card training-selected-card-soft">
                                <span>{{ __('admin.user_trainings.detail_title') }}</span>
                                <strong>{{ $currentTrainingRegistration->statusLabel() }}</strong>
                                <p>{{ $currentTrainingRegistration->paymentStatusLabel() }}</p>
                                <a href="{{ route('panel.user.trainings') }}"
                                    class="ghost-submit">{{ __('trainings.form.space_cta') }}</a>
                            </div>
                        @endif
                    </div>

                    @guest
                        <div class="contact-form-card cv-auth-card">
                            <strong>{{ __('trainings.form.auth_title') }}</strong>
                            <p>{{ __('trainings.form.auth_text') }}</p>
                            <div class="contact-form-actions">
                                <a href="{{ route('login') }}"
                                    class="solid-submit">{{ __('admin.auth.login_submit') }}</a>
                                <a href="{{ route('register.user') }}"
                                    class="ghost-submit">{{ __('admin.auth.create_simple_user_account') }}</a>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('trainings.register') }}" class="contact-form-card">
                            @csrf
                            <x-honeypot />

                            <select name="formation_id">
                                <option value="">{{ __('trainings.form.fields.formation_placeholder') }}</option>
                                @foreach ($trainingOptions as $training)
                                    <option value="{{ $training->id }}" @selected(old('formation_id', $selectedTraining?->id) == $training->id)>
                                        {{ $training->titre }}</option>
                                @endforeach
                            </select>

                            <div class="field-row">
                                <input type="text" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}"
                                    placeholder="{{ __('trainings.form.fields.prenom') }}" />
                                <input type="text" name="nom" value="{{ old('nom', auth()->user()->nom) }}"
                                    placeholder="{{ __('trainings.form.fields.nom') }}" />
                            </div>

                            <div class="field-row">
                                <input type="email" name="email" value="{{ auth()->user()->email }}"
                                    placeholder="{{ __('trainings.form.fields.email') }}" readonly />
                                <input type="text" name="telephone"
                                    value="{{ old('telephone', auth()->user()->telephone) }}"
                                    placeholder="{{ __('trainings.form.fields.telephone') }}" />
                            </div>

                            <div class="field-row">
                                <input type="text" name="whatsapp"
                                    value="{{ old('whatsapp', auth()->user()->whatsapp) }}"
                                    placeholder="{{ __('trainings.form.fields.whatsapp') }}" />
                                <input type="text" name="pays" value="{{ old('pays', auth()->user()->pays) }}"
                                    placeholder="{{ __('trainings.form.fields.pays') }}" />
                            </div>

                            <div class="field-row">
                                <input type="text" name="profession"
                                    value="{{ old('profession', auth()->user()->profession) }}"
                                    placeholder="{{ __('trainings.form.fields.profession') }}" />
                                <input type="text" name="niveau_etude"
                                    value="{{ old('niveau_etude', auth()->user()->niveau_etude) }}"
                                    placeholder="{{ __('trainings.form.fields.niveau_etude') }}" />
                            </div>

                            <textarea name="motivation" rows="5" placeholder="{{ __('trainings.form.fields.motivation') }}">{{ old('motivation') }}</textarea>

                            <div class="contact-form-actions">
                                <button type="submit" class="solid-submit"
                                    @disabled(!$selectedTraining || !$selectedTraining->isRegistrationOpen() || $currentTrainingRegistration)>{{ __('trainings.form.submit') }}</button>
                                <a href="{{ route('panel.user.trainings') }}"
                                    class="ghost-submit">{{ __('trainings.form.space_cta') }}</a>
                            </div>
                        </form>
                    @endguest
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
