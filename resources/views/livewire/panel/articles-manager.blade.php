<div class="panel-stack" wire:poll.30s>
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.articles.title') }}</h2>
                <p>{{ __('admin.articles.images_hint') }}</p>
            </div>

            <form wire:submit="saveArticle" class="panel-form-grid" enctype="multipart/form-data">
                <label class="panel-field">
                    <span>Titre FR</span>
                    <input type="text" wire:model="titreFr" />
                    @error('titreFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Title EN</span>
                    <input type="text" wire:model="titreEn" />
                    @error('titreEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.articles.category') }}</span>
                    <select wire:model="categorieId">
                        <option value="">{{ __('admin.articles.category_placeholder') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nom }}</option>
                        @endforeach
                    </select>
                    @error('categorieId')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.articles.status') }}</span>
                    <select wire:model="statut">
                        <option value="brouillon">{{ __('admin.articles.statuses.brouillon') }}</option>
                        <option value="publie">{{ __('admin.articles.statuses.publie') }}</option>
                        <option value="archive">{{ __('admin.articles.statuses.archive') }}</option>
                    </select>
                    @error('statut')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Extrait FR</span>
                    <textarea wire:model="extraitFr" rows="3"></textarea>
                    @error('extraitFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Excerpt EN</span>
                    <textarea wire:model="extraitEn" rows="3"></textarea>
                    @error('extraitEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Contenu FR</span>
                    <textarea wire:model="contenuFr" rows="7"></textarea>
                    @error('contenuFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Content EN</span>
                    <textarea wire:model="contenuEn" rows="7"></textarea>
                    @error('contenuEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Méta-titre FR</span>
                    <input type="text" wire:model="metaTitreFr" />
                </label>

                <label class="panel-field">
                    <span>Meta title EN</span>
                    <input type="text" wire:model="metaTitreEn" />
                </label>

                <label class="panel-field panel-field-span">
                    <span>Méta-description FR</span>
                    <textarea wire:model="metaDescriptionFr" rows="3"></textarea>
                </label>

                <label class="panel-field panel-field-span">
                    <span>Meta description EN</span>
                    <textarea wire:model="metaDescriptionEn" rows="3"></textarea>
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.articles.tags') }}</span>
                    <input type="text" wire:model="tags"
                        placeholder="{{ __('admin.articles.tags_placeholder') }}" />
                    @error('tags')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.articles.reading_time') }}</span>
                    <input type="text" wire:model="tempsLecture" placeholder="5 min" />
                    @error('tempsLecture')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.articles.published') }}</span>
                    <input type="date" wire:model="datePublication" />
                    @error('datePublication')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="enVedette" />
                        <span>{{ __('admin.articles.featured_article') }}</span>
                    </label>
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="commentairesActifs" />
                        <span>{{ __('admin.articles.comments_enabled') }}</span>
                    </label>
                </div>

                @include('livewire.panel.partials.schedule-fields')

                <div class="panel-field panel-field-span">
                    <span>{{ __('admin.articles.images') }}</span>
                    <input type="file" wire:model="newImages" multiple accept="image/*" />
                    <small>{{ __('admin.articles.images_hint') }}</small>
                    @error('newImages')
                        <small>{{ $message }}</small>
                    @enderror
                    @error('newImages.*')
                        <small>{{ $message }}</small>
                    @enderror
                    @error('featuredImageSelection')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                @if ($existingImages !== [])
                    <div class="panel-field panel-field-span">
                        <span>{{ __('admin.articles.current_images') }}</span>
                        <div class="panel-image-grid">
                            @foreach ($existingImages as $existingImage)
                                <article class="panel-image-card" wire:key="existing-image-{{ $existingImage['id'] }}">
                                    <img src="{{ $existingImage['url'] }}" alt=""
                                        class="panel-image-preview" />

                                    <label class="panel-checkline">
                                        <input type="radio" wire:model="featuredImageSelection"
                                            value="existing:{{ $existingImage['id'] }}" />
                                        <span>{{ __('admin.articles.featured_image') }}</span>
                                    </label>

                                    <label class="panel-field">
                                        <span>Texte alternatif FR</span>
                                        <input type="text"
                                            wire:model="existingImageAltsFr.{{ $existingImage['id'] }}" />
                                    </label>

                                    <label class="panel-field">
                                        <span>Alt text EN</span>
                                        <input type="text"
                                            wire:model="existingImageAltsEn.{{ $existingImage['id'] }}" />
                                    </label>

                                    <button type="button" wire:click="removeExistingImage({{ $existingImage['id'] }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.articles.remove_image') }}
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($newImages !== [])
                    <div class="panel-field panel-field-span">
                        <span>{{ __('admin.articles.new_images') }}</span>
                        <div class="panel-image-grid">
                            @foreach ($newImages as $index => $newImage)
                                <article class="panel-image-card" wire:key="new-image-{{ $index }}">
                                    <img src="{{ $newImage->temporaryUrl() }}" alt=""
                                        class="panel-image-preview" />

                                    <label class="panel-checkline">
                                        <input type="radio" wire:model="featuredImageSelection"
                                            value="new:{{ $index }}" />
                                        <span>{{ __('admin.articles.featured_image') }}</span>
                                    </label>

                                    <label class="panel-field">
                                        <span>Texte alternatif FR</span>
                                        <input type="text" wire:model="newImageAltsFr.{{ $index }}" />
                                    </label>

                                    <label class="panel-field">
                                        <span>Alt text EN</span>
                                        <input type="text" wire:model="newImageAltsEn.{{ $index }}" />
                                    </label>

                                    <button type="button" wire:click="removeNewImage({{ $index }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.articles.remove_image') }}
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.articles.save') }}</button>
                    <button type="button" wire:click="resetForm"
                        class="panel-secondary-btn">{{ __('admin.articles.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.articles.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admin.articles.search') }}" />

                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.articles.all_statuses') }}</option>
                    <option value="brouillon">{{ __('admin.articles.statuses.brouillon') }}</option>
                    <option value="publie">{{ __('admin.articles.statuses.publie') }}</option>
                    <option value="archive">{{ __('admin.articles.statuses.archive') }}</option>
                </select>

                <select wire:model.live="categoryFilter">
                    <option value="">{{ __('admin.articles.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}">{{ $category->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.articles.article') }}</th>
                            <th>{{ __('admin.articles.category') }}</th>
                            <th>{{ __('admin.articles.status') }}</th>
                            <th>{{ __('admin.articles.published') }}</th>
                            <th>{{ __('admin.articles.images_count') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>
                                    <strong>{{ $article->titre }}</strong>
                                    <span>{{ $article->publie_le?->format('d/m/Y') ?: '-' }}</span>
                                </td>
                                <td>{{ $article->category?->nom ?: '-' }}</td>
                                <td>{{ __('admin.articles.statuses.' . $article->statut) }}</td>
                                <td>
                                    @if ($article->auto_publish && $article->scheduled_for)
                                        <span class="panel-badge">
                                            {{ app()->getLocale() === 'fr' ? 'Programmé le ' : 'Scheduled on ' }}{{ $article->scheduled_for->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        {{ $article->publie_le?->format('d/m/Y') ?: '-' }}
                                    @endif
                                </td>
                                <td>{{ $article->images->count() }}/5</td>
                                <td class="panel-inline-actions">
                                    <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                        rel="noopener" class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.articles.view') }}
                                    </a>
                                    <button type="button" wire:click="editArticle({{ $article->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.articles.edit') }}
                                    </button>
                                    <button type="button" wire:click="deleteArticle({{ $article->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.articles.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="panel-pagination">
                    {{ $articles->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
