<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .rich-editor-wrap { margin-bottom: 4px; }

    .rich-editor-quill {
        background: rgba(7, 9, 14, 0.95);
        border: 1px solid var(--border-primary);
        border-radius: 0 0 8px 8px;
        min-height: var(--rich-editor-min-height, 280px);
    }

    .rich-editor-quill .ql-toolbar.ql-snow {
        background: rgba(18, 24, 38, 0.95);
        border: 1px solid var(--border-primary);
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        font-family: 'Outfit', sans-serif;
    }

    .rich-editor-quill .ql-container.ql-snow {
        border: none;
        font-family: 'Outfit', sans-serif;
        font-size: 0.92rem;
        color: var(--text-main);
    }

    .rich-editor-quill .ql-editor {
        min-height: var(--rich-editor-min-height, 280px);
        line-height: 1.65;
    }

    .rich-editor-quill .ql-editor.ql-blank::before {
        color: var(--text-secondary);
        font-style: normal;
    }

    .rich-editor-quill .ql-snow .ql-stroke { stroke: var(--text-secondary); }
    .rich-editor-quill .ql-snow .ql-fill { fill: var(--text-secondary); }
    .rich-editor-quill .ql-snow .ql-picker-label { color: var(--text-secondary); }
    .rich-editor-quill .ql-snow .ql-picker-options {
        background: rgba(13, 17, 27, 0.98);
        border-color: var(--border-primary);
    }

    .rich-editor-quill .ql-snow .ql-picker-item { color: var(--text-main); }
    .rich-editor-quill .ql-snow .ql-picker-item:hover { color: var(--accent-gold); }

    .rich-editor-quill .ql-snow button:hover .ql-stroke,
    .rich-editor-quill .ql-snow .ql-picker-label:hover .ql-stroke,
    .rich-editor-quill .ql-snow button.ql-active .ql-stroke,
    .rich-editor-quill .ql-snow .ql-picker-label.ql-active .ql-stroke {
        stroke: var(--accent-gold);
    }

    .rich-editor-quill .ql-snow button:hover .ql-fill,
    .rich-editor-quill .ql-snow button.ql-active .ql-fill {
        fill: var(--accent-gold);
    }

    .rich-editor-quill .ql-editor h3 {
        font-family: 'Share Tech Mono', monospace;
        color: var(--accent-gold);
        font-size: 1rem;
        text-transform: uppercase;
        margin: 1em 0 0.5em;
    }

    .rich-editor-quill .ql-editor ul,
    .rich-editor-quill .ql-editor ol {
        padding-left: 1.4em;
    }

    .rich-editor-hint {
        font-size: 0.78rem;
        color: var(--text-secondary);
        margin-top: 8px;
        line-height: 1.45;
    }

    .rich-editor-hint i { color: var(--accent-gold); margin-right: 4px; }
</style>
