@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+2290167229575';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $whatsappHref = $whatsappBase . '?text=' . urlencode($siteWhatsappMessage ?? __('home.forms.whatsapp_default'));
    $localizedContactPrayerUrl = \App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale());
    $contactRedirect = $localizedContactPrayerUrl . '#contact-form';
    $prayerRedirect = $localizedContactPrayerUrl . '#prayer-form';
    $showPrayerEncouragement = (bool) $prayerEncouragement;
    $prayerNoteTitle = $showPrayerEncouragement
        ? __('contact_prayer.prayer.approved_note_title')
        : __('contact_prayer.prayer.note_title');
    $prayerNoteText = $showPrayerEncouragement
        ? (app()->getLocale() === 'en'
            ? __('contact_prayer.prayer.approved_note_text')
            : $prayerEncouragement->sujet)
        : __('contact_prayer.prayer.note_text');
    $seoDescription = \App\Support\Seo::description(__('contact_prayer.page.subtitle'));
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('contact_prayer.page.label'), 'url' => \App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale())],
        ]),
        \App\Support\Seo::schema('WebPage', [
            'name' => __('contact_prayer.meta.title'),
            'url' => \App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale()),
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
            'about' => [
                ['@type' => 'Thing', 'name' => 'Prayer requests'],
                ['@type' => 'Thing', 'name' => 'Christian spirituality'],
                ['@type' => 'Thing', 'name' => 'Personal guidance'],
            ],
        ]),
    ];
@endphp

