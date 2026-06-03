<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.article_comments.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.article_comments.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.article_comments.all_statuses') }}</option>
                    @foreach (__('admin.article_comments.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="articleFilter">
                    <option value="">{{ __('admin.article_comments.all_articles') }}</option>
                    @foreach ($articles as $article)
                        <option value="{{ $article->id }}">{{ $article->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($comments as $comment)
                    <button type="button" wire:click="selectComment({{ $comment->id }})" class="panel-application-item{{ $selectedComment && $selectedComment->id === $comment->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $comment->authorLabel() }}</strong>
                            <span>{{ $comment->article?->titre ?: __('admin.article_comments.labels.article') }}</span>
                            <span>{{ \Illuminate\Support\Str::limit($comment->contenu, 90) }}</span>
                        </div>
                        <span class="panel-badge">{{ $comment->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.article_comments.empty') }}</p>
                @endforelse
            </div>

            @if ($comments->hasPages())
                <div class="panel-pagination">
                    {{ $comments->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedComment)
                <div class="panel-card-head">
                    <h2>{{ __('admin.article_comments.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.article_comments.labels.article') }}</span>
                            <strong>{{ $selectedComment->article?->titre ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.article_comments.labels.author') }}</span>
                            <strong>{{ $selectedComment->auteur_nom }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.article_comments.labels.email') }}</span>
                            <strong>{{ $selectedComment->auteur_email ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.article_comments.labels.submitted') }}</span>
                            <strong>{{ $selectedComment->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.article_comments.labels.parent') }}</span>
                            <strong>{{ $selectedComment->parent?->auteur_nom ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.article_comments.labels.account') }}</span>
                            <strong>{{ $selectedComment->user?->fullName() ?: '-' }}</strong>
                        </div>
                    </div>

                    <form wire:submit="updateComment" class="panel-form-grid">
                        <label class="panel-field">
                            <span>{{ __('admin.article_comments.labels.author') }}</span>
                            <input type="text" wire:model="authorName" />
                            @error('authorName') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.article_comments.labels.email') }}</span>
                            <input type="email" wire:model="authorEmail" />
                            @error('authorEmail') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.article_comments.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.article_comments.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.article_comments.labels.content') }}</span>
                            <textarea wire:model="content" rows="6"></textarea>
                            @error('content') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.article_comments.process') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.article_comments.empty') }}</p>
            @endif
        </article>
    </section>
</div>
