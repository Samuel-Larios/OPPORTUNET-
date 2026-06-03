@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+229XXXXXXXXX';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $accent = $article->category?->couleur ?: '#1A7A6E';
    $galleryImages = $article->images->take(5);
    $primaryImageUrl = $article->primaryImageUrl();
    $primaryImageAlt = $article->primaryImageAlt();
    $commentsByParent = $approvedComments->groupBy(fn ($comment) => $comment->parent_id ?? 0);
    $topLevelComments = $commentsByParent->get(0, collect());
    $activeReplyComment = $replyComment ?? (old('parent_id') ? $approvedComments->firstWhere('id', (int) old('parent_id')) : null);
@endphp

<x-layouts.app
    :title="$article->meta_titre ?: $article->titre"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="article-detail-page">
        <section class="article-detail-hero">
            <div class="container">
                <div class="article-detail-shell reveal">
                    <div class="article-detail-copy">
                        <a href="{{ route('articles.index') }}" class="offers-detail-back">{{ __('articles.detail.back') }}</a>
                        <div class="article-badges">
                            <span class="article-category-badge" style="--article-accent: {{ $accent }};">
                                {{ $article->category?->nom ?: __('articles.card.default_badge') }}
                            </span>
                            @if ($article->en_vedette)
                                <span class="article-featured-badge">{{ __('articles.badges.featured') }}</span>
                            @endif
                        </div>
                        <h1 class="section-title">{{ $article->titre }}</h1>
                        <p class="section-sub">{{ $article->extrait ?: \Illuminate\Support\Str::limit(strip_tags($article->contenu), 220) }}</p>

                        <div class="article-detail-meta">
                            @if ($article->publie_le)
                                <span>{{ __('articles.card.published') }} {{ $article->publie_le->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                            @endif
                            <span>{{ __('articles.card.reading_time') }} {{ $article->temps_lecture ?: __('articles.card.reading_time_fallback') }}</span>
                            <span>{{ __('articles.card.views') }} {{ number_format((int) $article->vues, 0, ',', ' ') }}</span>
                        </div>
                    </div>

                    <aside class="article-detail-side">
                        <article class="offers-detail-action-card">
                            <span>{{ __('articles.detail.side_label') }}</span>
                            <strong>{{ __('articles.detail.side_title') }}</strong>
                            <p>{{ __('articles.detail.side_text') }}</p>
                            <div class="offers-detail-actions">
                                <a href="{{ route('articles.index') }}" class="solid-submit">{{ __('articles.detail.all_articles') }}</a>
                            </div>
                        </article>
                    </aside>
                </div>
            </div>
        </section>

        <section class="article-detail-content">
            <div class="container">
                <div class="article-detail-layout">
                    <article class="article-detail-main reveal">
                        @if ($primaryImageUrl)
                            <div class="article-detail-cover">
                                <img src="{{ $primaryImageUrl }}" alt="{{ $primaryImageAlt }}">
                            </div>
                        @endif

                        @if ($galleryImages->count() > 1)
                            <div class="article-detail-gallery">
                                <div class="article-detail-gallery-head">
                                    <h2>{{ __('articles.detail.gallery_title') }}</h2>
                                    <span>{{ trans_choice('articles.detail.gallery_count', $galleryImages->count(), ['count' => $galleryImages->count()]) }}</span>
                                </div>

                                <div class="article-detail-gallery-grid">
                                    @foreach ($galleryImages as $image)
                                        <figure class="article-detail-gallery-item">
                                            <img src="{{ $image->publicUrl() }}" alt="{{ $image->altText($article->titre) }}">
                                        </figure>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="article-detail-richtext">
                            {!! nl2br(e($article->contenu)) !!}
                        </div>
                    </article>

                    <aside class="article-detail-aside reveal reveal-delay-1">
                        <article class="offers-detail-panel">
                            <h2>{{ __('articles.detail.summary') }}</h2>
                            <ul class="offers-detail-summary">
                                <li>
                                    <span>{{ __('articles.detail.labels.category') }}</span>
                                    <strong>{{ $article->category?->nom ?: __('articles.card.default_badge') }}</strong>
                                </li>
                                <li>
                                    <span>{{ __('articles.detail.labels.status') }}</span>
                                    <strong>{{ $article->en_vedette ? __('articles.badges.featured') : __('articles.detail.standard') }}</strong>
                                </li>
                                <li>
                                    <span>{{ __('articles.detail.labels.reading_time') }}</span>
                                    <strong>{{ $article->temps_lecture ?: __('articles.card.reading_time_fallback') }}</strong>
                                </li>
                                <li>
                                    <span>{{ __('articles.detail.labels.comments') }}</span>
                                    <strong>{{ trans_choice('articles.comments.count', $approvedComments->count(), ['count' => $approvedComments->count()]) }}</strong>
                                </li>
                            </ul>
                        </article>
                    </aside>
                </div>
            </div>
        </section>

        <section class="article-detail-content" id="article-comments">
            <div class="container">
                <div class="article-detail-layout">
                    <article class="article-detail-main reveal">
                        <div class="home-section-head">
                            <span class="section-label">{{ __('articles.comments.label') }}</span>
                            <h2 class="section-title">{{ __('articles.comments.title') }}</h2>
                            <p class="section-sub">{{ __('articles.comments.subtitle') }}</p>
                        </div>

                        @if (session('article_comment_success'))
                            <div class="home-alert success reveal">{{ session('article_comment_success') }}</div>
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

                        <div class="contact-form-card">
                            <strong>{{ trans_choice('articles.comments.count', $approvedComments->count(), ['count' => $approvedComments->count()]) }}</strong>

                            @if ($topLevelComments->isNotEmpty())
                                <div style="margin-top: 16px;">
                                    @foreach ($topLevelComments as $comment)
                                        @include('articles.partials.comment', [
                                            'article' => $article,
                                            'comment' => $comment,
                                            'commentsByParent' => $commentsByParent,
                                        ])
                                    @endforeach
                                </div>
                            @else
                                <p style="margin-top: 12px;">{{ __('articles.comments.empty') }}</p>
                            @endif
                        </div>
                    </article>

                    <aside class="article-detail-aside reveal reveal-delay-1">
                        <div class="contact-form-card">
                            @if ($article->commentaires_actifs)
                                @auth
                                    <form method="POST" action="{{ route('articles.comments.store', $article->slug) }}">
                                        @csrf

                                        @if ($activeReplyComment)
                                            <div class="contact-form-card" style="margin-bottom: 16px;">
                                                <strong>{{ __('articles.comments.reply_to', ['author' => $activeReplyComment->authorLabel()]) }}</strong>
                                                <p>{{ \Illuminate\Support\Str::limit($activeReplyComment->contenu, 140) }}</p>
                                                <a href="{{ route('articles.show', $article->slug) }}#article-comments" class="ghost-submit">{{ __('articles.comments.cancel_reply') }}</a>
                                            </div>
                                            <input type="hidden" name="parent_id" value="{{ $activeReplyComment->id }}" />
                                        @endif

                                        <p style="margin-bottom: 12px;">{{ __('articles.comments.logged_in_as', ['author' => auth()->user()->fullName()]) }}</p>
                                        <textarea name="contenu" rows="6" placeholder="{{ __('articles.comments.fields.content') }}">{{ old('contenu') }}</textarea>

                                        <div class="contact-form-actions">
                                            <button type="submit" class="solid-submit">{{ __('articles.comments.submit') }}</button>
                                        </div>
                                    </form>
                                @else
                                    <strong>{{ __('articles.comments.label') }}</strong>
                                    <p style="margin-top: 12px;">{{ __('articles.comments.login_required') }}</p>
                                    <div class="contact-form-actions" style="margin-top: 16px;">
                                        <a href="{{ route('login', ['redirect_to' => route('articles.show', $article->slug) . '#article-comments']) }}" class="solid-submit">{{ __('articles.comments.login_action') }}</a>
                                        <a href="{{ route('register.user', ['redirect_to' => route('articles.show', $article->slug) . '#article-comments']) }}" class="ghost-submit">{{ __('articles.comments.register_action') }}</a>
                                    </div>
                                @endauth
                            @else
                                <strong>{{ __('articles.comments.label') }}</strong>
                                <p style="margin-top: 12px;">{{ __('articles.comments.closed') }}</p>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        @if ($relatedArticles->isNotEmpty())
            <section class="offers-detail-related">
                <div class="container">
                    <div class="home-section-head reveal">
                        <span class="section-label">{{ __('articles.detail.related_label') }}</span>
                        <h2 class="section-title">{{ __('articles.detail.related_title') }}</h2>
                    </div>

                    <div class="articles-grid">
                        @foreach ($relatedArticles as $index => $relatedArticle)
                            @php
                                $relatedAccent = $relatedArticle->category?->couleur ?: '#1A7A6E';
                                $relatedImageUrl = $relatedArticle->primaryImageUrl();
                                $relatedImageAlt = $relatedArticle->primaryImageAlt();
                            @endphp
                            <article class="article-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                                <div class="article-card-visual" style="--article-accent: {{ $relatedAccent }};">
                                    @if ($relatedImageUrl)
                                        <img src="{{ $relatedImageUrl }}" alt="{{ $relatedImageAlt }}">
                                    @else
                                        <div class="article-card-placeholder">
                                            <span>{{ $relatedArticle->category?->nom ?: __('articles.card.default_badge') }}</span>
                                            <strong>{{ $relatedArticle->titre }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="article-card-body">
                                    <div class="article-card-top">
                                        <span class="article-category-badge" style="--article-accent: {{ $relatedAccent }};">
                                            {{ $relatedArticle->category?->nom ?: __('articles.card.default_badge') }}
                                        </span>
                                    </div>
                                    <h3>{{ $relatedArticle->titre }}</h3>
                                    <p>{{ $relatedArticle->extrait ?: \Illuminate\Support\Str::limit(strip_tags($relatedArticle->contenu), 145) }}</p>
                                    <div class="article-card-actions">
                                        <a href="{{ route('articles.show', $relatedArticle->slug) }}" class="solid-submit">
                                            {{ __('articles.card.view_details') }}
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
</x-layouts.app>
