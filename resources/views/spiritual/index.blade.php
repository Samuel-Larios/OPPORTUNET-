@php
    $isFrench = app()->getLocale() === 'fr';
    $detailRouteName = $detailRouteName ?? null;
@endphp

@once
    <style>
        .spiritual-page {
            padding: 0 0 72px;
        }

        .spiritual-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .spiritual-card {
            display: grid;
            gap: 14px;
            padding: 24px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(240, 247, 249, 0.98));
            border: 1px solid rgba(17, 109, 121, 0.12);
            box-shadow: 0 18px 44px rgba(15, 63, 72, 0.1);
            cursor: pointer;
        }

        .spiritual-card-top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 16px;
        }

        .spiritual-kicker {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(17, 109, 121, 0.1);
            color: #116d79;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .spiritual-card h2 {
            margin: 0;
            font-size: 1.3rem;
            color: #0d2e35;
        }

        .spiritual-card p {
            margin: 0;
            color: #395a64;
            line-height: 1.8;
        }

        .spiritual-ref {
            color: #116d79;
            font-weight: 700;
        }

        .spiritual-author {
            color: #627f88;
            font-size: 0.95rem;
        }

        .spiritual-empty {
            padding: 34px;
            border-radius: 28px;
            background: rgba(247, 250, 252, 0.98);
            border: 1px dashed rgba(17, 109, 121, 0.2);
            text-align: center;
            color: #58727b;
        }

        .spiritual-card-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }
    </style>

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

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="\App\Support\Seo::localizedUrl(url()->current(), app()->getLocale())"
    :page-banner-title="$title"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="spiritual-page">
        <section class="home-strip">
            <div class="container">
                <div class="home-section-head reveal visible">
                    <span class="section-label">{{ $kicker }}</span>
                    <h1 class="section-title">{{ $title }}</h1>
                    <p class="section-sub">{{ $description }}</p>
                </div>

                @if ($items->isEmpty())
                    <div class="spiritual-empty reveal visible">
                        {{ $isFrench ? 'Le contenu apparaîtra ici dès sa publication.' : 'Content will appear here as soon as it is published.' }}
                    </div>
                @else
                    <div class="spiritual-grid">
                        @foreach ($items as $index => $item)
                            <article
                                class="spiritual-card reveal reveal-delay-{{ min($index + 1, 3) }}"
                                @if ($detailRouteName)
                                    data-card-link="{{ \App\Support\Seo::localizedUrl(route($detailRouteName, $item->slug), app()->getLocale()) }}"
                                @endif
                            >
                                <div class="spiritual-card-top">
                                    <span class="spiritual-kicker">{{ $kicker }}</span>
                                    @if ($item->reference)
                                        <span class="spiritual-ref">{{ $item->reference }}</span>
                                    @endif
                                </div>

                                <div style="display: grid; gap: 10px;">
                                    <h2>{{ $item->titre }}</h2>

                                    @if ($item->extrait)
                                        <p>{{ $item->extrait }}</p>
                                    @endif

                                    <p>{{ \Illuminate\Support\Str::limit($item->contenu, 220) }}</p>
                                </div>

                                @if ($item->auteur)
                                    <div class="spiritual-author">{{ $item->auteur }}</div>
                                @endif

                                @if ($detailRouteName)
                                    <div class="spiritual-card-actions">
                                        <a
                                            href="{{ \App\Support\Seo::localizedUrl(route($detailRouteName, $item->slug), app()->getLocale()) }}"
                                            class="solid-submit"
                                        >
                                            {{ $isFrench ? 'Voir les détails' : 'View details' }}
                                        </a>
                                    </div>
                                @endif

                                <x-share-buttons
                                    :url="$detailRouteName
                                        ? \App\Support\Seo::localizedUrl(route($detailRouteName, $item->slug), app()->getLocale())
                                        : \App\Support\Seo::localizedUrl(url()->current(), app()->getLocale(), ['item' => $item->slug])"
                                    :title="$item->titre"
                                    :text="$item->extrait ?: \Illuminate\Support\Str::limit($item->contenu, 180)"
                                />
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.app>
