@php
    $seoDescription = \App\Support\Seo::description(__('community.testimonials.hero.subtitle'));
    $seoCanonical = \App\Support\Seo::localizedUrl(route('community.testimonials.index'), app()->getLocale(), request()->query());
    $localizedHomeUrl = \App\Support\Seo::localizedUrl(route('home'), app()->getLocale());
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('community.testimonials.hero.label'), 'url' => $seoCanonical],
        ]),
        \App\Support\Seo::schema('CollectionPage', [
            'name' => __('community.testimonials.meta.title'),
            'url' => $seoCanonical,
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
        ]),
    ];
@endphp

<x-layouts.app
    :title="__('community.testimonials.meta.title')"
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
    <style>
        .community-testimonials-page {
            padding: 64px 0 88px;
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.18), transparent 28%),
                linear-gradient(180deg, #fffdf7 0%, #f8f4ea 100%);
        }

        .community-testimonials-shell {
            display: grid;
            gap: 24px;
        }

        .community-testimonials-hero,
        .community-testimonial-stat,
        .community-testimonial-card,
        .community-testimonial-empty {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .community-testimonials-hero,
        .community-testimonial-stat,
        .community-testimonial-card,
        .community-testimonial-empty {
            padding: 24px;
        }

        .community-testimonials-actions,
        .community-testimonials-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .community-testimonials-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .community-testimonial-stat strong {
            display: block;
            margin: 8px 0 6px;
            font-size: 2rem;
        }

        .community-testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .community-testimonial-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .community-testimonial-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f59e0b, #b45309);
            color: #ffffff;
            font-weight: 700;
        }

        .community-testimonial-meta {
            margin-top: 16px;
            color: #475569;
        }

        .community-testimonials-pagination-label {
            color: #475569;
        }

        @media (max-width: 900px) {
            .community-testimonials-stats,
            .community-testimonials-grid {
                grid-template-columns: 1fr;
            }

            .community-testimonial-card-top {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <main class="community-testimonials-page">
        <div class="container">
            <div class="community-testimonials-shell">
                <section class="community-testimonials-hero reveal">
                    <span class="section-label">{{ __('community.testimonials.hero.label') }}</span>
                    <h1 class="section-title">{{ __('community.testimonials.hero.title') }}</h1>
                    <p class="section-sub">{{ __('community.testimonials.hero.subtitle') }}</p>
                    <p>{{ __('community.testimonials.hero.note') }}</p>

                    <div class="community-testimonials-actions">
                        <a href="{{ $localizedHomeUrl . '#testimonial-form' }}" class="btn-primary-lg">
                            {{ __('community.testimonials.hero.primary_cta') }}
                            <span class="btn-arrow">-></span>
                        </a>
                        <a href="{{ $localizedHomeUrl . '#home-testimonials' }}" class="ghost-submit">{{ __('community.testimonials.hero.secondary_cta') }}</a>
                    </div>
                </section>

                <section class="community-testimonials-stats reveal">
                    <article class="community-testimonial-stat">
                        <span>{{ __('community.testimonials.stats.approved_label') }}</span>
                        <strong>{{ $approvedTestimonialTotal }}</strong>
                        <small>{{ __('community.testimonials.stats.approved_text') }}</small>
                    </article>
                    <article class="community-testimonial-stat">
                        <span>{{ __('community.testimonials.stats.featured_label') }}</span>
                        <strong>{{ $featuredTestimonialTotal }}</strong>
                        <small>{{ __('community.testimonials.stats.featured_text') }}</small>
                    </article>
                    <article class="community-testimonial-stat">
                        <span>{{ __('community.testimonials.stats.visible_label') }}</span>
                        <strong>{{ $testimonials->count() }}</strong>
                        <small>{{ __('community.testimonials.stats.visible_text') }}</small>
                    </article>
                </section>

                <section class="community-testimonials-grid">
                    @forelse ($testimonials as $testimonial)
                        <article class="community-testimonial-card reveal">
                            <div class="community-testimonial-card-top">
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <span class="community-testimonial-avatar">{{ strtoupper(substr($testimonial->prenom, 0, 1)) }}</span>
                                    <div>
                                        <strong>{{ trim($testimonial->prenom . ' ' . ($testimonial->nom ?? '')) }}</strong>
                                        <div class="community-testimonial-meta">
                                            {{ $testimonial->typeLabel() }}
                                            @if ($testimonial->note)
                                                · {{ $testimonial->note }}/5
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($testimonial->en_vedette)
                                    <span class="prayer-card-label">{{ __('community.testimonials.featured_badge') }}</span>
                                @endif
                            </div>

                            <p style="margin: 18px 0 14px;">{{ $testimonial->contenu }}</p>

                            <div class="community-testimonial-meta">
                                {{ trim(($testimonial->profession ?: '') . ($testimonial->pays ? ' - ' . $testimonial->pays : '')) ?: __('community.testimonials.meta_fallback') }}
                            </div>
                        </article>
                    @empty
                        <article class="community-testimonial-empty reveal">
                            <span class="section-label">{{ __('community.testimonials.hero.label') }}</span>
                            <h3>{{ __('home.sections.testimonials.empty_title') }}</h3>
                            <p>{{ __('home.sections.testimonials.empty_text') }}</p>
                            <a href="{{ $localizedHomeUrl . '#testimonial-form' }}" class="btn-primary-lg">
                                {{ __('community.testimonials.hero.primary_cta') }}
                                <span class="btn-arrow">-></span>
                            </a>
                        </article>
                    @endforelse
                </section>

                @if ($testimonials->hasPages())
                    <nav class="community-testimonials-pagination reveal" aria-label="{{ __('community.testimonials.pagination.label') }}">
                        @if ($testimonials->onFirstPage())
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('community.testimonials.pagination.previous') }}</span>
                        @else
                            <a href="{{ \App\Support\Seo::localizedUrl(route('community.testimonials.index'), app()->getLocale(), array_merge(request()->query(), ['page' => $testimonials->currentPage() - 1])) }}" class="ghost-submit">{{ __('community.testimonials.pagination.previous') }}</a>
                        @endif

                        <span class="community-testimonials-pagination-label">
                            {{ __('community.testimonials.pagination.summary', ['page' => $testimonials->currentPage(), 'last' => $testimonials->lastPage()]) }}
                        </span>

                        @if ($testimonials->hasMorePages())
                            <a href="{{ \App\Support\Seo::localizedUrl(route('community.testimonials.index'), app()->getLocale(), array_merge(request()->query(), ['page' => $testimonials->currentPage() + 1])) }}" class="ghost-submit">{{ __('community.testimonials.pagination.next') }}</a>
                        @else
                            <span class="ghost-submit" style="pointer-events: none; opacity: 0.7;">{{ __('community.testimonials.pagination.next') }}</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </main>
</x-layouts.app>
