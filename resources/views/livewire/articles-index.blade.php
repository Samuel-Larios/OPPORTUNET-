<div>
    <section class="articles-hero">
        <div class="container">
            <div class="articles-hero-shell reveal">
                <div class="articles-hero-copy">
                    <span class="section-label">{{ __('articles.page.label') }}</span>
                    <h1 class="section-title">{{ __('articles.page.title') }}</h1>
                    <p class="section-sub">{{ __('articles.page.subtitle') }}</p>
                </div>

                <div class="articles-hero-meta">
                    <article class="articles-hero-stat">
                        <span>{{ __('articles.page.stat_total') }}</span>
                        <strong>{{ $publishedCount }}</strong>
                    </article>
                    <article class="articles-hero-stat">
                        <span>{{ __('articles.page.stat_featured') }}</span>
                        <strong>{{ $featuredCount }}</strong>
                    </article>
                    <article class="articles-hero-stat">
                        <span>{{ __('articles.page.stat_visible') }}</span>
                        <strong>{{ $filteredCount }}</strong>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="articles-search-section">
        <div class="container">
            <div class="articles-search-shell reveal visible">
                <div class="articles-search-bar">
                    <div class="articles-search-field">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('articles.search.placeholder') }}"
                            aria-label="{{ __('articles.search.aria') }}"
                        />
                    </div>
                </div>

                <div class="articles-search-meta">
                    <span>{{ __('articles.search.live') }}</span>
                    <span wire:loading.inline wire:target="search,category">{{ __('articles.search.searching') }}</span>
                </div>

                <div class="articles-filter-row">
                    <button
                        type="button"
                        wire:click="$set('category', '')"
                        class="articles-filter-chip{{ $category === '' ? ' is-active' : '' }}"
                    >
                        {{ __('articles.filters.all_categories') }}
                    </button>

                    @foreach ($availableCategories as $blogCategory)
                        <button
                            type="button"
                            wire:click="$set('category', '{{ $blogCategory->slug }}')"
                            class="articles-filter-chip{{ $category === $blogCategory->slug ? ' is-active' : '' }}"
                            wire:key="article-category-{{ $blogCategory->id }}"
                        >
                            {{ $blogCategory->nom }}
                        </button>
                    @endforeach

                    <button type="button" wire:click="resetFilters" class="ghost-submit articles-reset">
                        {{ __('articles.filters.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="articles-list-section">
        <div class="container">
            <div class="articles-results-head reveal visible">
                <p>{{ trans_choice('articles.results.count', $filteredCount, ['count' => $filteredCount]) }}</p>
            </div>

            <div class="articles-grid" wire:loading.class="articles-grid-loading" wire:target="search,category">
                @forelse ($articles as $index => $article)
                    @php
                        $accent = $article->category?->couleur ?: '#1A7A6E';
                        $readingTime = $article->temps_lecture ?: __('articles.card.reading_time_fallback');
                        $primaryImageUrl = $article->primaryImageUrl();
                        $primaryImageAlt = $article->primaryImageAlt();
                    @endphp

                    <article class="article-card reveal visible reveal-delay-{{ min(($index % 4) + 1, 4) }}" wire:key="article-{{ $article->id }}">
                        <div class="article-card-visual" style="--article-accent: {{ $accent }};">
                            @if ($primaryImageUrl)
                                <img src="{{ $primaryImageUrl }}" alt="{{ $primaryImageAlt }}">
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
                                    <span class="article-date">
                                        {{ $article->publie_le->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </div>

                            <h2>{{ $article->titre }}</h2>
                            <p>{{ $article->extrait ?: \Illuminate\Support\Str::limit(strip_tags($article->contenu), 170) }}</p>

                            <div class="article-meta-list">
                                <span>{{ __('articles.card.reading_time') }} {{ $readingTime }}</span>
                                <span>{{ __('articles.card.views') }} {{ number_format((int) $article->vues, 0, ',', ' ') }}</span>
                            </div>

                            <div class="article-card-actions">
                                <a href="{{ route('articles.show', $article->slug) }}" class="solid-submit">
                                    {{ __('articles.card.view_details') }}
                                </a>
                            </div>
                            <x-share-buttons
                                :url="route('articles.show', $article->slug)"
                                :title="$article->titre"
                                variant="compact"
                            />
                        </div>
                    </article>
                @empty
                    <article class="empty-card reveal visible">
                        <h2>{{ __('articles.empty.title') }}</h2>
                        <p>{{ __('articles.empty.text') }}</p>
                    </article>
                @endforelse
            </div>

            @if ($articles->hasPages())
                <nav class="offers-pagination reveal visible" aria-label="{{ __('articles.pagination.label') }}">
                    @if ($articles->onFirstPage())
                        <span class="pagination-btn disabled">{{ __('articles.pagination.previous') }}</span>
                    @else
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="pagination-btn">
                            {{ __('articles.pagination.previous') }}
                        </button>
                    @endif

                    <span class="pagination-state">
                        {{ __('articles.pagination.page', ['current' => $articles->currentPage(), 'last' => $articles->lastPage()]) }}
                    </span>

                    @if ($articles->hasMorePages())
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="pagination-btn">
                            {{ __('articles.pagination.next') }}
                        </button>
                    @else
                        <span class="pagination-btn disabled">{{ __('articles.pagination.next') }}</span>
                    @endif
                </nav>
            @endif
        </div>
    </section>
</div>
