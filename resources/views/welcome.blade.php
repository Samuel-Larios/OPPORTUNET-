@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ";
$siteWhatsapp = $siteWhatsapp ?? '+2290166441840';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $whatsappHref = $whatsappBase . '?text=' . urlencode($siteWhatsappMessage ?? __('home.forms.whatsapp_default'));
    $localizedHomeUrl = \App\Support\Seo::localizedUrl(route('home'), app()->getLocale());
    $localizedArticlesUrl = \App\Support\Seo::localizedUrl(route('articles.index'), app()->getLocale());
    $localizedVersesUrl = \App\Support\Seo::localizedUrl(route('spiritual.verses.index'), app()->getLocale());
    $localizedThoughtsUrl = \App\Support\Seo::localizedUrl(route('spiritual.thoughts.index'), app()->getLocale());
    $localizedExhortationsUrl = \App\Support\Seo::localizedUrl(route('spiritual.exhortations.index'), app()->getLocale());
    $localizedDailyPrayersUrl = \App\Support\Seo::localizedUrl(route('spiritual.daily-prayers.index'), app()->getLocale());
    $localizedConversionUrl = \App\Support\Seo::localizedUrl(route('spiritual.conversion.index'), app()->getLocale());
    $isFrench = app()->getLocale() === 'fr';
    $supportWhatsappMessage = $isFrench
        ? 'Bonjour Opportunet Mondiale, je souhaite confirmer un soutien financier par Mobile Money.'
        : 'Hello Opportunet Mondiale, I would like to confirm a financial support through Mobile Money.';
    $supportWhatsappHref = $whatsappBase . '?text=' . urlencode($supportWhatsappMessage);
    $supportSection = [
        'label' => $isFrench ? 'Nous soutenir' : 'Support us',
        'title' => $isFrench ? 'Soutenez la mission par Mobile Money' : 'Support the mission through Mobile Money',
        'subtitle' => $isFrench
            ? "Envoyez tout soutien financier uniquement sur les num\u{00E9}ros officiels ci-dessous."
            : 'Please send all financial support only to the official numbers below.',
        'note' => $isFrench
            ? "Deux canaux sont valid\u{00E9}s pour les soutiens financiers: MTN et Moov."
            : 'Two channels are approved for financial support: MTN and Moov.',
        'helper' => $isFrench
            ? "Apr\u{00E8}s votre envoi, vous pouvez \u{00E9}crire \u{00E0} l'\u{00E9}quipe sur WhatsApp pour confirmer votre soutien."
            : 'After sending, you can contact the team on WhatsApp to confirm your support.',
        'cta' => $isFrench ? "Contacter l'\u{00E9}quipe" : 'Contact the team',
    ];
    $supportChannels = [
        [
            'operator' => 'MTN Mobile Money',
            'number' => '0167229575',
            'badge' => $isFrench ? 'Canal MTN' : 'MTN channel',
            'tone' => 'mtn',
        ],
        [
            'operator' => 'Moov Money',
            'number' => '0195757065',
            'badge' => $isFrench ? 'Canal Moov' : 'Moov channel',
            'tone' => 'moov',
        ],
    ];
    $welcomeVerses = collect($welcomeVerses ?? [])->take(3)->values();
    $welcomeThoughts = collect($welcomeThoughts ?? ($featuredThought ? [$featuredThought] : []))->take(3)->values();
    $welcomeExhortations = collect($welcomeExhortations ?? ($featuredExhortation ? [$featuredExhortation] : []))->take(3)->values();
    $welcomeDailyPrayers = collect($welcomeDailyPrayers ?? ($featuredDailyPrayer ? [$featuredDailyPrayer] : []))->take(3)->values();
    $spiritualSectionCopy = [
        'verse' => [
            'label' => $isFrench ? 'Verset du moment' : 'Verse of the moment',
            'title' => $isFrench ? "Une parole à méditer aujourd'hui" : 'A word to reflect on today',
            'subtitle' => $isFrench
                ? 'Prenez un moment pour lire, méditer et vous laisser encourager par les versets mis en avant sur la plateforme.'
                : 'Take time to read, reflect, and be encouraged by the verses highlighted on the platform.',
            'cta' => $isFrench ? 'Voir tous les versets' : 'See all verses',
        ],
        'thought' => [
            'label' => $isFrench ? 'Pensée du jour' : 'Thought of the day',
            'title' => $isFrench ? 'Une pensée pour éclairer la journée' : 'A thought to guide the day',
            'subtitle' => $isFrench
                ? 'Recevez une courte méditation biblique pour avancer avec clarté.'
                : 'Receive a short biblical meditation to move forward with clarity.',
            'cta' => $isFrench ? 'Voir les pensées du jour' : 'See thoughts of the day',
            'empty' => $isFrench ? 'La première pensée du jour apparaîtra ici après publication.' : 'The first thought of the day will appear here after publication.',
        ],
        'exhortation' => [
            'label' => $isFrench ? 'Exhortation' : 'Exhortation',
            'title' => $isFrench ? 'Des paroles pour tenir ferme' : 'Words to help you stand firm',
            'subtitle' => $isFrench
                ? "Retrouvez des exhortations bibliques pour fortifier la foi et l'espérance."
                : 'Find biblical exhortations that strengthen faith and hope.',
            'cta' => $isFrench ? 'Voir les exhortations' : 'See exhortations',
            'empty' => $isFrench ? 'La première exhortation apparaîtra ici après publication.' : 'The first exhortation will appear here after publication.',
        ],
        'daily_prayer' => [
            'label' => $isFrench ? 'Prière du jour' : 'Prayer of the day',
            'title' => $isFrench ? "Une prière pour aujourd'hui" : 'A prayer to carry today',
            'subtitle' => $isFrench
                ? 'Prenez un moment de recueillement avec des prières écrites pour la journée.'
                : 'Pause and pray through curated written prayers for the day.',
            'cta' => $isFrench ? 'Voir les prières du jour' : 'See daily prayers',
            'empty' => $isFrench ? 'La première prière du jour apparaîtra ici après publication.' : 'The first prayer of the day will appear here after publication.',
        ],
        'conversion' => [
            'label' => $isFrench ? 'Se convertir' : 'Convert',
            'title' => $isFrench ? 'Faire un pas vers Jésus-Christ' : 'Take a step toward Jesus Christ',
            'subtitle' => $isFrench
                ? "Un espace simple pour découvrir l'Évangile, prier et commencer une marche de foi."
                : 'A simple space to understand the Gospel, pray, and begin a life of faith.',
            'cta' => $isFrench ? 'Découvrir comment se convertir' : 'Discover how to convert',
        ],
    ];
    $approvedPrayerRequests = collect($approvedPrayerRequests ?? [])->values();
    $prayerRequestSlides = $approvedPrayerRequests->count() > 1
        ? $approvedPrayerRequests->concat($approvedPrayerRequests)
        : $approvedPrayerRequests;
    $testimonials = collect($testimonials ?? [])->values();
    $testimonialSlides = $testimonials->count() > 1
        ? $testimonials->concat($testimonials)
        : $testimonials;
    $prayerTestimonies = collect($prayerTestimonies ?? [])->take(4)->values();
    $seoDescription = __('home.meta.description');
    $seoKeywords = __('home.meta.keywords');
    $seoSchema = [
        \App\Support\Seo::schema('WebPage', [
            'name' => __('home.meta.title'),
            'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale()),
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
            'about' => [
                ['@type' => 'Thing', 'name' => 'Emploi au Benin'],
                ['@type' => 'Thing', 'name' => 'Opportunites internationales'],
                ['@type' => 'Thing', 'name' => 'Spiritualité chrétienne'],
                ['@type' => 'Thing', 'name' => 'Prière et témoignages'],
            ],
        ]),
        \App\Support\Seo::schema('ItemList', [
            'name' => __('home.sections.opportunities.title'),
            'itemListElement' => collect($latestOpportunities ?? [])
                ->take(3)
                ->values()
                ->map(fn ($opportunity, $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $opportunity->titre,
                    'url' => route('offers.show', $opportunity->slug),
                ])
                ->all(),
        ]),
    ];

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

