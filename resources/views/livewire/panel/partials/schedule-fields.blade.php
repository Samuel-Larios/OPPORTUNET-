@props([
    'spanClass' => 'panel-field-span',
])

@php
    $isFrench = app()->getLocale() === 'fr';
@endphp

<div class="{{ $spanClass }}"
    style="display: grid; gap: 14px; padding: 18px; border: 1px solid rgba(15, 90, 131, 0.12); border-radius: 18px; background: rgba(247, 251, 253, 0.92);">
    <div style="display: grid; gap: 6px;">
        <strong>{{ $isFrench ? 'Publication programmée' : 'Scheduled publishing' }}</strong>
        <span
            style="color: #607788; font-size: 0.95rem;">{{ $isFrench ? 'Le contenu est enregistré maintenant, mais il reste masqué jusqu’à la date choisie.' : 'The content is saved now, but it stays hidden until the chosen date.' }}</span>
    </div>

    <label class="panel-checkline">
        <input type="checkbox" wire:model.live="scheduleEnabled" />
        <span>{{ $isFrench ? 'Programmer la publication automatique' : 'Schedule automatic publishing' }}</span>
    </label>

    @if ($scheduleEnabled)
        <label class="panel-field">
            <span>{{ $isFrench ? 'Publier le' : 'Publish on' }}</span>
            <input type="datetime-local" wire:model.live="scheduleAt" />
            @error('scheduleAt')
                <small>{{ $message }}</small>
            @enderror
        </label>
    @endif
</div>