<x-layouts.app
    :title="__('contact_prayer.meta.title')"
    :description="$seoDescription"
    :canonical="\App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale())"
    :schema-data="$seoSchema"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="contact-prayer-page">
        <section class="contact-prayer-hero">
            <div class="container">
                <div class="contact-prayer-hero-shell reveal">
                    <div class="contact-prayer-hero-copy">
                        <span class="section-label">{{ __('contact_prayer.page.label') }}</span>
                        <h1 class="section-title">{{ __('contact_prayer.page.title') }}</h1>
                        <p class="section-sub">{{ __('contact_prayer.page.subtitle') }}</p>
                    </div>

                    <div class="contact-prayer-hero-cards">
                        <article class="contact-prayer-info-card">
                            <span>{{ __('contact_prayer.page.cards.email') }}</span>
                            <strong>{{ $siteEmail }}</strong>
                        </article>
                        <article class="contact-prayer-info-card">
                            <span>{{ __('contact_prayer.page.cards.hours') }}</span>
                            <strong>{{ $siteHours }}</strong>
                        </article>
                        <article class="contact-prayer-info-card accent">
                            <span>{{ __('contact_prayer.page.cards.address') }}</span>
                            <strong>{{ $siteAddress }}</strong>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-prayer-panels">
            <div class="container">
                @if (session('contact_success'))
                    <div class="home-alert success reveal">{{ session('contact_success') }}</div>
                @endif

                @if (session('prayer_success'))
                    <div class="home-alert reveal">{{ session('prayer_success') }}</div>
                @endif

                @if (session('prayer_support_success'))
                    <div class="home-alert success reveal">{{ session('prayer_support_success') }}</div>
                @endif

                @if (session('prayer_support_info'))
                    <div class="home-alert reveal">{{ session('prayer_support_info') }}</div>
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

                <div class="contact-prayer-grid">
                    <div class="contact-prayer-form-wrap reveal" id="contact-form">
                        <div class="home-section-head">
                            <span class="section-label">{{ __('contact_prayer.contact.label') }}</span>
                            <h2 class="section-title">{{ __('contact_prayer.contact.title') }}</h2>
                            <p class="section-sub">{{ __('contact_prayer.contact.subtitle') }}</p>
                        </div>

                        <form method="POST" action="{{ route('contact.quick') }}" class="contact-form-card">
                            @csrf
                            <x-honeypot />
                            <x-form-captcha />
                            <input type="hidden" name="redirect_to" value="{{ $contactRedirect }}" />

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
                                <a href="{{ $whatsappHref }}" class="ghost-submit" target="_blank" rel="noopener">{{ __('contact_prayer.contact.whatsapp_cta') }}</a>
                            </div>
                        </form>
                    </div>

                    <aside class="contact-prayer-side reveal reveal-delay-1">
                        <div class="whatsapp-focus-card">
                            <span class="section-label">{{ __('contact_prayer.whatsapp.label') }}</span>
                            <h2>{{ __('contact_prayer.whatsapp.title') }}</h2>
                            <p>{{ __('contact_prayer.whatsapp.subtitle') }}</p>
                            <div class="contact-prayer-side-points">
                                <span>{{ __('contact_prayer.whatsapp.points.one') }}</span>
                                <span>{{ __('contact_prayer.whatsapp.points.two') }}</span>
                                <span>{{ __('contact_prayer.whatsapp.points.three') }}</span>
                            </div>
                            <a href="{{ $whatsappHref }}" class="btn-primary-lg" target="_blank" rel="noopener">
                                {{ __('contact_prayer.whatsapp.button') }}
                                <span class="btn-arrow">-></span>
                            </a>
                        </div>

                        @if ($featuredVerse)
                            <div class="contact-prayer-verse-card">
                                <span>{{ $featuredVerse->reference }}</span>
                                <p>{{ $featuredVerse->texte }}</p>
                            </div>
                        @elseif ($prayerNoteText !== '')
                            <div class="contact-prayer-verse-card">
                                <span>{{ $prayerNoteTitle }}</span>
                                <p>{{ $prayerNoteText }}</p>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </section>

        <section class="contact-prayer-request" id="prayer-form">
            <div class="container">
                <div class="contact-prayer-request-shell reveal">
                    <div class="contact-prayer-request-copy">
                        <span class="section-label">{{ __('contact_prayer.prayer.label') }}</span>
                        <h2 class="section-title">{{ __('contact_prayer.prayer.title') }}</h2>
                        <p class="section-sub">{{ __('contact_prayer.prayer.subtitle') }}</p>

                        <div class="contact-prayer-request-note">
                            <strong>{{ $prayerNoteTitle }}</strong>
                            <p>{{ $prayerNoteText }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('prayer.store') }}" class="contact-form-card">
                        @csrf
                        <x-honeypot />
                        <x-form-captcha />
                        <input type="hidden" name="redirect_to" value="{{ $prayerRedirect }}" />

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
                            <a href="{{ $whatsappHref }}" class="ghost-submit" target="_blank" rel="noopener">{{ __('contact_prayer.prayer.whatsapp_cta') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="home-prayer" id="prayer-wall">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('contact_prayer.wall.label') }}</span>
                    <h2 class="section-title">{{ __('contact_prayer.wall.title') }}</h2>
                    <p class="section-sub">{{ __('contact_prayer.wall.subtitle') }}</p>
                    <p class="contact-prayer-wall-note">{{ __('contact_prayer.wall.status_note') }}</p>
                </div>

                <div class="contact-prayer-wall-stats reveal">
                    <article class="contact-prayer-wall-stat">
                        <span>{{ __('contact_prayer.wall.stats.approved_label') }}</span>
                        <strong>{{ $approvedPrayerTotal }}</strong>
                        <small>{{ __('contact_prayer.wall.stats.approved_text') }}</small>
                    </article>
                    <article class="contact-prayer-wall-stat">
                        <span>{{ __('contact_prayer.wall.stats.support_label') }}</span>
                        <strong>{{ $approvedPrayerSupportTotal }}</strong>
                        <small>{{ __('contact_prayer.wall.stats.support_text') }}</small>
                    </article>
                    <article class="contact-prayer-wall-stat accent">
                        <span>{{ __('contact_prayer.wall.stats.visible_label') }}</span>
                        <strong>{{ $prayerRequests->count() }}</strong>
                        <small>{{ __('contact_prayer.wall.stats.visible_text') }}</small>
                    </article>
                </div>

                <div class="contact-prayer-wall-grid">
                    @forelse ($prayerRequests as $prayer)
                        <article class="contact-prayer-wall-card reveal">
                            <div class="contact-prayer-wall-card-top">
                                <span class="prayer-card-label">{{ $prayer->publicAuthorName() }}</span>
                                <span class="contact-prayer-wall-country">{{ $prayer->pays ?: __('contact_prayer.wall.country_fallback') }}</span>
                            </div>

                            <p class="contact-prayer-wall-message">{{ $prayer->sujet }}</p>

                            <div class="contact-prayer-wall-actions">
                                <span class="ghost-submit contact-prayer-wall-count" style="pointer-events: none;">
                                    {{ __('contact_prayer.wall.support_count', ['count' => $prayer->priants]) }}
                                </span>
                                @if (in_array($prayer->id, $supportedPrayerIds, true))
                                    <span class="solid-submit contact-prayer-wall-button" style="pointer-events: none; opacity: 0.8;">
                                        {{ __('contact_prayer.wall.supported_cta') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('prayer.support', $prayer->id) }}">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="{{ $localizedContactPrayerUrl . '#prayer-wall' }}" />
                                        <button type="submit" class="solid-submit contact-prayer-wall-button">{{ __('contact_prayer.wall.support_cta') }}</button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @empty
                        <article class="contact-prayer-wall-empty reveal">
                            <span class="prayer-card-label">{{ __('contact_prayer.wall.label') }}</span>
                            <h3>{{ __('contact_prayer.wall.empty_title') }}</h3>
                            <p>{{ __('contact_prayer.wall.empty') }}</p>
                            <a href="#prayer-form" class="btn-primary-lg">
                                {{ __('contact_prayer.wall.empty_cta') }}
                                <span class="btn-arrow">-></span>
                            </a>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
