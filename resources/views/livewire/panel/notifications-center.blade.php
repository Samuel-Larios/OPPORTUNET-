<div class="panel-stack">
    <section class="panel-card">
        <div class="panel-card-head">
            <h2>{{ __('admin.notifications.title') }}</h2>
            <div class="panel-action-row">
                @if ($unreadCount > 0)
                    <button type="button" wire:click="markAllAsRead" class="panel-secondary-btn panel-small-btn">
                        {{ __('admin.notifications.mark_all_read') }}
                    </button>
                @endif
            </div>
        </div>

        <div class="panel-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.notifications.search') }}" />
            <select wire:model.live="filter">
                <option value="all">{{ __('admin.notifications.filters.all') }}</option>
                <option value="unread">{{ __('admin.notifications.filters.unread') }}</option>
            </select>
        </div>

        <div class="panel-thread panel-notifications-list">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $level = $data['level'] ?? 'info';
                @endphp
                <article class="panel-message panel-notification-item is-{{ $level }}{{ $notification->read_at === null ? ' is-unread' : '' }}">
                    <div class="panel-message-head">
                        <strong>{{ $data['title'] ?? __('admin.notifications.fallback_title') }}</strong>
                        <span>{{ $notification->created_at?->format('d/m/Y H:i') }}</span>
                    </div>

                    <p>{{ $data['message'] ?? '' }}</p>

                    <div class="panel-notification-meta">
                        <span class="panel-badge{{ $notification->read_at === null ? '' : ' is-muted' }}">
                            {{ $notification->read_at === null ? __('admin.notifications.unread') : __('admin.notifications.read') }}
                        </span>
                        @if (! empty($data['category']))
                            <span class="panel-badge is-muted">{{ __('admin.notifications.categories.' . $data['category']) }}</span>
                        @endif
                    </div>

                    <div class="panel-action-row">
                        @if (! empty($data['action_url']))
                            <a href="{{ route('panel.notifications.open', $notification) }}" class="panel-primary-btn panel-small-btn">
                                {{ $data['action_label'] ?? __('admin.notifications.open') }}
                            </a>
                        @endif

                        @if ($notification->read_at === null)
                            <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="panel-secondary-btn panel-small-btn">
                                {{ __('admin.notifications.mark_read') }}
                            </button>
                        @endif

                        <button type="button" wire:click="deleteNotification('{{ $notification->id }}')" class="panel-secondary-btn panel-small-btn">
                            {{ __('admin.notifications.delete') }}
                        </button>
                    </div>
                </article>
            @empty
                <p class="panel-empty">{{ __('admin.notifications.empty') }}</p>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="panel-pagination">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
</div>
