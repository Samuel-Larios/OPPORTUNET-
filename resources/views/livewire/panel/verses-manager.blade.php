<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.verses.title') }}</h2>
                <p>{{ __('admin.verses.intro') }}</p>
            </div>

            <form wire:submit="saveVerse" class="panel-form-grid">
                <label class="panel-field">
                    <span>FR {{ __('admin.verses.reference') }}</span>
                    <input type="text" wire:model="referenceFr" />
                    @error('referenceFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN {{ __('admin.verses.reference') }}</span>
                    <input type="text" wire:model="referenceEn" />
                    @error('referenceEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR {{ __('admin.verses.text') }}</span>
                    <textarea rows="5" wire:model="texteFr"></textarea>
                    @error('texteFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN {{ __('admin.verses.text') }}</span>
                    <textarea rows="5" wire:model="texteEn"></textarea>
                    @error('texteEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>FR {{ __('admin.verses.version') }}</span>
                    <input type="text" wire:model="versionFr" />
                    @error('versionFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN {{ __('admin.verses.version') }}</span>
                    <input type="text" wire:model="versionEn" />
                    @error('versionEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.verses.order') }}</span>
                    <input type="number" min="0" wire:model="ordre" />
                    <small>{{ __('admin.verses.order_hint') }}</small>
                    @error('ordre') <small>{{ $message }}</small> @enderror
                </label>

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="actif" />
                        <span>{{ __('admin.verses.active') }}</span>
                    </label>
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="afficherAccueil" />
                        <span>{{ __('admin.verses.show_on_home') }}</span>
                    </label>
                </div>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.verses.save') }}</button>
                    <button type="button" wire:click="resetForm" class="panel-secondary-btn">{{ __('admin.verses.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.verses.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.verses.search') }}" />
                <select wire:model.live="activeFilter">
                    <option value="">{{ __('admin.verses.all_statuses') }}</option>
                    <option value="1">{{ __('admin.verses.active') }}</option>
                    <option value="0">{{ __('admin.verses.inactive') }}</option>
                </select>
                <select wire:model.live="homeFilter">
                    <option value="">{{ __('admin.verses.all_home_states') }}</option>
                    <option value="1">{{ __('admin.verses.show_on_home') }}</option>
                    <option value="0">{{ __('admin.verses.hide_from_home') }}</option>
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.verses.reference') }}</th>
                            <th>{{ __('admin.verses.version') }}</th>
                            <th>{{ __('admin.verses.home_display') }}</th>
                            <th>{{ __('admin.verses.order') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($verses as $verse)
                            <tr>
                                <td>
                                    <strong>{{ $verse->reference }}</strong>
                                    <span>{{ \Illuminate\Support\Str::limit($verse->texte, 90) }}</span>
                                </td>
                                <td>{{ $verse->version }}</td>
                                <td>
                                    <div style="display: grid; gap: 6px;">
                                        <span class="panel-badge{{ $verse->actif ? ' is-success' : ' is-muted' }}">
                                            {{ $verse->actif ? __('admin.verses.active') : __('admin.verses.inactive') }}
                                        </span>
                                        <span class="panel-badge{{ $verse->afficher_accueil ? '' : ' is-muted' }}">
                                            {{ $verse->afficher_accueil ? __('admin.verses.show_on_home') : __('admin.verses.hide_from_home') }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $verse->ordre }}</td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editVerse({{ $verse->id }})" class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.verses.edit') }}
                                    </button>
                                    <button type="button" wire:click="deleteVerse({{ $verse->id }})" class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.verses.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($verses->hasPages())
                <div class="panel-pagination">
                    {{ $verses->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
