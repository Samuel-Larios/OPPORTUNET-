<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.prayers.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.prayers.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.prayers.all_statuses') }}</option>
                    @foreach (__('admin.prayers.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="typeFilter">
                    <option value="">{{ __('admin.prayers.all_types') }}</option>
                    @foreach (__('admin.prayers.types') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($prayers as $prayer)
                    <button type="button" wire:click="selectPrayer({{ $prayer->id }})" class="panel-application-item{{ $selectedPrayer && $selectedPrayer->id === $prayer->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $prayer->publicAuthorName() }}</strong>
                            <span>{{ $prayer->typeLabel() }}</span>
                            <span>{{ $prayer->priants }} {{ __('admin.prayers.labels.support_count') }}</span>
                        </div>
                        <span class="panel-badge{{ $prayer->statut === 'approuve' ? ' is-success' : ' is-muted' }}">{{ $prayer->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.prayers.empty') }}</p>
                @endforelse
            </div>

            @if ($prayers->hasPages())
                <div class="panel-pagination">
                    {{ $prayers->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedPrayer)
                <div class="panel-card-head">
                    <h2>{{ __('admin.prayers.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.prayers.labels.author') }}</span>
                            <strong>{{ $selectedPrayer->prenom }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.email') }}</span>
                            <strong>{{ $selectedPrayer->email ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.country') }}</span>
                            <strong>{{ $selectedPrayer->pays ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.support_count') }}</span>
                            <strong>{{ $selectedPrayer->priants }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.submitted') }}</span>
                            <strong>{{ $selectedPrayer->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.from_account') }}</span>
                            <strong>{{ $selectedPrayer->user?->fullName() ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.prayers.labels.visibility') }}</span>
                            <strong>{{ $selectedPrayer->visibilityLabel() }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.prayers.labels.status') }}</strong>
                        <p style="margin-top: 12px;">{{ $selectedPrayer->statusDescription() }}</p>
                    </div>

                    <form wire:submit="updatePrayer" class="panel-form-grid">
                        <label class="panel-field">
                            <span>{{ __('admin.prayers.labels.author') }}</span>
                            <input type="text" wire:model="prenom" />
                            @error('prenom') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.prayers.labels.country') }}</span>
                            <input type="text" wire:model="pays" />
                            @error('pays') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.prayers.labels.email') }}</span>
                            <input type="email" wire:model="email" />
                            @error('email') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.prayers.labels.type') }}</span>
                            <select wire:model="prayerType">
                                @foreach (__('admin.prayers.types') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('prayerType') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.prayers.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.prayers.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-checkline">
                            <input type="checkbox" wire:model="anonyme" />
                            <span>{{ __('admin.prayers.labels.anonymous') }}</span>
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.prayers.labels.subject') }}</span>
                            <textarea wire:model="sujet" rows="6"></textarea>
                            @error('sujet') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.prayers.process') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.prayers.empty') }}</p>
            @endif
        </article>
    </section>
</div>
