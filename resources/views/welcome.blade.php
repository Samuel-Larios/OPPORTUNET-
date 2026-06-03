@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+229XXXXXXXXX';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $whatsappHref = $whatsappBase . '?text=' . urlencode($siteWhatsappMessage ?? __('home.forms.whatsapp_default'));
    $welcomeVerses = collect($welcomeVerses ?? [])->take(3)->values();
    $approvedPrayerRequests = collect($approvedPrayerRequests ?? [])->values();
    $prayerRequestSlides = $approvedPrayerRequests->count() > 1
        ? $approvedPrayerRequests->concat($approvedPrayerRequests)
        : $approvedPrayerRequests;
    $testimonials = collect($testimonials ?? [])->values();
    $testimonialSlides = $testimonials->count() > 1
        ? $testimonials->concat($testimonials)
        : $testimonials;
    $prayerTestimonies = collect($prayerTestimonies ?? [])->take(4)->values();

    $serviceCards = [
        [
            'title' => $services->get('redaction_cv')?->titre ?? __('home.sections.services.cv_title'),
            'description' => $services->get('redaction_cv')?->description_courte ?? __('home.sections.services.cv_fallback'),
            'meta' => $services->get('redaction_cv')?->duree ?? __('home.sections.services.meta_cv'),
            'badge' => 'CV',
            'tone' => 'blue',
        ],
        [
            'title' => $services->get('coaching')?->titre ?? __('home.sections.services.coaching_title'),
            'description' => $services->get('coaching')?->description_courte ?? __('home.sections.services.coaching_fallback'),
            'meta' => $services->get('coaching')?->duree ?? __('home.sections.services.meta_coaching'),
            'badge' => 'CO',
            'tone' => 'gold',
        ],
        [
            'title' => $services->get('orientation')?->titre ?? __('home.sections.services.orientation_title'),
            'description' => $services->get('orientation')?->description_courte ?? __('home.sections.services.orientation_fallback'),
            'meta' => $services->get('orientation')?->duree ?? __('home.sections.services.meta_orientation'),
            'badge' => 'OR',
            'tone' => 'teal',
        ],
        [
            'title' => $featuredFormation?->titre ?? __('home.sections.services.training_title'),
            'description' => $featuredFormation?->description_courte ?? __('home.sections.services.training_fallback'),
            'meta' => $featuredFormation
                ? ($featuredFormation->gratuit ? __('home.sections.services.free_training') : number_format((float) $featuredFormation->prix, 0, ',', ' ') . ' ' . $featuredFormation->devise)
                : __('home.sections.services.meta_training'),
            'badge' => 'FO',
            'tone' => 'slate',
        ],
    ];
@endphp

