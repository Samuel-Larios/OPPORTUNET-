@php
    $isFrench = app()->getLocale() === 'fr';
@endphp

<div class="panel-stack" wire:poll.30s>
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ $isFrench ? 'Créer une infolettre' : 'Create Newsletter' }}</h2>
                <p>{{ $isFrench ? 'Programmez une infolettre pour être envoyée automatiquement.' : 'Schedule a newsletter to be sent automatically.' }}
                </p>
            </div>

            <form wire:submit="saveNewsletter" class="panel-form-grid">
                <label class="panel-field panel-field-span">
                    <span>{{ $isFrench ? 'Sujet' : 'Subject' }}</span>
                    <input type="text" wire:model="subject" />
                    @error('subject')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Type de contenu' : 'Content Type' }}</span>
                    <input type="text" wire:model="contentType"
                        placeholder="{{ $isFrench ? 'Ex: article, offre, etc.' : 'E.g. article, offer, etc.' }}" />
                    @error('contentType')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ $isFrench ? 'Titre du contenu' : 'Content Title' }}</span>
                    <input type="text" wire:model="contentTitle" />
                    @error('contentTitle')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Audience' : 'Audience' }}</span>
                    <select wire:model="audience">
                        <option value="platform_users_and_subscribers">
                            {{ $isFrench ? 'Utilisateurs et abonnés' : 'Users and subscribers' }}</option>
                        <option value="subscribers_only">{{ $isFrench ? 'Abonnés uniquement' : 'Subscribers only' }}
                        </option>
                        <option value="users_only">{{ $isFrench ? 'Utilisateurs uniquement' : 'Users only' }}</option>
                    </select>
                    @error('audience')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ $isFrench ? 'Statut' : 'Status' }}</span>
                    <select wire:model="status" @if ($scheduleEnabled) disabled @endif>
                        <option value="draft">{{ $isFrench ? 'Brouillon' : 'Draft' }}</option>
                        <option value="sent">{{ $isFrench ? 'Envoyé' : 'Sent' }}</option>
                        <option value="failed">{{ $isFrench ? 'Échoué' : 'Failed' }}</option>
                    </select>
                    @error('status')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                @include('livewire.panel.partials.schedule-fields')

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ $isFrench ? 'Enregistrer' : 'Save' }}</button>
                    <button type="button" wire:click="resetForm"
                        class="panel-secondary-btn">{{ $isFrench ? 'Nouveau' : 'New' }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ $isFrench ? 'Infos lettres' : 'Newsletters' }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ $isFrench ? 'Rechercher...' : 'Search...' }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ $isFrench ? 'Tous les statuts' : 'All statuses' }}</option>
                    <option value="draft">{{ $isFrench ? 'Brouillon' : 'Draft' }}</option>
                    <option value="scheduled">{{ $isFrench ? 'Programmé' : 'Scheduled' }}</option>
                    <option value="sent">{{ $isFrench ? 'Envoyé' : 'Sent' }}</option>
                    <option value="failed">{{ $isFrench ? 'Échoué' : 'Failed' }}</option>
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($newsletters as $newsletter)
                    <button type="button" wire:click="editNewsletter({{ $newsletter->id }})"
                        class="panel-application-item{{ $selectedNewsletter && $selectedNewsletter->id === $newsletter->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $newsletter->subject }}</strong>
                            <span>{{ $newsletter->content_title }}</span>
                            <span>{{ $newsletter->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <span
                            class="panel-badge{{ $newsletter->status === 'sent' ? ' is-success' : ($newsletter->status === 'scheduled' ? ' is-warning' : ' is-muted') }}">
                            {{ $newsletter->status }}
                        </span>
                    </button>
                @empty
                    <p class="panel-empty">{{ $isFrench ? 'Aucune infolettre' : 'No newsletters' }}</p>
                @endforelse
            </div>

            @if ($newsletters->hasPages())
                <div class="panel-pagination">
                    {{ $newsletters->links() }}
                </div>
            @endif
        </article>
    </section>

    @if ($selectedNewsletter)
        <article class="panel-card" style="margin-top: 24px;">
            <div class="panel-card-head">
                <h2>{{ $isFrench ? 'Détails de l\'infolettre' : 'Newsletter Details' }}</h2>
            </div>

            <div class="panel-application-detail">
                <div class="panel-application-meta">
                    <div>
                        <span>{{ $isFrench ? 'Sujet' : 'Subject' }}</span>
                        <strong>{{ $selectedNewsletter->subject }}</strong>
                    </div>
                    <div>
                        <span>{{ $isFrench ? 'Statut' : 'Status' }}</span>
                        <strong>{{ $selectedNewsletter->status }}</strong>
                    </div>
                    <div>
                        <span>{{ $isFrench ? 'Audience' : 'Audience' }}</span>
                        <strong>{{ $selectedNewsletter->audience }}</strong>
                    </div>
                    <div>
                        <span>{{ $isFrench ? 'Créée le' : 'Created' }}</span>
                        <strong>{{ $selectedNewsletter->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if ($selectedNewsletter->sent_at)
                        <div>
                            <span>{{ $isFrench ? 'Envoyée le' : 'Sent' }}</span>
                            <strong>{{ $selectedNewsletter->sent_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                    @if ($selectedNewsletter->auto_publish && $selectedNewsletter->scheduled_for)
                        <div>
                            <span>{{ $isFrench ? 'Programmée pour' : 'Scheduled for' }}</span>
                            <strong>{{ $selectedNewsletter->scheduled_for->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="panel-action-row">
                    <button type="button" wire:click="deleteNewsletter({{ $selectedNewsletter->id }})"
                        class="panel-secondary-btn">
                        {{ $isFrench ? 'Supprimer' : 'Delete' }}
                    </button>
                </div>
            </div>
        </article>
    @endif
</div>
