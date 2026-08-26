@props(['model', 'label', 'limit' => 20000])

{{-- The editable area is ignored by Livewire, so its HTML is explicitly synced. --}}
<div class="panel-field panel-field-span" x-data="{
    value: $wire.entangle('{{ $model }}').live,
    sync() {
        const content = this.$refs.editor.innerHTML;
        this.value = content;
        this.$refs.input.value = content;
        this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
    },
    get characterCount() {
        const element = document.createElement('div');
        element.innerHTML = this.value || '';
        return (element.textContent || '').length;
    }
}">
    <span>{{ $label }}</span>
    <textarea x-ref="input" wire:model.live="{{ $model }}" class="rich-text-editor__sync-input"
        aria-hidden="true" tabindex="-1"></textarea>
    <div class="rich-text-editor" wire:ignore x-effect="if (document.activeElement !== $refs.editor && $refs.editor.innerHTML !== (value || '')) { $refs.editor.innerHTML = value || '' }">
        <div class="rich-text-editor__toolbar" role="toolbar" aria-label="Mise en forme du texte">
            <button type="button" @click="$refs.editor.focus(); document.execCommand('bold'); sync()"><strong>G</strong></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('italic'); sync()"><em>I</em></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('underline'); sync()"><u>S</u></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('formatBlock', false, 'h2'); sync()">Titre</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('insertUnorderedList'); sync()">• Liste</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('insertOrderedList'); sync()">1. Liste</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyLeft'); sync()">↤</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyCenter'); sync()">↔</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyRight'); sync()">↦</button>
        </div>
        <div x-ref="editor" contenteditable="true" class="rich-text-editor__content" @input="sync()" @paste="$nextTick(() => sync())"></div>
    </div>
    <small>Utilisez les boutons pour mettre en forme le texte avant publication.</small>
    <small class="character-counter" x-text="characterCount + ' / {{ $limit }}'"></small>
    @error($model)<small>{{ $message }}</small>@enderror
</div>

<style>
    .rich-text-editor { border: 1px solid #cbd5e1; border-radius: 10px; overflow: hidden; background: #fff; }
    .rich-text-editor__toolbar { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; }
    .rich-text-editor__toolbar button { border: 1px solid #cbd5e1; background: #fff; border-radius: 5px; padding: 5px 9px; cursor: pointer; }
    .rich-text-editor__content { min-height: 210px; padding: 12px; outline: none; line-height: 1.65; }
    .rich-text-editor__sync-input { position: absolute; width: 1px; height: 1px; padding: 0; border: 0; opacity: 0; pointer-events: none; }
    .rich-text-editor__content:empty::before { content: 'Saisissez le contenu de l’article…'; color: #94a3b8; }
</style>
