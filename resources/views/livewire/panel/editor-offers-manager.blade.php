<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.offers.title') }}</h2>
            </div>

            @if ($isCompanyUser)
                <p class="panel-section-intro">{{ __('admin.offers.company_intro') }}</p>
            @endif

            <form wire:submit="saveOffer" class="panel-form-grid">
                <label class="panel-field">
                    <span>FR titre</span>
                    <input type="text" wire:model="titreFr" />
                    @error('titreFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN title</span>
                    <input type="text" wire:model="titreEn" />
                    @error('titreEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.offers.organization') }}</span>
                    <input type="text" wire:model="organisation" />
                    @error('organisation') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>Categorie</span>
                    <select wire:model="categorieId">
                        <option value="">Choisir</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nom }}</option>
                        @endforeach
                    </select>
                    @error('categorieId') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>Type</span>
                    <select wire:model="type">
                        @foreach (__('offers.types') as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>Contrat</span>
                    <select wire:model="contrat">
                        @foreach (__('offers.contracts') as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('contrat') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>Lieu</span>
                    <input type="text" wire:model="lieu" />
                </label>

                <label class="panel-field">
                    <span>Pays</span>
                    <input type="text" wire:model="pays" />
                </label>

                <label class="panel-field panel-field-span">
                    <span>Description FR</span>
                    <textarea wire:model="descriptionFr" rows="4"></textarea>
                    @error('descriptionFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Description EN</span>
                    <textarea wire:model="descriptionEn" rows="4"></textarea>
                    @error('descriptionEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Profil FR</span>
                    <textarea wire:model="profilFr" rows="3"></textarea>
                </label>

                <label class="panel-field panel-field-span">
                    <span>Profil EN</span>
                    <textarea wire:model="profilEn" rows="3"></textarea>
                </label>

                <label class="panel-field panel-field-span">
                    <span>Avantages FR</span>
                    <textarea wire:model="avantagesFr" rows="3"></textarea>
                </label>

                <label class="panel-field panel-field-span">
                    <span>Avantages EN</span>
                    <textarea wire:model="avantagesEn" rows="3"></textarea>
                </label>

                <label class="panel-field">
                    <span>Lien candidature</span>
                    <input type="url" wire:model="lienCandidature" />
                    @error('lienCandidature') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>Email candidature</span>
                    <input type="email" wire:model="emailCandidature" />
                    @error('emailCandidature') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.offers.published') }}</span>
                    <input type="date" wire:model="datePublication" />
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.offers.deadline') }}</span>
                    <input type="date" wire:model="dateExpiration" />
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.offers.status') }}</span>
                    <select wire:model="statut">
                        @foreach ($statusOptions as $statusOption)
                            <option value="{{ $statusOption }}">{{ __('admin.offers.statuses.' . $statusOption) }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="teletravail" />
                        <span>Teletravail</span>
                    </label>
                    @unless ($isCompanyUser)
                        <label class="panel-checkline">
                            <input type="checkbox" wire:model="enVedette" />
                            <span>{{ __('admin.offers.featured') }}</span>
                        </label>
                        <label class="panel-checkline">
                            <input type="checkbox" wire:model="urgent" />
                            <span>{{ __('admin.offers.urgent') }}</span>
                        </label>
                    @endunless
                </div>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.offers.save') }}</button>
                    <button type="button" wire:click="resetForm" class="panel-secondary-btn">{{ __('admin.offers.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.offers.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.offers.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.offers.all_statuses') }}</option>
                    @foreach ($statusFilterOptions as $statusOption)
                        <option value="{{ $statusOption }}">{{ __('admin.offers.statuses.' . $statusOption) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>{{ __('admin.offers.organization') }}</th>
                            <th>{{ __('admin.offers.status') }}</th>
                            <th>{{ __('admin.offers.deadline') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offers as $offer)
                            <tr>
                                <td>
                                    <strong>{{ $offer->titre }}</strong>
                                    <span>{{ $offer->type }}</span>
                                </td>
                                <td>{{ $offer->organisation ?: '-' }}</td>
                                <td>{{ __('admin.offers.statuses.' . $offer->statut) }}</td>
                                <td>{{ $offer->date_expiration?->format('d/m/Y') ?: '-' }}</td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editOffer({{ $offer->id }})" class="panel-secondary-btn panel-small-btn">{{ __('admin.offers.edit') }}</button>
                                    <button type="button" wire:click="deleteOffer({{ $offer->id }})" class="panel-secondary-btn panel-small-btn">{{ __('admin.offers.delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($offers->hasPages())
                <div class="panel-pagination">
                    {{ $offers->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