<style>
    .home-prayer-stream {
        margin-bottom: 28px;
        padding: 22px 24px;
        border-radius: 30px;
        background:
            radial-gradient(circle at top left, rgba(34, 130, 161, 0.18), transparent 42%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(234, 244, 244, 0.96));
        border: 1px solid rgba(46, 132, 140, 0.12);
        box-shadow: 0 24px 70px rgba(16, 52, 61, 0.12);
        overflow: hidden;
    }

    .home-prayer-stream-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .home-prayer-stream-head h3 {
        margin: 8px 0 0;
        font-size: clamp(1.35rem, 2vw, 1.9rem);
        color: #0f242a;
    }

    .home-prayer-stream-head p {
        margin: 6px 0 0;
        max-width: 720px;
        color: #50707a;
    }

    .home-prayer-stream-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        color: #116d79;
        font-weight: 700;
        text-decoration: none;
    }

    .home-prayer-marquee {
        position: relative;
        overflow: hidden;
        mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    }

    .home-prayer-marquee-track {
        display: flex;
        gap: 18px;
        width: max-content;
        animation: homePrayerScroll 36s linear infinite;
    }

    .home-prayer-marquee:hover .home-prayer-marquee-track {
        animation-play-state: paused;
    }

    .home-prayer-marquee-track.is-static {
        width: 100%;
        animation: none;
        flex-wrap: wrap;
    }

    .home-prayer-pill {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: min(360px, 82vw);
        max-width: 420px;
        padding: 20px 22px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(46, 132, 140, 0.14);
        box-shadow: 0 16px 44px rgba(17, 85, 90, 0.1);
    }

    .home-prayer-pill-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .home-prayer-pill-author {
        font-size: 0.75rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #5f8a92;
    }

    .home-prayer-pill-count {
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(31, 126, 136, 0.12);
        color: #116d79;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .home-prayer-pill p {
        margin: 0;
        color: #17353c;
        font-size: 1rem;
        line-height: 1.8;
    }

    .home-prayer-pill-meta {
        margin-top: 14px;
        color: #648089;
        font-size: 0.92rem;
    }

    @keyframes homePrayerScroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(calc(-50% - 9px));
        }
    }

    @media (max-width: 900px) {
        .home-prayer-stream-head {
            align-items: start;
            flex-direction: column;
        }

        .home-prayer-marquee {
            mask-image: none;
            -webkit-mask-image: none;
        }

        .home-prayer-marquee-track {
            animation-duration: 42s;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-prayer-marquee-track {
            animation: none;
            flex-wrap: wrap;
        }
    }

    .home-testimonial-marquee {
        position: relative;
        overflow: hidden;
        margin-top: 20px;
        mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    }

    .home-testimonial-marquee-track {
        display: flex;
        gap: 18px;
        width: max-content;
        animation: homeTestimonialScroll 34s linear infinite;
    }

    .home-testimonial-marquee:hover .home-testimonial-marquee-track {
        animation-play-state: paused;
    }

    .home-testimonial-marquee-track.is-static {
        width: 100%;
        animation: none;
        flex-wrap: wrap;
    }

    .home-testimonial-marquee .testimonial-card-home {
        min-width: min(352px, 82vw);
        max-width: 390px;
        margin: 0;
    }

    @keyframes homeTestimonialScroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(calc(-50% - 9px));
        }
    }

    @media (max-width: 900px) {
        .home-testimonial-marquee {
            mask-image: none;
            -webkit-mask-image: none;
        }

        .home-testimonial-marquee-track {
            animation-duration: 40s;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-testimonial-marquee-track {
            animation: none;
            flex-wrap: wrap;
        }
    }
</style>

<x-layouts.app
    :title="__('home.meta.title')"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :header-verses="$headerVerses ?? collect()"
    :banner="$banner ?? null"
    :featured-verse="$featuredVerse ?? null"
>
    <main class="home-page">
        <section class="home-strip">
            <div class="container">
                @if (session('contact_success'))
                    <div class="home-alert success reveal">{{ session('contact_success') }}</div>
                @endif

                @if (session('prayer_success'))
                    <div class="home-alert reveal">{{ session('prayer_success') }}</div>
                @endif

                @if (session('testimonial_success'))
                    <div class="home-alert success reveal">{{ session('testimonial_success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="home-alert error reveal">
                        <strong>{{ __('home.forms.errors_title') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="home-strip-grid reveal">
                    <article class="home-strip-card">
                        <span>{{ __('home.sections.banner.metric_1_label') }}</span>
                        <strong>{{ $services->count() + ($featuredFormation ? 1 : 0) }}</strong>
                    </article>
                    <article class="home-strip-card">
                        <span>{{ __('home.sections.banner.metric_2_label') }}</span>
                        <strong>{{ $latestOpportunities->count() }}</strong>
                    </article>
                    <article class="home-strip-card">
                        <span>{{ __('home.sections.banner.metric_3_label') }}</span>
                        <strong>{{ $prayerRequestCount }}</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-services" id="home-services">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('home.sections.services.label') }}</span>
                    <h2 class="section-title">{{ __('home.sections.services.title') }}</h2>
                    <p class="section-sub">{{ __('home.sections.services.subtitle') }}</p>
                </div>

                <div class="service-grid">
                    @foreach ($serviceCards as $index => $card)
                        <article class="service-card service-card-{{ $card['tone'] }} reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <div class="service-card-top">
                                <span class="service-badge">{{ $card['badge'] }}</span>
                                <span class="service-meta">{{ $card['meta'] }}</span>
                            </div>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="home-opportunities" id="home-opportunities">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('home.sections.opportunities.label') }}</span>
                    <h2 class="section-title">{{ __('home.sections.opportunities.title') }}</h2>
                    <p class="section-sub">{{ __('home.sections.opportunities.subtitle') }}</p>
                </div>

                <div class="opportunity-grid">
                    @forelse ($latestOpportunities as $index => $opportunity)
                        <article class="opportunity-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <div class="opportunity-card-top">
                                <span class="opportunity-type">{{ __('home.opportunity_types.' . $opportunity->type) }}</span>
                                @if ($opportunity->urgent)
                                    <span class="opportunity-urgent">{{ __('home.sections.opportunities.urgent') }}</span>
                                @endif
                            </div>
                            <h3>{{ $opportunity->titre }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($opportunity->description, 145) }}</p>
                            <div class="opportunity-meta">
                                <span>{{ $opportunity->organisation ?: __('home.sections.opportunities.organization_fallback') }}</span>
                                <span>{{ trim(($opportunity->lieu ? $opportunity->lieu . ', ' : '') . ($opportunity->pays ?: '')) ?: __('home.sections.opportunities.location_fallback') }}</span>
                                @if ($opportunity->date_expiration)
                                    <span>{{ __('home.sections.opportunities.deadline') }} {{ $opportunity->date_expiration->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('offers.show', $opportunity->slug) }}" class="opportunity-link">
                                {{ __('home.sections.opportunities.cta') }}
                            </a>
                        </article>
                    @empty
                        <article class="empty-card reveal">
                            <h3>{{ __('home.sections.opportunities.empty_title') }}</h3>
                            <p>{{ __('home.sections.opportunities.empty_text') }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="home-articles" id="home-articles">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('home.sections.articles.label') }}</span>
                    <h2 class="section-title">{{ __('home.sections.articles.title') }}</h2>
                    <p class="section-sub">{{ __('home.sections.articles.subtitle') }}</p>
                </div>

                <div class="articles-grid">
                    @forelse ($latestArticles as $index => $article)
                        @php
                            $accent = $article->category?->couleur ?: '#1A7A6E';
                            $imageUrl = $article->primaryImageUrl();
                            $imageAlt = $article->primaryImageAlt();
                        @endphp
                        <article class="article-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <div class="article-card-visual" style="--article-accent: {{ $accent }};">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}">
                                @else
                                    <div class="article-card-placeholder">
                                        <span>{{ $article->category?->nom ?: __('articles.card.default_badge') }}</span>
                                        <strong>{{ $article->titre }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="article-card-body">
                                <div class="article-card-top">
                                    <div class="article-badges">
                                        <span class="article-category-badge" style="--article-accent: {{ $accent }};">
                                            {{ $article->category?->nom ?: __('articles.card.default_badge') }}
                                        </span>
                                        @if ($article->en_vedette)
                                            <span class="article-featured-badge">{{ __('articles.badges.featured') }}</span>
                                        @endif
                                    </div>

                                    @if ($article->publie_le)
                                        <span class="article-date">{{ $article->publie_le->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                                    @endif
                                </div>

                                <h3>{{ $article->titre }}</h3>
                                <p>{{ $article->extrait ?: \Illuminate\Support\Str::limit(strip_tags($article->contenu), 150) }}</p>

                                <div class="article-meta-list">
                                    <span>{{ __('articles.card.reading_time') }} {{ $article->temps_lecture ?: __('articles.card.reading_time_fallback') }}</span>
                                </div>

                                <div class="article-card-actions">
                                    <a href="{{ route('articles.show', $article->slug) }}" class="opportunity-link">
                                        {{ __('home.sections.articles.cta') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="empty-card reveal">
                            <h3>{{ __('home.sections.articles.empty_title') }}</h3>
                            <p>{{ __('home.sections.articles.empty_text') }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="home-testimonials" id="home-testimonials">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('home.sections.testimonials.label') }}</span>
                    <h2 class="section-title">{{ __('home.sections.testimonials.title') }}</h2>
                    <p class="section-sub">{{ __('home.sections.testimonials.subtitle') }}</p>
                </div>

                @if ($testimonials->isNotEmpty())
                    <div class="home-testimonial-marquee reveal" aria-label="{{ __('home.sections.testimonials.title') }}">
                        <div class="home-testimonial-marquee-track{{ $testimonials->count() === 1 ? ' is-static' : '' }}">
                            @foreach ($testimonialSlides as $testimonial)
                                <article class="testimonial-card-home">
                                    <div class="testimonial-avatar">{{ strtoupper(substr($testimonial->prenom, 0, 1)) }}</div>
                                    <p>{{ $testimonial->contenu }}</p>
                                    <div class="testimonial-author">
                                        <strong>{{ $testimonial->prenom }}{{ $testimonial->nom ? ' ' . $testimonial->nom : '' }}</strong>
                                        <span>{{ trim(($testimonial->profession ?: '') . ($testimonial->pays ? ' - ' . $testimonial->pays : '')) }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="testimonial-grid">
                        <article class="empty-card reveal">
                            <h3>{{ __('home.sections.testimonials.empty_title') }}</h3>
                            <p>{{ __('home.sections.testimonials.empty_text') }}</p>
                        </article>
                    </div>
                @endif

                <div class="contact-shell reveal" id="testimonial-form" style="margin-top: 32px;">
                    <div class="contact-copy">
                        <span class="section-label">{{ __('home.sections.testimonials.form_label') }}</span>
                        <h3 class="section-title">{{ __('home.sections.testimonials.form_title') }}</h3>
                        <p class="section-sub">{{ __('home.sections.testimonials.form_subtitle') }}</p>
                    </div>

                    <div class="contact-form-wrap">
                        @guest
                            <div class="contact-form-card cv-auth-card">
                                <strong>{{ __('home.forms.testimonial.auth_title') }}</strong>
                                <p>{{ __('home.forms.testimonial.auth_text') }}</p>
                                <div class="contact-form-actions">
                                    <a href="{{ route('login') }}" class="solid-submit">{{ __('admin.auth.login_submit') }}</a>
                                    <a href="{{ route('register.user') }}" class="ghost-submit">{{ __('admin.auth.create_simple_user_account') }}</a>
                                </div>
                            </div>
                        @else
                            <form method="POST" action="{{ route('testimonials.store') }}" class="contact-form-card">
                                @csrf

                                <div class="field-row">
                                    <input type="text" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}" placeholder="{{ __('home.forms.testimonial.prenom') }}" />
                                    <input type="text" name="nom" value="{{ old('nom', auth()->user()->nom) }}" placeholder="{{ __('home.forms.testimonial.nom') }}" />
                                </div>

                                <div class="field-row">
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" placeholder="{{ __('home.forms.testimonial.email') }}" readonly />
                                    <input type="text" name="pays" value="{{ old('pays', auth()->user()->pays) }}" placeholder="{{ __('home.forms.testimonial.pays') }}" />
                                </div>

                                <div class="field-row">
                                    <input type="text" name="profession" value="{{ old('profession', auth()->user()->profession) }}" placeholder="{{ __('home.forms.testimonial.profession') }}" />
                                    <select name="type">
                                        <option value="">{{ __('home.forms.testimonial.type_placeholder') }}</option>
                                        @foreach (__('home.forms.testimonial.types') as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field-row">
                                    <select name="note">
                                        <option value="">{{ __('home.forms.testimonial.note_placeholder') }}</option>
                                        @for ($rating = 5; $rating >= 1; $rating--)
                                            <option value="{{ $rating }}" @selected((string) old('note') === (string) $rating)>{{ $rating }}/5</option>
                                        @endfor
                                    </select>
                                </div>

                                <textarea name="contenu" rows="6" placeholder="{{ __('home.forms.testimonial.contenu') }}">{{ old('contenu') }}</textarea>

                                <div class="contact-form-actions">
                                    <button type="submit" class="solid-submit">{{ __('home.forms.testimonial.submit') }}</button>
                                    <a href="{{ auth()->user()?->canManageOffers() ? route('dashboard') : route('panel.user.testimonials') }}" class="ghost-submit">{{ __('home.forms.testimonial.space_cta') }}</a>
                                </div>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        <section class="home-prayer" id="home-prayer">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('home.sections.prayer.label') }}</span>
                    <h2 class="section-title">{{ __('home.sections.prayer.title') }}</h2>
                    <p class="section-sub">{{ __('home.sections.prayer.subtitle') }}</p>
                    <p class="prayer-intro-note">{{ __('home.sections.prayer.approved_only_note') }}</p>
                </div>

                @if ($approvedPrayerRequests->isNotEmpty())
                    <div class="home-prayer-stream reveal">
                        <div class="home-prayer-stream-head">
                            <div>
                                <span class="prayer-card-label">{{ __('home.sections.prayer.requests_label') }}</span>
                                <h3>{{ __('home.sections.prayer.requests_title') }}</h3>
                                <p>{{ __('home.sections.prayer.requests_subtitle') }}</p>
                            </div>
                            <a href="{{ route('contact.prayer.index') . '#prayer-wall' }}" class="home-prayer-stream-cta">
                                {{ __('home.sections.prayer.requests_cta') }}
                                <span>&gt;</span>
                            </a>
                        </div>

                        <div class="home-prayer-marquee" aria-label="{{ __('home.sections.prayer.requests_title') }}">
                            <div class="home-prayer-marquee-track{{ $approvedPrayerRequests->count() === 1 ? ' is-static' : '' }}">
                                @foreach ($prayerRequestSlides as $prayerRequest)
                                    <article class="home-prayer-pill">
                                        <div class="home-prayer-pill-top">
                                            <span class="home-prayer-pill-author">{{ $prayerRequest->publicAuthorName() }}</span>
                                            <span class="home-prayer-pill-count">{{ __('contact_prayer.wall.support_count', ['count' => $prayerRequest->priants]) }}</span>
                                        </div>
                                        <p>{{ $prayerRequest->sujet }}</p>
                                        <span class="home-prayer-pill-meta">{{ $prayerRequest->pays ?: __('home.sections.prayer.country_fallback') }}</span>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="prayer-grid">
                    @forelse ($welcomeVerses as $index => $verse)
                        <article class="prayer-card prayer-verse prayer-verse-card reveal reveal-delay-{{ min($index + 1, 3) }}">
                            <span class="prayer-card-label">{{ __('home.sections.prayer.verse_label') }}</span>
                            <h3>{{ $verse->reference }}</h3>
                            <p>{{ $verse->texte }}</p>
                            <strong class="prayer-verse-version">{{ $verse->version }}</strong>
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card reveal">
                            <h3>{{ __('home.sections.prayer.verse_empty_title') }}</h3>
                            <p>{{ __('home.sections.prayer.verse_empty_text') }}</p>
                        </article>
                    @endforelse

                    <article class="prayer-form-card reveal reveal-delay-4">
                        <span class="prayer-card-label">{{ __('home.sections.prayer.form_label') }}</span>
                        <h3>{{ __('home.sections.prayer.form_title') }}</h3>
                        <form method="POST" action="{{ route('prayer.store') }}" class="stack-form">
                            @csrf
                            <div class="field-row">
                                <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="{{ __('home.forms.prayer.prenom') }}" />
                                <input type="text" name="pays" value="{{ old('pays') }}" placeholder="{{ __('home.forms.prayer.pays') }}" />
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('home.forms.prayer.email') }}" />
                            <textarea name="sujet" rows="5" placeholder="{{ __('home.forms.prayer.sujet') }}">{{ old('sujet') }}</textarea>
                            <label class="checkbox-line">
                                <input type="checkbox" name="anonyme" value="1" @checked(old('anonyme')) />
                                <span>{{ __('home.forms.prayer.anonyme') }}</span>
                            </label>
                            <button type="submit" class="solid-submit">{{ __('home.forms.prayer.submit') }}</button>
                        </form>
                    </article>

                    @forelse ($prayerTestimonies as $index => $prayerTestimony)
                        <article class="prayer-card prayer-testimony-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <span class="prayer-card-label">{{ __('home.sections.prayer.testimony_label') }}</span>
                            <h3>{{ $prayerTestimony->publicAuthorName() }}</h3>
                            <p>{{ $prayerTestimony->sujet }}</p>
                            @if ($prayerTestimony->pays)
                                <span class="prayer-testimony-meta">{{ $prayerTestimony->pays }}</span>
                            @endif
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card prayer-empty-card-wide reveal">
                            <h3>{{ __('home.sections.prayer.testimonies_empty_title') }}</h3>
                            <p>{{ __('home.sections.prayer.testimonies_empty_text') }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="home-contact" id="home-contact">
            <div class="container">
                <div class="contact-shell reveal">
                    <div class="contact-copy">
                        <span class="section-label">{{ __('home.sections.contact.label') }}</span>
                        <h2 class="section-title">{{ __('home.sections.contact.title') }}</h2>
                        <p class="section-sub">{{ __('home.sections.contact.subtitle') }}</p>

                        <div class="contact-cards">
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.contact.email_label') }}</span>
                                <strong>{{ $siteEmail }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.contact.hours_label') }}</span>
                                <strong>{{ $siteHours }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.contact.address_label') }}</span>
                                <strong>{{ $siteAddress }}</strong>
                            </article>
                        </div>
                    </div>

                    <div class="contact-form-wrap">
                        <form method="POST" action="{{ route('contact.quick') }}" class="contact-form-card">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ route('home') . '#home-contact' }}" />
                            <div class="field-row">
                                <input type="text" name="prenom" value="{{ old('prenom', auth()->user()?->prenom) }}" placeholder="{{ __('home.forms.contact.prenom') }}" />
                                <input type="text" name="nom" value="{{ old('nom', auth()->user()?->nom) }}" placeholder="{{ __('home.forms.contact.nom') }}" />
                            </div>
                            <div class="field-row">
                                <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="{{ __('home.forms.contact.email') }}" />
                                <input type="text" name="telephone" value="{{ old('telephone', auth()->user()?->telephone) }}" placeholder="{{ __('home.forms.contact.telephone') }}" />
                            </div>
                            <div class="field-row">
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', auth()->user()?->whatsapp) }}" placeholder="{{ __('home.forms.contact.whatsapp_label') }}" />
                                <input type="text" name="pays" value="{{ old('pays', auth()->user()?->pays) }}" placeholder="{{ __('home.forms.contact.pays') }}" />
                            </div>
                            <div class="field-row">
                                <select name="sujet">
                                    <option value="">{{ __('home.forms.contact.sujet_placeholder') }}</option>
                                    <option value="information" @selected(old('sujet') === 'information')>{{ __('home.forms.contact.subjects.information') }}</option>
                                    <option value="service" @selected(old('sujet') === 'service')>{{ __('home.forms.contact.subjects.service') }}</option>
                                    <option value="formation" @selected(old('sujet') === 'formation')>{{ __('home.forms.contact.subjects.formation') }}</option>
                                    <option value="offre" @selected(old('sujet') === 'offre')>{{ __('home.forms.contact.subjects.offre') }}</option>
                                    <option value="partenariat" @selected(old('sujet') === 'partenariat')>{{ __('home.forms.contact.subjects.partenariat') }}</option>
                                    <option value="technique" @selected(old('sujet') === 'technique')>{{ __('home.forms.contact.subjects.technique') }}</option>
                                    <option value="autre" @selected(old('sujet') === 'autre')>{{ __('home.forms.contact.subjects.autre') }}</option>
                                </select>
                                <input type="text" name="sujet_personnalise" value="{{ old('sujet_personnalise') }}" placeholder="{{ __('home.forms.contact.sujet_personnalise') }}" />
                            </div>
                            <textarea name="message" rows="6" placeholder="{{ __('home.forms.contact.message') }}">{{ old('message') }}</textarea>
                            <div class="contact-form-actions">
                                <button type="submit" class="solid-submit">{{ __('home.forms.contact.submit') }}</button>
                                <a href="{{ $whatsappHref }}" class="ghost-submit" target="_blank" rel="noopener">{{ __('home.forms.contact.whatsapp') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
