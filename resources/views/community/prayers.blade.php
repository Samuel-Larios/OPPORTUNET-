@php
    $seoDescription = \App\Support\Seo::description(__('community.prayers.hero.subtitle'));
    $seoCanonical = \App\Support\Seo::localizedUrl(route('community.prayers.index'), app()->getLocale(), request()->query());
    $localizedHomeUrl = \App\Support\Seo::localizedUrl(route('home'), app()->getLocale());
    $localizedContactPrayerUrl = \App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale());
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('community.prayers.hero.label'), 'url' => $seoCanonical],
        ]),
        \App\Support\Seo::schema('CollectionPage', [
            'name' => __('community.prayers.meta.title'),
            'url' => $seoCanonical,
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
        ]),
    ];
@endphp

<x-layouts.app
    :title="__('community.prayers.meta.title')"
    :description="$seoDescription"
    :canonical="$seoCanonical"
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
    @php
        $currentPageUrl = request()->fullUrl();
    @endphp

    <style>
        .community-page {
            padding: 64px 0 88px;
            background:
                radial-gradient(circle at top left, rgba(20, 184, 166, 0.16), transparent 30%),
                linear-gradient(180deg, #fbfffe 0%, #f2f7f6 100%);
        }

        .community-shell {
            display: grid;
            gap: 24px;
        }

        .community-hero {
            display: grid;
            gap: 18px;
            padding: 28px;
            border-radius: 30px;
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .community-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .community-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .community-stat,
        .community-card,
        .community-empty {
            padding: 22px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .community-stat strong {
            display: block;
            margin: 8px 0 6px;
            font-size: 2rem;
        }

        .community-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .community-card-top,
        .community-card-actions,
        .community-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .community-card-message {
            margin: 18px 0;
            color: #334155;
        }

        .community-pagination {
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .community-pagination-label {
            color: #475569;
        }

        @media (max-width: 900px) {
            .community-stats,
            .community-grid {
                grid-template-columns: 1fr;
            }

            .community-card-top,
            .community-card-actions {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <main class="community-page">
        <div class="container">
            <div class="community-shell">
                <section class="community-hero reveal">
                    <div>
                        <span class="section-label">{{ __('community.prayers.hero.label') }}</span>
                        <h1 class="section-title">{{ __('community.prayers.hero.title') }}</h1>
                        <p class="section-sub">{{ __('community.prayers.hero.subtitle') }}</p>
                        <p class="prayer-intro-note">{{ __('community.prayers.hero.note') }}</p>
                    </div>

                    <div class="community-actions">
                        <a href="{{ $localizedContactPrayerUrl . '#prayer-form' }}" class="btn-primary-lg">
                            {{ __('community.prayers.hero.primary_cta') }}
                            <span class="btn-arrow">-></span>
                        </a>
                        <a href="{{ $localizedHomeUrl . '#home-prayer' }}" class="ghost-submit">{{ __('community.prayers.hero.secondary_cta') }}</a>
                    </div>
                </section>

                <section class="community-stats reveal">
                    <article class="community-stat">
                        <span>{{ __('community.prayers.stats.approved_label') }}</span>
                        <strong>{{ $approvedPrayerTotal }}</strong>
                        <small>{{ __('community.prayers.stats.approved_text') }}</small>
                    </article>
                    <article class="community-stat">
                        <span>{{ __('community.prayers.stats.support_label') }}</span>
                        <strong>{{ $approvedPrayerSupportTotal }}</strong>
                        <small>{{ __('community.prayers.stats.support_text') }}</small>
                    </article>
                    <article class="community-stat">
                        <span>{{ __('community.prayers.stats.visible_label') }}</span>
                        <strong>{{ $prayerRequests->count() }}</strong>
                        <small>{{ __('community.prayers.stats.visible_text') }}</small>
                    </article>
                </section>

                <section class="community-grid">
                    @forelse ($prayerRequests as $prayer)
                        <article class="community-card reveal">
                            <div class="community-card-top">
                                <span class="prayer-card-label">{{ $prayer->publicAuthorName() }}</span>
                                <span>{{ $prayer->pays ?: __('contact_prayer.wall.country_fallback') }}</span>
                            </div>

                            <p class="community-card-message">{{ $prayer->sujet }}</p>

                            <div class="community-card-actions">
                                <span class="ghost-submit" style="pointer-events: none;">
                                    {{ __('contact_prayer.wall.support_count', ['count' => $prayer->priants]) }}
                                </span>
                                @if (in_array($prayer->id, $supportedPrayerIds, true))
                                    <span class="solid-submit" style="pointer-events: none; opacity: 0.8;">
                                        {{ __('contact_prayer.wall.supported_cta') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('prayer.support', $prayer->id) }}">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="{{ $currentPageUrl }}" />
                                        <button type="submit" class="solid-submit">{{ __('contact_prayer.wall.support_cta') }}</button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @empty
                        <article class="community-empty reveal">
                            <span class="prayer-card-label">{{ __('community.prayers.hero.label') }}</span>
                            <h3>{{ __('contact_prayer.wall.empty_title') }}</h3>
                            <p>{{ __('contact_prayer.wall.empty') }}</p>
                            <a href="{{ $localizedContactPrayerUrl . '#prayer-form' }}" class="btn-primary-lg">
                                {{ __('contact_prayer.wall.empty_cta') }}
                                <span class="btn-arrow">-></span>
                            </a>
                        </article>
                    @endforelse
                </section>

                @if ($prayerRequests->hasPages())
                    <nav class="community-pagination reveal" aria-label="{{ __('community.prayers.pagination.label') }}">
                        @if ($prayerRequests->onFirstPage())
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('community.prayers.pagination.previous') }}</span>
                        @else
                            <a href="{{ $prayerRequests->previousPageUrl() }}" class="ghost-submit">{{ __('community.prayers.pagination.previous') }}</a>
                        @endif

                        <span class="community-pagination-label">
                            {{ __('community.prayers.pagination.summary', ['page' => $prayerRequests->currentPage(), 'last' => $prayerRequests->lastPage()]) }}
                        </span>

                        @if ($prayerRequests->hasMorePages())
                            <a href="{{ $prayerRequests->nextPageUrl() }}" class="ghost-submit">{{ __('community.prayers.pagination.next') }}</a>
                        @else
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('community.prayers.pagination.next') }}</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </main>
</x-layouts.app>
