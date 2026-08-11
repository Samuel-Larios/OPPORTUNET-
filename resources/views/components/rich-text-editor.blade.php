@props(['model', 'label'])

<div class="panel-field panel-field-span" x-data="{ value: $wire.entangle('{{ $model }}').live }">
    <span>{{ $label }}</span>
    <div class="rich-text-editor" x-effect="if (document.activeElement !== $refs.editor && $refs.editor.innerHTML !== (value || '')) { $refs.editor.innerHTML = value || '' }">
        <div class="rich-text-editor__toolbar" role="toolbar" aria-label="Mise en forme du texte">
            <button type="button" @click="$refs.editor.focus(); document.execCommand('bold'); value = $refs.editor.innerHTML"><strong>G</strong></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('italic'); value = $refs.editor.innerHTML"><em>I</em></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('underline'); value = $refs.editor.innerHTML"><u>S</u></button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('formatBlock', false, 'h2'); value = $refs.editor.innerHTML">Titre</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('insertUnorderedList'); value = $refs.editor.innerHTML">• Liste</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('insertOrderedList'); value = $refs.editor.innerHTML">1. Liste</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyLeft'); value = $refs.editor.innerHTML">↤</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyCenter'); value = $refs.editor.innerHTML">↔</button>
            <button type="button" @click="$refs.editor.focus(); document.execCommand('justifyRight'); value = $refs.editor.innerHTML">↦</button>
        </div>
        <div x-ref="editor" contenteditable="true" class="rich-text-editor__content" @input="value = $refs.editor.innerHTML"></div>
    </div>
    <small>Utilisez les boutons pour mettre en forme le texte avant publication.</small>
    @error($model)<small>{{ $message }}</small>@enderror
</div>

<style>
    .rich-text-editor { border: 1px solid #cbd5e1; border-radius: 10px; overflow: hidden; background: #fff; }
    .rich-text-editor__toolbar { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; }
    .rich-text-editor__toolbar button { border: 1px solid #cbd5e1; background: #fff; border-radius: 5px; padding: 5px 9px; cursor: pointer; }
    .rich-text-editor__content { min-height: 210px; padding: 12px; outline: none; line-height: 1.65; }
    .rich-text-editor__content:empty::before { content: 'Saisissez le contenu de l’article…'; color: #94a3b8; }
</style>
