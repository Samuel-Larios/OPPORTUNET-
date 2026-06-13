@php
    $isFrench = app()->getLocale() === 'fr';
@endphp

@once
    <style>
        .verse-page {
            padding: 0 0 72px;
        }

        .verse-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .verse-card {
            display: grid;
            gap: 14px;
            padding: 24px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(242, 247, 235, 0.98));
            border: 1px solid rgba(98, 126, 48, 0.14);
            box-shadow: 0 18px 44px rgba(54, 74, 24, 0.1);
            cursor: pointer;
        }

        .verse-card h2 {
            margin: 0;
            color: #18301f;
        }

        .verse-card p,
        .verse-card strong,
        .verse-card span {
            margin: 0;
        }

        .verse-version {
            color: #627e30;
            font-weight: 700;
        }

        .verse-card-actions {
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
    <main class="verse-page">
        <section class="home-strip">
            <div class="container">
                <div class="home-section-head reveal visible">
                    <span class="section-label">{{ $isFrench ? 'Versets bibliques' : 'Bible verses' }}</span>
                    <h1 class="section-title">{{ $title }}</h1>
                    <p class="section-sub">{{ $description }}</p>
                </div>

                <div class="verse-grid">
                    @foreach ($verses as $index => $verse)
                        <article
                            class="verse-card reveal reveal-delay-{{ min($index + 1, 3) }}"
                            data-card-link="{{ \App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale()) }}"
                        >
                            <span class="section-label">{{ $isFrench ? 'Référence' : 'Reference' }}</span>
                            <h2>{{ $verse->reference }}</h2>
                            <p>{{ $verse->texte }}</p>
                            <strong class="verse-version">{{ $verse->version }}</strong>

                            <div class="verse-card-actions">
                                <a
                                    href="{{ \App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale()) }}"
                                    class="solid-submit"
                                >
                                    {{ $isFrench ? 'Voir les détails' : 'View details' }}
                                </a>
                            </div>

                            <x-share-buttons
                                :url="\App\Support\Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale())"
                                :title="$verse->reference"
                                :text="$verse->texte . ' - ' . $verse->version"
                            />
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
