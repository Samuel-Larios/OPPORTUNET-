<x-layouts.panel :title="app()->getLocale() === 'fr' ? 'Prières du jour' : 'Daily prayers'">
    <livewire:panel.spiritual-publications-manager type="priere_jour" />
</x-layouts.panel>
