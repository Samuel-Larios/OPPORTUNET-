<div>
    <section class="offers-hero">
        <div class="container">
            <div class="offers-hero-shell reveal">
                <div class="offers-hero-copy">
                    <span class="section-label">{{ __('offers.page.label') }}</span>
                    <h1 class="section-title">{{ __('offers.page.title') }}</h1>
                    <p class="section-sub">{{ __('offers.page.subtitle') }}</p>
                </div>
                <div class="offers-hero-meta">
                    <article class="offers-hero-stat">
                        <span>{{ __('offers.page.stat_total') }}</span>
                        <strong>{{ $publishedCount }}</strong>
                    </article>
                    <article class="offers-hero-stat">
                        <span>{{ __('offers.page.stat_featured') }}</span>
                        <strong>{{ $filteredCount }}</strong>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="offers-search-section">
        <div class="container">
            <div class="offers-search-shell reveal">
                <div class="offers-search-bar">
                    <div class="offers-search-field">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('offers.search.placeholder') }}"
                            aria-label="{{ __('offers.search.aria') }}"
                        />
                    </div>
                </div>

                <div class="offers-search-meta">
                    <span>{{ __('offers.search.live') }}</span>
                    <span wire:loading.inline wire:target="search,type,contrat,pays,teletravail,urgent">{{ __('offers.search.searching') }}</span>
                </div>

                <div class="offers-filters">
                    <select wire:model.live="type" aria-label="{{ __('offers.filters.type') }}">
                        <option value="">{{ __('offers.filters.all_types') }}</option>
                        @foreach (__('offers.types') as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="contrat" aria-label="{{ __('offers.filters.contract') }}">
                        <option value="">{{ __('offers.filters.all_contracts') }}</option>
                        @foreach (__('offers.contracts') as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="pays" aria-label="{{ __('offers.filters.country') }}">
                        <option value="">{{ __('offers.filters.all_countries') }}</option>
                        @foreach ($availableCountries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>

                    <label class="offers-check{{ $teletravail ? ' is-active' : '' }}">
                        <input type="checkbox" wire:model.live="teletravail" />
                        <span>{{ __('offers.filters.remote') }}</span>
                    </label>

                    <label class="offers-check{{ $urgent ? ' is-active' : '' }}">
                        <input type="checkbox" wire:model.live="urgent" />
                        <span>{{ __('offers.filters.urgent') }}</span>
                    </label>

                    <button type="button" wire:click="resetFilters" class="ghost-submit offers-reset">
                        {{ __('offers.filters.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="offers-list-section">
        <div class="container">
            <div class="offers-results-head reveal">
                <p>{{ trans_choice('offers.results.count', $filteredCount, ['count' => $filteredCount]) }}</p>
            </div>

            <div class="offers-grid" wire:loading.class="offers-grid-loading" wire:target="search,type,contrat,pays,teletravail,urgent">
                @forelse ($opportunities as $index => $opportunity)
                    <article class="offer-list-card reveal reveal-delay-{{ min(($index % 4) + 1, 4) }}" wire:key="offer-{{ $opportunity->id }}">
                        <div class="offer-card-top">
                            <div class="offer-badges">
                                <span class="opportunity-type">{{ __('home.opportunity_types.' . $opportunity->type) }}</span>
                                @if ($opportunity->urgent)
                                    <span class="opportunity-urgent">{{ __('offers.badges.urgent') }}</span>
                                @endif
                                @if ($opportunity->teletravail)
                                    <span class="offer-remote-badge">{{ __('offers.badges.remote') }}</span>
                                @endif
                            </div>
                            @if ($opportunity->date_publication)
                                <span class="offer-date">{{ __('offers.card.published') }} {{ $opportunity->date_publication->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>

                        <h2>{{ $opportunity->titre }}</h2>
                        <p>{{ \Illuminate\Support\Str::limit($opportunity->description, 180) }}</p>

                        <div class="offer-meta-list">
                            <span>{{ $opportunity->organisation ?: __('offers.card.organization_fallback') }}</span>
                            <span>{{ trim(($opportunity->lieu ? $opportunity->lieu . ', ' : '') . ($opportunity->pays ?: '')) ?: __('offers.card.location_fallback') }}</span>
                            @if ($opportunity->contrat)
                                <span>{{ __('offers.contracts.' . $opportunity->contrat) }}</span>
                            @endif
                            @if ($opportunity->date_expiration)
                                <span>{{ __('offers.card.deadline') }} {{ $opportunity->date_expiration->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>

                        <div class="offer-card-actions">
                            <a href="{{ route('offers.show', $opportunity->slug) }}" class="solid-submit">{{ __('offers.card.view_details') }}</a>
                        </div>
                    </article>
                @empty
                    <article class="empty-card reveal">
                        <h2>{{ __('offers.empty.title') }}</h2>
                        <p>{{ __('offers.empty.text') }}</p>
                    </article>
                @endforelse
            </div>

            @if ($opportunities->hasPages())
                <nav class="offers-pagination reveal" aria-label="{{ __('offers.pagination.label') }}">
                    @if ($opportunities->onFirstPage())
                        <span class="pagination-btn disabled">{{ __('offers.pagination.previous') }}</span>
                    @else
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="pagination-btn">
                            {{ __('offers.pagination.previous') }}
                        </button>
                    @endif

                    <span class="pagination-state">
                        {{ __('offers.pagination.page', ['current' => $opportunities->currentPage(), 'last' => $opportunities->lastPage()]) }}
                    </span>

                    @if ($opportunities->hasMorePages())
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="pagination-btn">
                            {{ __('offers.pagination.next') }}
                        </button>
                    @else
                        <span class="pagination-btn disabled">{{ __('offers.pagination.next') }}</span>
                    @endif
                </nav>
            @endif
        </div>
    </section>
</div>
