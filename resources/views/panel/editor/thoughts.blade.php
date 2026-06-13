<x-layouts.panel :title="app()->getLocale() === 'fr' ? 'Pensées du jour' : 'Thoughts of the day'">
    <livewire:panel.spiritual-publications-manager type="pensee" />
</x-layouts.panel>
