@php
    $editorId = $id ?? 'rich-editor-' . uniqid();
    $fieldName = $name ?? 'content';
    $fieldValue = $value ?? '';
    $editorLabel = $label ?? 'Contenido';
    $editorMode = $mode ?? 'full';
    $editorHeight = $minHeight ?? '280px';
    $isRequired = $required ?? true;
@endphp

@once
    @push('styles')
        @include('admin.partials.rich_editor_styles')
    @endpush
    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            window.__richEditors = window.__richEditors || {};

            window.__richEditorToolbars = {
                full: [
                    [{ header: [3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean']
                ],
                basic: [
                    ['bold', 'italic'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean']
                ]
            };

            window.initRichEditor = function (config) {
                const textarea = document.getElementById(config.id);
                const mount = document.getElementById(config.id + '-mount');
                if (!textarea || !mount || window.__richEditors[config.id]) return;

                const quill = new Quill(mount, {
                    theme: 'snow',
                    modules: {
                        toolbar: window.__richEditorToolbars[config.mode] || window.__richEditorToolbars.full
                    },
                    placeholder: config.placeholder || 'Escriba aquí el contenido...'
                });

                if (config.initialHtml) {
                    quill.clipboard.dangerouslyPasteHTML(config.initialHtml);
                }

                const sync = () => {
                    const html = quill.root.innerHTML;
                    const empty = quill.getText().trim() === '';
                    textarea.value = empty ? '' : html;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    document.dispatchEvent(new CustomEvent('rich-editor:change', {
                        detail: { id: config.id, html: textarea.value, empty }
                    }));
                };

                quill.on('text-change', sync);
                sync();

                window.__richEditors[config.id] = quill;

                const form = textarea.closest('form');
                if (form && !form.dataset.richEditorBound) {
                    form.dataset.richEditorBound = '1';
                    form.addEventListener('submit', () => {
                        Object.keys(window.__richEditors).forEach((key) => {
                            const ta = document.getElementById(key);
                            const q = window.__richEditors[key];
                            if (ta && q) {
                                const empty = q.getText().trim() === '';
                                ta.value = empty ? '' : q.root.innerHTML;
                            }
                        });
                    });
                }
            };
        </script>
    @endpush
@endonce

<div class="rich-editor-wrap form-group">
    @if($editorLabel)
        <label class="form-label" for="{{ $editorId }}">{{ $editorLabel }}</label>
    @endif

    <div id="{{ $editorId }}-mount" class="rich-editor-quill" style="--rich-editor-min-height: {{ $editorHeight }};"></div>

    <textarea
        name="{{ $fieldName }}"
        id="{{ $editorId }}"
        class="rich-editor-source"
        style="display:none;"
        @if($isRequired) required @endif
    >{{ $fieldValue }}</textarea>

    <p class="rich-editor-hint">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
        Use la barra de herramientas para dar formato al texto. No necesita escribir código HTML.
    </p>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.initRichEditor({
            id: @json($editorId),
            mode: @json($editorMode),
            initialHtml: @json($fieldValue),
            placeholder: @json($placeholder ?? 'Escriba aquí el contenido...')
        });
    });
</script>
@endpush
