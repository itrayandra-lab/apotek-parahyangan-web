@props(['id', 'name', 'value' => '', 'placeholder' => 'Start writing...'])

@once
@push('styles')
<style>
    /* Beautylatory Trix Theme - Rose & Glass Morphism */
    trix-toolbar .trix-button--icon {
        color: #525252;
    }

    trix-toolbar .trix-button.trix-active {
        color: #B76E79;
        background-color: rgba(183, 110, 121, 0.1);
    }

    trix-editor {
        border: none !important;
        padding: 1.5rem !important;
        font-family: 'Inter', sans-serif;
        color: #1A1A1A;
        min-height: 400px;
    }

    trix-editor ol,
    .trix-content ol {
        list-style: decimal outside;
        padding-left: 1.5rem;
    }

    trix-editor ol li,
    .trix-content ol li {
        list-style-type: decimal;
    }

    trix-editor:focus {
        outline: none !important;
    }

    trix-toolbar {
        border-bottom: 1px solid #f3f4f6 !important;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 1rem 1rem 0 0;
        padding: 0.75rem !important;
    }
</style>
@endpush
@endonce

<div class="glass-panel rounded-3xl p-1 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
    />

    <trix-editor
        input="{{ $id }}"
        class="trix-content flex-1 bg-white/30 backdrop-blur-sm"
        placeholder="{{ $placeholder }}"
        {{ $attributes }}
    ></trix-editor>
</div>
