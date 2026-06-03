<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.categories.title') }}</h2>
                <p>{{ __('admin.categories.intro') }}</p>
            </div>

            <form wire:submit="saveCategory" class="panel-form-grid">
                <label class="panel-field">
                    <span>{{ __('admin.categories.type') }}</span>
                    <select wire:model="type">
                        @foreach ($categoryTypes as $categoryType)
                            <option value="{{ $categoryType }}">{{ __('admin.categories.types.' . $categoryType) }}</option>
                        @endforeach
                    </select>
                    @error('type') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.categories.slug') }}</span>
                    <input type="text" value="{{ $slug }}" readonly disabled />
                    <small>{{ __('admin.categories.slug_hint') }}</small>
                    @error('slug') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>FR nom</span>
                    <input type="text" wire:model.live.debounce.250ms="nomFr" />
                    @error('nomFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN name</span>
                    <input type="text" wire:model="nomEn" />
                    @error('nomEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.categories.icon') }}</span>
                    <input type="text" wire:model="icone" placeholder="briefcase" />
                    @error('icone') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.categories.color') }}</span>
                    <input type="text" wire:model="couleur" placeholder="#1A7A6E" />
                    @error('couleur') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR description</span>
                    <textarea wire:model="descriptionFr" rows="4"></textarea>
                    @error('descriptionFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN description</span>
                    <textarea wire:model="descriptionEn" rows="4"></textarea>
                    @error('descriptionEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.categories.order') }}</span>
                    <input type="number" min="0" wire:model="ordre" />
                    @error('ordre') <small>{{ $message }}</small> @enderror
                </label>

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="actif" />
                        <span>{{ __('admin.users.active') }}</span>
                    </label>
                </div>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.categories.save') }}</button>
                    <button type="button" wire:click="resetForm" class="panel-secondary-btn">{{ __('admin.categories.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.categories.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.categories.search') }}" />

                <select wire:model.live="typeFilter">
                    <option value="">{{ __('admin.categories.all_types') }}</option>
                    @foreach ($categoryTypes as $categoryType)
                        <option value="{{ $categoryType }}">{{ __('admin.categories.types.' . $categoryType) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="activeFilter">
                    <option value="">{{ __('admin.categories.all_statuses') }}</option>
                    <option value="1">{{ __('admin.users.active') }}</option>
                    <option value="0">{{ __('admin.users.inactive') }}</option>
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.categories.category') }}</th>
                            <th>{{ __('admin.categories.type') }}</th>
                            <th>{{ __('admin.categories.slug') }}</th>
                            <th>{{ __('admin.categories.status') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->nom }}</strong>
                                    <span>{{ $category->couleur ?: '-' }}</span>
                                </td>
                                <td>{{ __('admin.categories.types.' . $category->type) }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    <span class="panel-badge{{ $category->actif ? ' is-success' : ' is-muted' }}">
                                        {{ $category->actif ? __('admin.users.active') : __('admin.users.inactive') }}
                                    </span>
                                </td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editCategory({{ $category->id }})" class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.categories.edit') }}
                                    </button>
                                    <button type="button" wire:click="deleteCategory({{ $category->id }})" class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.categories.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="panel-pagination">
                    {{ $categories->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