@once
    <script>
        document.addEventListener('click', function (event) {
            const card = event.target.closest('[data-card-link]');

            if (!card || event.target.closest('a, button, input, textarea, select, label')) {
                return;
            }

            const target = card.getAttribute('data-card-link');
            if (target) {
                window.location.href = target;
            }
        });
    </script>
@endonce

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

    .prayer-verse-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .prayer-verse-grid .prayer-verse-card,
    .prayer-verse-grid .prayer-empty-card {
        grid-column: auto;
    }

    .prayer-verse-grid .prayer-empty-card {
        grid-column: 1 / -1;
    }

    .prayer-request-shell {
        margin-top: 10px;
    }

    @media (max-width: 1024px) {
        .prayer-verse-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .prayer-verse-grid {
            grid-template-columns: 1fr;
        }
    }

    .home-support {
        padding: 44px 0 18px;
    }

    .home-support-shell {
        position: relative;
        overflow: hidden;
        padding: 38px;
        border-radius: 36px;
        background:
            radial-gradient(circle at top left, rgba(40, 255, 216, 0.14), transparent 28%),
            radial-gradient(circle at 88% 14%, rgba(68, 98, 255, 0.18), transparent 24%),
            linear-gradient(135deg, #04111f 0%, #091a2f 48%, #071423 100%);
        border: 1px solid rgba(104, 222, 255, 0.18);
        box-shadow:
            0 32px 80px rgba(3, 12, 26, 0.42),
            inset 0 1px 0 rgba(180, 245, 255, 0.08);
    }

    .home-support-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(180deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 22px 22px;
        opacity: 0.22;
        pointer-events: none;
    }

    .home-support-grid {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
        gap: 28px;
        align-items: stretch;
    }

    .home-support-copy .section-label {
        background: rgba(84, 235, 255, 0.12);
        border-color: rgba(84, 235, 255, 0.2);
        color: #8df3ff;
    }

    .home-support-copy .section-label::before {
        background: #53f2ff;
    }

    .home-support-copy .section-title,
    .home-support-copy .section-sub {
        color: #f4fbff;
    }

    .home-support-copy .section-sub {
        max-width: 540px;
        color: rgba(224, 244, 255, 0.78);
    }

    .home-support-lead {
        margin-top: 22px;
        padding: 18px 20px;
        border-radius: 20px;
        background: rgba(11, 31, 52, 0.58);
        border: 1px solid rgba(92, 233, 255, 0.12);
        color: #9adff0;
        line-height: 1.75;
    }

    .home-support-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .home-support-note {
        margin-top: 18px;
        color: rgba(222, 243, 255, 0.72);
        line-height: 1.75;
        max-width: 540px;
    }

    .home-support-display {
        position: relative;
        display: grid;
        gap: 18px;
        padding: 24px;
        border-radius: 28px;
        background:
            linear-gradient(180deg, rgba(5, 13, 28, 0.94), rgba(9, 20, 39, 0.9));
        border: 1px solid rgba(95, 229, 255, 0.18);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.05),
            0 18px 45px rgba(0, 0, 0, 0.28);
    }

    .home-support-display::after {
        content: '';
        position: absolute;
        left: 18px;
        right: 18px;
        top: 18px;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(90, 245, 255, 0.52), transparent);
        box-shadow: 0 0 18px rgba(90, 245, 255, 0.44);
    }

    .home-support-display-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 8px;
    }

    .home-support-kicker,
    .home-support-signal {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .home-support-kicker {
        color: #7be7ff;
    }

    .home-support-signal {
        color: rgba(192, 240, 255, 0.7);
    }

    .home-support-lanes {
        display: grid;
        gap: 16px;
    }

    .home-support-lane {
        position: relative;
        overflow: hidden;
        padding: 22px;
        border-radius: 24px;
        border: 1px solid rgba(122, 224, 255, 0.14);
        background: linear-gradient(135deg, rgba(13, 30, 58, 0.94), rgba(7, 18, 34, 0.98));
    }

    .home-support-lane::before {
        content: '';
        position: absolute;
        inset: auto -12% 0 auto;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        opacity: 0.18;
        filter: blur(8px);
    }

    .home-support-lane.is-mtn::before {
        background: radial-gradient(circle, rgba(255, 214, 72, 0.95), transparent 66%);
    }

    .home-support-lane.is-moov::before {
        background: radial-gradient(circle, rgba(71, 118, 255, 0.95), transparent 66%);
    }

    .home-support-lane-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .home-support-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(92, 245, 255, 0.12);
        border: 1px solid rgba(92, 245, 255, 0.16);
        color: #9af5ff;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .home-support-operator {
        color: rgba(226, 246, 255, 0.76);
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .home-support-number {
        margin-top: 18px;
        color: #ffffff;
        font-size: clamp(1.9rem, 3vw, 2.65rem);
        font-weight: 700;
        letter-spacing: 0.12em;
        text-shadow: 0 0 18px rgba(101, 236, 255, 0.22);
    }

    .home-support-caption {
        margin: 10px 0 0;
        color: rgba(206, 232, 245, 0.74);
        line-height: 1.7;
    }

    .home-support-mini-note {
        margin: 0;
        color: rgba(208, 238, 248, 0.72);
        line-height: 1.72;
    }

    .home-conversion-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 14px;
        margin-top: 10px;
    }

    .home-conversion-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 52px;
        width: min(100%, 324px);
        padding: 14px 20px;
        border-radius: 18px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
    }

    .home-conversion-btn:hover {
        transform: translateY(-2px);
    }

    .home-conversion-btn-primary {
        background: linear-gradient(135deg, #0d6f7c 0%, #1399a8 100%);
        color: #f7feff;
        box-shadow: 0 18px 34px rgba(17, 109, 121, 0.22);
    }

    .home-conversion-btn-primary:hover {
        box-shadow: 0 22px 38px rgba(17, 109, 121, 0.28);
    }

    .home-conversion-btn-secondary {
        border: 1px solid rgba(32, 160, 104, 0.22);
        background:
            linear-gradient(135deg, rgba(233, 255, 245, 0.96), rgba(214, 250, 233, 0.96));
        color: #16794e;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .home-conversion-btn-secondary:hover {
        border-color: rgba(32, 160, 104, 0.34);
        box-shadow: 0 16px 30px rgba(22, 121, 78, 0.14);
    }

    .home-conversion-btn-icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 20px;
    }

    .home-conversion-btn-icon svg {
        width: 100%;
        height: 100%;
        display: block;
        fill: currentColor;
    }

    @media (max-width: 1024px) {
        .home-support-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .home-support-shell {
            padding: 24px;
            border-radius: 28px;
        }

        .home-support-display-head,
        .home-support-lane-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .home-conversion-actions {
            flex-direction: column;
        }

        .home-conversion-btn {
            width: 100%;
        }

        .home-support-number {
            font-size: 1.8rem;
            letter-spacing: 0.08em;
            word-break: break-word;
        }
    }
