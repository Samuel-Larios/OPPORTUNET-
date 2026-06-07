<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-stats-grid">
        <article class="panel-stat-card">
            <span>{{ __('admin.security.stats.active_blocks') }}</span>
            <strong>{{ $stats['active_blocks'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.security.stats.incidents_24h') }}</span>
            <strong>{{ $stats['incidents_24h'] }}</strong>
        </article>
        <article class="panel-stat-card">
            <span>{{ __('admin.security.stats.critical_24h') }}</span>
            <strong>{{ $stats['critical_24h'] }}</strong>
        </article>
    </section>

    <section class="panel-grid-3">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.security.manual_block.title') }}</h2>
                <p>{{ __('admin.security.manual_block.intro') }}</p>
            </div>

            <form wire:submit="blockManualIp" class="panel-form-grid">
                <label class="panel-field">
                    <span>{{ __('admin.security.manual_block.ip') }}</span>
                    <input type="text" wire:model="manualIpAddress" />
                </label>
                <label class="panel-field">
                    <span>{{ __('admin.security.manual_block.reason') }}</span>
                    <input type="text" wire:model="manualReason" />
                </label>
                <div class="panel-action-row">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.security.manual_block.submit') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card" style="grid-column: span 2;">
            <div class="panel-card-head">
                <h2>{{ __('admin.security.blocks.title') }}</h2>
                <p>{{ __('admin.security.blocks.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($activeBlocks as $block)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $block->ip_address }}</strong>
                            <span>{{ $block->reason }}</span>
                            <span>
                                {{ $block->is_manual ? __('admin.security.blocks.manual') : __('admin.security.blocks.automatic') }}
                                @if ($block->blocked_until)
                                    · {{ __('admin.security.blocks.until', ['date' => $block->blocked_until->format('d/m/Y H:i')]) }}
                                @endif
                            </span>
                        </div>
                        <button type="button" wire:click="unblockIp({{ $block->id }})" class="panel-secondary-btn panel-small-btn">
                            {{ __('admin.security.blocks.unblock') }}
                        </button>
                    </div>
                @empty
                    <p class="panel-empty">{{ __('admin.security.blocks.empty') }}</p>
                @endforelse
            </div>
        </article>
    </section>

    <article class="panel-card">
        <div class="panel-card-head">
            <h2>{{ __('admin.security.incidents.title') }}</h2>
            <p>{{ __('admin.security.incidents.intro') }}</p>
        </div>

        <div class="panel-list">
            @forelse ($recentIncidents as $incident)
                <div class="panel-list-row">
                    <div>
                        <strong>{{ $incident->ip_address ?: __('admin.security.incidents.unknown_ip') }}</strong>
                        <span>{{ $incident->type }} · {{ $incident->reason }}</span>
                        <span>{{ $incident->method }} {{ $incident->path }} · {{ optional($incident->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <span class="panel-badge{{ $incident->severity === 'critical' ? ' is-danger' : '' }}">{{ $incident->severity }}</span>
                        @if ($incident->ip_address)
                            <button type="button" wire:click="blockIp('{{ $incident->ip_address }}')" class="panel-secondary-btn panel-small-btn">
                                {{ __('admin.security.incidents.block_ip') }}
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="panel-empty">{{ __('admin.security.incidents.empty') }}</p>
            @endforelse
        </div>

        @if ($recentIncidents->hasPages())
            <div class="panel-pagination">
                {{ $recentIncidents->links() }}
            </div>
        @endif
    </article>
</div>
