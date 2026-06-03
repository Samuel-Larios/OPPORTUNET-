<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <article class="panel-card">
        <div class="panel-card-head">
            <h2>{{ __('admin.pages.settings') }}</h2>
            <p>{{ __('admin.settings.intro') }}</p>
        </div>

        <form wire:submit="save" class="panel-stack">
            @foreach ($groupedSettings as $group => $settings)
                <section class="panel-settings-group">
                    <div class="panel-settings-head">
                        <strong>{{ ucfirst($group) }}</strong>
                    </div>

                    <div class="panel-settings-grid">
                        @foreach ($settings as $setting)
                            <div class="panel-card panel-card-soft">
                                <strong>{{ $setting['label'] }}</strong>
                                <label class="panel-field">
                                    <span>{{ __('admin.settings.french') }}</span>
                                    <textarea wire:model="settings.{{ $setting['id'] }}.valeur_fr" rows="2"></textarea>
                                </label>
                                <label class="panel-field">
                                    <span>{{ __('admin.settings.english') }}</span>
                                    <textarea wire:model="settings.{{ $setting['id'] }}.valeur_en" rows="2"></textarea>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="panel-action-row">
                <button type="submit" class="panel-primary-btn">{{ __('admin.settings.save') }}</button>
            </div>
        </form>
    </article>
</div>