</style>

<x-layouts.app
    :title="__('home.meta.title')"
    :description="$seoDescription"
    :keywords="$seoKeywords"
    :canonical="\App\Support\Seo::localizedUrl(route('home'), app()->getLocale())"
    :schema-data="$seoSchema"
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
                            <x-share-buttons
                                :url="route('offers.show', $opportunity->slug)"
                                :title="$opportunity->titre"
                                :text="$opportunity->description"
                                variant="compact"
                            />
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
                    <div class="contact-form-actions" style="margin-top: 18px;">
                        <a href="{{ $localizedArticlesUrl }}" class="ghost-submit">
                            {{ __('home.sections.articles.all_cta') }}
                        </a>
                    </div>
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
                                <x-share-buttons
                                    :url="route('articles.show', $article->slug)"
                                    :title="$article->titre"
                                    :text="$article->extrait ?: \Illuminate\Support\Str::limit(strip_tags($article->contenu), 170)"
                                    variant="compact"
                                />
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
                    <div class="home-prayer-stream reveal" style="margin-bottom: 18px;">
                        <div class="home-prayer-stream-head">
                            <div>
                                <span class="prayer-card-label">{{ __('home.sections.testimonials.label') }}</span>
                                <h3>{{ __('home.sections.testimonials.title') }}</h3>
                                <p>{{ __('home.sections.testimonials.subtitle') }}</p>
                            </div>
                            <a href="{{ route('community.testimonials.index') }}" class="home-prayer-stream-cta">
                                {{ __('community.testimonials.cta') }}
                                <span>&gt;</span>
                            </a>
                        </div>
                    </div>

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
                                <x-honeypot />
                                <x-form-captcha />

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

        <section class="home-prayer" id="home-verse">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ $spiritualSectionCopy['verse']['label'] }}</span>
                    <h2 class="section-title">{{ $spiritualSectionCopy['verse']['title'] }}</h2>
                    <p class="section-sub">{{ $spiritualSectionCopy['verse']['subtitle'] }}</p>
                    <a href="{{ $localizedVersesUrl }}" class="home-prayer-stream-cta" style="margin-top: 10px;">
                        {{ $spiritualSectionCopy['verse']['cta'] }}
                        <span>&gt;</span>
                    </a>
                </div>

                <div class="prayer-verse-grid">
                    @forelse ($welcomeVerses as $index => $verse)
                        <article
                            class="prayer-card prayer-verse prayer-verse-card reveal reveal-delay-{{ min($index + 1, 3) }}"
                            data-card-link="{{ \App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale()) }}"
                            style="cursor: pointer;"
                        >
                            <span class="prayer-card-label">{{ __('home.sections.prayer.verse_label') }}</span>
                            <h3>{{ $verse->reference }}</h3>
                            <p>{{ $verse->texte }}</p>
                            <strong class="prayer-verse-version">{{ $verse->version }}</strong>
                            <a
                                href="{{ \App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale()) }}"
                                class="solid-submit"
                                style="width: fit-content;"
                            >
                                {{ $isFrench ? 'Voir les détails' : 'View details' }}
                            </a>
                            <x-share-buttons
                                :url="\App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale())"
                                :title="$verse->reference"
                                :text="$verse->texte . ' - ' . $verse->version"
                            />
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card reveal">
                            <h3>{{ __('home.sections.prayer.verse_empty_title') }}</h3>
                            <p>{{ __('home.sections.prayer.verse_empty_text') }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="home-prayer" id="home-thought">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ $spiritualSectionCopy['thought']['label'] }}</span>
                    <h2 class="section-title">{{ $spiritualSectionCopy['thought']['title'] }}</h2>
                    <p class="section-sub">{{ $spiritualSectionCopy['thought']['subtitle'] }}</p>
                </div>

                <div class="prayer-verse-grid">
                    @forelse ($welcomeThoughts as $index => $thought)
                        <article
                            class="prayer-card prayer-verse prayer-verse-card reveal reveal-delay-{{ min($index + 1, 3) }}"
                            data-card-link="{{ \App\Support\Seo::localizedUrl(route('spiritual.thoughts.show', $thought->slug), app()->getLocale()) }}"
                            style="cursor: pointer;"
                        >
                            @if ($thought->reference)
                                <span class="prayer-card-label">{{ $thought->reference }}</span>
                            @endif
                            <h3>{{ $thought->titre }}</h3>
                            <p>{{ $thought->extrait ?: \Illuminate\Support\Str::limit($thought->contenu, 220) }}</p>
                            @if ($thought->auteur)
                                <strong class="prayer-verse-version">{{ $thought->auteur }}</strong>
                            @endif
                            <a
                                href="{{ \App\Support\Seo::localizedUrl(route('spiritual.thoughts.show', $thought->slug), app()->getLocale()) }}"
                                class="solid-submit"
                                style="width: fit-content;"
                            >
                                {{ $isFrench ? 'Voir les détails' : 'View details' }}
                            </a>
                            <x-share-buttons
                                :url="\App\Support\Seo::localizedUrl(route('spiritual.thoughts.show', $thought->slug), app()->getLocale())"
                                :title="$thought->titre"
                                :text="$thought->extrait ?: \Illuminate\Support\Str::limit($thought->contenu, 170)"
                            />
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card reveal">
                            <h3>{{ $spiritualSectionCopy['thought']['title'] }}</h3>
                            <p>{{ $spiritualSectionCopy['thought']['empty'] }}</p>
                        </article>
                    @endforelse
                </div>

                <div style="margin-top: 18px;">
                    <a href="{{ $localizedThoughtsUrl }}" class="solid-submit">{{ $spiritualSectionCopy['thought']['cta'] }}</a>
                </div>
            </div>
        </section>

        <section class="home-prayer" id="home-exhortation">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ $spiritualSectionCopy['exhortation']['label'] }}</span>
                    <h2 class="section-title">{{ $spiritualSectionCopy['exhortation']['title'] }}</h2>
                    <p class="section-sub">{{ $spiritualSectionCopy['exhortation']['subtitle'] }}</p>
                </div>

                <div class="prayer-verse-grid">
                    @forelse ($welcomeExhortations as $index => $exhortation)
                        <article
                            class="prayer-card prayer-verse prayer-verse-card reveal reveal-delay-{{ min($index + 1, 3) }}"
                            data-card-link="{{ \App\Support\Seo::localizedUrl(route('spiritual.exhortations.show', $exhortation->slug), app()->getLocale()) }}"
                            style="cursor: pointer;"
                        >
                            @if ($exhortation->reference)
                                <span class="prayer-card-label">{{ $exhortation->reference }}</span>
                            @endif
                            <h3>{{ $exhortation->titre }}</h3>
                            <p>{{ $exhortation->extrait ?: \Illuminate\Support\Str::limit($exhortation->contenu, 220) }}</p>
                            @if ($exhortation->auteur)
                                <strong class="prayer-verse-version">{{ $exhortation->auteur }}</strong>
                            @endif
                            <a
                                href="{{ \App\Support\Seo::localizedUrl(route('spiritual.exhortations.show', $exhortation->slug), app()->getLocale()) }}"
                                class="solid-submit"
                                style="width: fit-content;"
                            >
                                {{ $isFrench ? 'Voir les détails' : 'View details' }}
                            </a>
                            <x-share-buttons
                                :url="\App\Support\Seo::localizedUrl(route('spiritual.exhortations.show', $exhortation->slug), app()->getLocale())"
                                :title="$exhortation->titre"
                                :text="$exhortation->extrait ?: \Illuminate\Support\Str::limit($exhortation->contenu, 170)"
                            />
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card reveal">
                            <h3>{{ $spiritualSectionCopy['exhortation']['title'] }}</h3>
                            <p>{{ $spiritualSectionCopy['exhortation']['empty'] }}</p>
                        </article>
                    @endforelse
                </div>

                <div style="margin-top: 18px;">
                    <a href="{{ $localizedExhortationsUrl }}" class="solid-submit">{{ $spiritualSectionCopy['exhortation']['cta'] }}</a>
                </div>
            </div>
        </section>

        <section class="home-prayer" id="home-daily-prayer">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ $spiritualSectionCopy['daily_prayer']['label'] }}</span>
                    <h2 class="section-title">{{ $spiritualSectionCopy['daily_prayer']['title'] }}</h2>
                    <p class="section-sub">{{ $spiritualSectionCopy['daily_prayer']['subtitle'] }}</p>
                </div>

                <div class="prayer-verse-grid">
                    @forelse ($welcomeDailyPrayers as $index => $dailyPrayer)
                        <article class="prayer-card prayer-verse prayer-verse-card reveal reveal-delay-{{ min($index + 1, 3) }}">
                            @if ($dailyPrayer->reference)
                                <span class="prayer-card-label">{{ $dailyPrayer->reference }}</span>
                            @endif
                            <h3>{{ $dailyPrayer->titre }}</h3>
                            <p>{{ $dailyPrayer->extrait ?: \Illuminate\Support\Str::limit($dailyPrayer->contenu, 220) }}</p>
                            @if ($dailyPrayer->auteur)
                                <strong class="prayer-verse-version">{{ $dailyPrayer->auteur }}</strong>
                            @endif
                            <x-share-buttons
                                :url="$localizedDailyPrayersUrl"
                                :title="$dailyPrayer->titre"
                                :text="$dailyPrayer->extrait ?: \Illuminate\Support\Str::limit($dailyPrayer->contenu, 170)"
                            />
                        </article>
                    @empty
                        <article class="empty-card prayer-empty-card reveal">
                            <h3>{{ $spiritualSectionCopy['daily_prayer']['title'] }}</h3>
                            <p>{{ $spiritualSectionCopy['daily_prayer']['empty'] }}</p>
                        </article>
                    @endforelse
                </div>

                <div style="margin-top: 18px;">
                    <a href="{{ $localizedDailyPrayersUrl }}" class="solid-submit">{{ $spiritualSectionCopy['daily_prayer']['cta'] }}</a>
                </div>
            </div>
        </section>

        <section class="home-contact" id="home-conversion">
            <div class="container">
                <div class="contact-shell reveal">
                    <div class="contact-copy">
                        <span class="section-label">{{ $spiritualSectionCopy['conversion']['label'] }}</span>
                        <h2 class="section-title">{{ $spiritualSectionCopy['conversion']['title'] }}</h2>
                        <p class="section-sub">{{ $spiritualSectionCopy['conversion']['subtitle'] }}</p>

                        <div class="contact-cards">
                            <article class="contact-info-card">
                                <span>{{ $isFrench ? 'Foi' : 'Faith' }}</span>
                                <strong>{{ $isFrench ? 'Comprendre le message du salut en Jésus-Christ.' : 'Understand the message of salvation in Jesus Christ.' }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ $isFrench ? 'Prière' : 'Prayer' }}</span>
                                <strong>{{ $isFrench ? 'Recevoir une prière simple pour commencer votre marche avec Dieu.' : 'Receive a simple prayer to begin your walk with God.' }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ $isFrench ? 'Accompagnement' : 'Guidance' }}</span>
                                <strong>{{ $isFrench ? 'Entrer en contact avec une equipe qui peut vous orienter.' : 'Connect with a team that can guide you.' }}</strong>
                            </article>
                        </div>
                    </div>

                    <div class="contact-form-wrap">
                        <div class="contact-form-card" style="display: grid; gap: 18px;">
                            <p style="margin: 0; color: #35555d; line-height: 1.85;">
                                {{ $isFrench ? 'Vous y trouverez une explication simple de la conversion, une prière guidée et des étapes concrètes pour aller plus loin.' : 'You can discover a simple explanation of conversion, a guided prayer, and practical next steps on the dedicated page.' }}
                            </p>

                            <div class="home-conversion-actions">
                                <a href="{{ $localizedConversionUrl }}" class="home-conversion-btn home-conversion-btn-primary">
                                    <span class="home-conversion-btn-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24"><path d="M12 2 4 6v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6zm3.5 9.2-4.2 4.2a1 1 0 0 1-1.4 0l-2.4-2.4 1.4-1.4 1.7 1.7 3.5-3.5z"/></svg>
                                    </span>
                                    <span>{{ $spiritualSectionCopy['conversion']['cta'] }}</span>
                                </a>
                                <a href="{{ $whatsappHref }}" class="home-conversion-btn home-conversion-btn-secondary" target="_blank" rel="noopener">
                                    <span class="home-conversion-btn-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24"><path d="M20 3.9A11 11 0 0 0 2.9 17.1L1.5 22.5l5.5-1.4A11 11 0 1 0 20 3.9zm-8 16.2c-1.8 0-3.6-.5-5.1-1.5l-.4-.2-3.2.8.8-3.1-.2-.4A8.4 8.4 0 1 1 12 20.1zm4.6-6.3c-.3-.2-1.6-.8-1.9-.9-.3-.1-.5-.2-.7.2l-.5.7c-.2.2-.3.3-.6.1-.3-.2-1.1-.4-2.1-1.4-.8-.7-1.4-1.7-1.6-2-.2-.3 0-.4.1-.6l.4-.5.3-.5c.1-.2.1-.4 0-.6l-.7-1.8c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.6.1-.9.4-.3.3-1.1 1-1.1 2.5 0 1.5 1.1 2.9 1.3 3.1.2.2 2.2 3.3 5.3 4.7.7.3 1.3.5 1.8.6.7.2 1.4.2 1.9.1.6-.1 1.6-.7 1.9-1.3.2-.6.2-1.2.2-1.3 0-.1-.2-.2-.5-.4z"/></svg>
                                    </span>
                                    <span>{{ $isFrench ? 'Échanger sur WhatsApp' : 'Chat on WhatsApp' }}</span>
                                </a>
                            </div>
                        </div>
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
                            <a href="{{ route('community.prayers.index') }}" class="home-prayer-stream-cta">
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
            </div>
        </section>

        <section class="home-contact home-prayer-request" id="prayer-request-form">
            <div class="container">
                <div class="contact-shell prayer-request-shell reveal">
                    <div class="contact-copy">
                        <span class="section-label">{{ __('home.sections.prayer_request.label') }}</span>
                        <h2 class="section-title">{{ __('home.sections.prayer_request.title') }}</h2>
                        <p class="section-sub">{{ __('home.sections.prayer_request.subtitle') }}</p>

                        <div class="contact-cards">
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.prayer_request.card_one_label') }}</span>
                                <strong>{{ __('home.sections.prayer_request.card_one_text') }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.prayer_request.card_two_label') }}</span>
                                <strong>{{ __('home.sections.prayer_request.card_two_text') }}</strong>
                            </article>
                            <article class="contact-info-card">
                                <span>{{ __('home.sections.prayer_request.card_three_label') }}</span>
                                <strong>{{ __('home.sections.prayer_request.card_three_text') }}</strong>
                            </article>
                        </div>
                    </div>

                    <div class="contact-form-wrap">
                        <form method="POST" action="{{ route('prayer.store') }}" class="contact-form-card">
                            @csrf
                            <x-honeypot />
                            <x-form-captcha />
                            <input type="hidden" name="redirect_to" value="{{ $localizedHomeUrl . '#prayer-request-form' }}" />
                            <div class="field-row">
                                <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="{{ __('home.forms.prayer.prenom') }}" />
                                <input type="text" name="pays" value="{{ old('pays') }}" placeholder="{{ __('home.forms.prayer.pays') }}" />
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('home.forms.prayer.email') }}" />
                            <textarea name="sujet" rows="6" placeholder="{{ __('home.forms.prayer.sujet') }}">{{ old('sujet') }}</textarea>
                            <label class="checkbox-line">
                                <input type="checkbox" name="anonyme" value="1" @checked(old('anonyme')) />
                                <span>{{ __('home.forms.prayer.anonyme') }}</span>
                            </label>
                            <div class="contact-form-actions">
                                <button type="submit" class="solid-submit">{{ __('home.forms.prayer.submit') }}</button>
                                <a href="{{ route('community.prayers.index') }}" class="ghost-submit">{{ __('home.sections.prayer.requests_cta') }}</a>
                            </div>
                        </form>
                    </div>
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
                            <x-honeypot />
                            <x-form-captcha />
                            <input type="hidden" name="redirect_to" value="{{ $localizedHomeUrl . '#home-contact' }}" />
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

        <section class="home-support" id="home-support">
            <div class="container">
                <div class="home-support-shell reveal">
                    <div class="home-support-grid">
                        <div class="home-support-copy">
                            <span class="section-label">{{ $supportSection['label'] }}</span>
                            <h2 class="section-title">{{ $supportSection['title'] }}</h2>
                            <p class="section-sub">{{ $supportSection['subtitle'] }}</p>

                            <p class="home-support-lead">{{ $supportSection['note'] }}</p>

                            <div class="home-support-actions">
                                <a href="{{ $supportWhatsappHref }}" class="solid-submit" target="_blank" rel="noopener">
                                    {{ $supportSection['cta'] }}
                                </a>
                            </div>

                            <p class="home-support-note">{{ $supportSection['helper'] }}</p>
                        </div>

                        <div class="home-support-display reveal-delay-1">
                            <div class="home-support-display-head">
                                <span class="home-support-kicker">{{ $isFrench ? 'Soutien financier' : 'Financial support' }}</span>
                                <span class="home-support-signal">{{ $isFrench ? "Canaux valid\u{00E9}s" : 'Approved channels' }}</span>
                            </div>

                            <div class="home-support-lanes">
                                @foreach ($supportChannels as $channel)
                                    <article class="home-support-lane is-{{ $channel['tone'] }}">
                                        <div class="home-support-lane-top">
                                            <span class="home-support-chip">{{ $channel['badge'] }}</span>
                                            <strong class="home-support-operator">{{ $channel['operator'] }}</strong>
                                        </div>
                                        <div class="home-support-number">{{ $channel['number'] }}</div>
                                        <p class="home-support-caption">
                                            {{ $isFrench ? "Envoyez votre soutien sur ce num\u{00E9}ro Mobile Money." : 'Send your support to this Mobile Money number.' }}
                                        </p>
                                    </article>
                                @endforeach
                            </div>

                            <p class="home-support-mini-note">
                                {{ $isFrench ? "Utilisez uniquement ces deux num\u{00E9}ros pour tout soutien financier affich\u{00E9} sur cette page." : 'Use only these two numbers for any financial support shown on this page.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
