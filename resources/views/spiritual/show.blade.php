@php
    $isFrench = app()->getLocale() === 'fr';
@endphp

@once
    <style>
        .spiritual-detail-page {
            padding: 0 0 72px;
        }

        .spiritual-detail-shell {
            display: grid;
            gap: 24px;
        }

        .spiritual-detail-card {
            display: grid;
            gap: 18px;
            padding: 28px;
            border-radius: 30px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(244, 248, 241, 0.98));
            border: 1px solid rgba(67, 104, 28, 0.14);
            box-shadow: 0 20px 48px rgba(41, 59, 18, 0.12);
        }

        .spiritual-detail-top {
            display: grid;
            gap: 10px;
        }

        .spiritual-detail-top h1,
        .spiritual-detail-card p {
            margin: 0;
        }

        .spiritual-detail-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(67, 104, 28, 0.1);
            color: #43681c;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .spiritual-detail-ref,
        .spiritual-detail-meta,
        .spiritual-detail-author {
            color: #43681c;
            font-weight: 700;
        }

        .spiritual-detail-excerpt {
            font-size: 1.05rem;
            color: #395545;
            line-height: 1.8;
        }

        .spiritual-detail-content {
            color: #21352b;
            line-height: 1.9;
        }

        .spiritual-detail-content p + p {
            margin-top: 14px;
        }

        .spiritual-detail-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            color: #0f5a83;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
@endonce

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
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
    <main class="spiritual-detail-page">
        <section class="home-strip">
            <div class="container">
                <div class="spiritual-detail-shell">
                    <a href="{{ $backUrl }}" class="spiritual-detail-back reveal visible">
                        <span>&lt;</span>
                        <span>{{ $backLabel }}</span>
                    </a>

                    <article class="spiritual-detail-card reveal visible">
                        <div class="spiritual-detail-top">
                            <span class="spiritual-detail-kicker">{{ $kicker }}</span>
                            <h1>{{ $title }}</h1>

                            @if (! empty($reference))
                                <div class="spiritual-detail-ref">{{ $reference }}</div>
                            @endif

                            @if (! empty($metaValue))
                                <div class="spiritual-detail-meta">{{ $metaLabel }}: {{ $metaValue }}</div>
                            @endif
                        </div>

                        @if (! empty($excerpt))
                            <p class="spiritual-detail-excerpt">{{ $excerpt }}</p>
                        @endif

                        <div class="spiritual-detail-content">
                            {!! nl2br(e($content)) !!}
                        </div>

                        @if (! empty($author) && $metaValue !== $author)
                            <div class="spiritual-detail-author">
                                {{ $isFrench ? 'Par' : 'By' }} {{ $author }}
                            </div>
                        @endif

                        <x-share-buttons
                            :url="$shareUrl"
                            :title="$shareTitle"
                            :text="$shareText"
                            variant="full"
                        />
                    </article>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
