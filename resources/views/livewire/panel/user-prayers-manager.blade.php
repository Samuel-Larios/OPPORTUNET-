<div class="panel-stack">
    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_prayers.list_title') }}</h2>
                <p>{{ __('admin.user_prayers.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($prayers as $prayer)
                    <button type="button" wire:click="selectPrayer({{ $prayer->id }})" class="panel-application-item{{ $selectedPrayer && $selectedPrayer->id === $prayer->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $prayer->typeLabel() }}</strong>
                            <span>{{ $prayer->created_at->format('d/m/Y') }}</span>
                            <span>{{ $prayer->priants }} {{ __('admin.prayers.labels.support_count') }}</span>
                        </div>
                        <span class="panel-badge{{ $prayer->statut === 'approuve' ? ' is-success' : ' is-muted' }}">{{ $prayer->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.user_prayers.empty') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            @if ($selectedPrayer)
                <div class="panel-card-head">
                    <h2>{{ __('admin.user_prayers.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.user_prayers.labels.status') }}</span>
                            <strong>{{ $selectedPrayer->statusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_prayers.labels.type') }}</span>
                            <strong>{{ $selectedPrayer->typeLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_prayers.labels.submitted') }}</span>
                            <strong>{{ $selectedPrayer->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_prayers.labels.support_count') }}</span>
                            <strong>{{ $selectedPrayer->priants }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_prayers.labels.visibility') }}</span>
                            <strong>{{ $selectedPrayer->visibilityLabel() }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_prayers.labels.status') }}</strong>
                        <p style="margin-top: 12px;">{{ __('admin.user_prayers.status_help.' . $selectedPrayer->statut) }}</p>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_prayers.labels.subject') }}</strong>
                        <p style="margin-top: 12px;">{{ $selectedPrayer->sujet }}</p>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_prayers.labels.anonymous') }}</strong>
                        <p>{{ $selectedPrayer->anonyme ? __('admin.user_prayers.labels.yes') : __('admin.user_prayers.labels.no') }}</p>
                    </div>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.user_prayers.empty') }}</p>
            @endif
        </article>
    </section>
</div>
