@php
    $isFrench = app()->getLocale() === 'fr';
@endphp

<div class="panel-stack" wire:poll.30s>
    <style>
        /* Empêche l’envoi du formulaire en cas de clic clavier / focus */
        .panel-action-row button[type="submit"],
        .panel-primary-btn {
            pointer-events: auto;
        }
    </style>

    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ $typeLabel }}</h2>
                <p>{{ $isFrench ? 'Créez un contenu bilingue et décidez s’il doit apparaître sur la page d’accueil.' : 'Create bilingual content and decide whether it should appear on the home page.' }}
                </p>
            </div>

            <form wire:submit.prevent="savePublication" class="panel-form-grid">
                <label class="panel-field">
                    <span>{{ $isFrench ? 'Titre' : 'Title' }} FR</span>
                    <input type="text" wire:model="titleFr" />
                    @error('titleFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Title EN</span>
                    <input type="text" wire:model="titleEn" />
                    @error('titleEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ $isFrench ? 'Extrait' : 'Excerpt' }} FR</span>
                    <textarea rows="3" wire:model="excerptFr"></textarea>
                    @error('excerptFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Excerpt EN</span>
                    <textarea rows="3" wire:model="excerptEn"></textarea>
                    @error('excerptEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Référence' : 'Reference' }} FR</span>
                    <input type="text" wire:model="referenceFr" />
                    @error('referenceFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Reference EN</span>
                    <input type="text" wire:model="referenceEn" />
                    @error('referenceEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Auteur / source' : 'Author / source' }} FR</span>
                    <input type="text" wire:model="authorFr" />
                    @error('authorFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Author / source EN</span>
                    <input type="text" wire:model="authorEn" />
                    @error('authorEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ $isFrench ? 'Contenu' : 'Content' }} FR</span>
                    <textarea rows="7" wire:model="contentFr"></textarea>
                    @error('contentFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Content EN</span>
                    <textarea rows="7" wire:model="contentEn"></textarea>
                    @error('contentEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Ordre' : 'Order' }}</span>
                    <input type="number" min="0" wire:model="order" />
                    <small>{{ $isFrench ? "Les contenus avec l'ordre le plus faible apparaissent en premier." : 'Lower numbers are displayed first.' }}</small>
                    @error('order')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="active" />
                        <span>{{ $isFrench ? 'Actif' : 'Active' }}</span>
                    </label>
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="showOnHome" />
                        <span>{{ $isFrench ? "Afficher sur la page d'accueil" : 'Show on home page' }}</span>
                    </label>
                </div>

                @include('livewire.panel.partials.schedule-fields')

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ $isFrench ? 'Enregistrer' : 'Save' }}</button>
                    <button type="button" wire:click="resetForm"
                        class="panel-secondary-btn">{{ $isFrench ? 'Nouveau contenu' : 'New content' }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ $isFrench ? 'Contenus enregistrés' : 'Saved content' }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ $isFrench ? 'Rechercher un titre, un extrait ou une référence' : 'Search by title, excerpt, or reference' }}" />
                <select wire:model.live="activeFilter">
                    <option value="">{{ $isFrench ? 'Tous les statuts' : 'All statuses' }}</option>
                    <option value="1">{{ $isFrench ? 'Actif' : 'Active' }}</option>
                    <option value="0">{{ $isFrench ? 'Inactif' : 'Inactive' }}</option>
                </select>
                <select wire:model.live="homeFilter">
                    <option value="">
                        {{ $isFrench ? "Tous les états d'affichage sur l'accueil" : 'All home states' }}</option>
                    <option value="1">{{ $isFrench ? "Afficher sur la page d'accueil" : 'Show on home page' }}
                    </option>
                    <option value="0">{{ $isFrench ? "Masquer de la page d'accueil" : 'Hide from home page' }}
                    </option>
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ $isFrench ? 'Titre' : 'Title' }}</th>
                            <th>{{ $isFrench ? 'Référence' : 'Reference' }}</th>
                            <th>{{ $isFrench ? 'Visibilité' : 'Visibility' }}</th>
                            <th>{{ $isFrench ? 'Ordre' : 'Order' }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publications as $publication)
                            <tr>
                                <td>
                                    <strong>{{ $publication->titre }}</strong>
                                    <span>{{ \Illuminate\Support\Str::limit($publication->extrait ?: $publication->contenu, 90) }}</span>
                                </td>
                                <td>{{ $publication->reference ?: '—' }}</td>
                                <td>
                                    <div style="display: grid; gap: 6px;">
                                        <span
                                            class="panel-badge{{ $publication->actif ? ' is-success' : ' is-muted' }}">
                                            {{ $publication->actif ? ($isFrench ? 'Actif' : 'Active') : ($isFrench ? 'Inactif' : 'Inactive') }}
                                        </span>
                                        <span
                                            class="panel-badge{{ $publication->afficher_accueil ? '' : ' is-muted' }}">
                                            {{ $publication->afficher_accueil ? ($isFrench ? 'Accueil' : 'Home') : ($isFrench ? 'Hors accueil' : 'Not on home') }}
                                        </span>
                                        @if ($publication->auto_publish && $publication->scheduled_for)
                                            <span class="panel-badge">
                                                {{ $isFrench ? 'Programmé le ' : 'Scheduled on ' }}{{ $publication->scheduled_for->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $publication->ordre }}</td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editPublication({{ $publication->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ $isFrench ? 'Modifier' : 'Edit' }}
                                    </button>
                                    <button type="button" wire:click="deletePublication({{ $publication->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ $isFrench ? 'Supprimer' : 'Delete' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($publications->hasPages())
                <div class="panel-pagination">
                    {{ $publications->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
