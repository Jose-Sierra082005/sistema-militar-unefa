@extends('layouts.admin')

@section('title', 'Editar Lección - Tactic Force')

@section('styles')
<style>
    .editor-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 1100px) {
        .editor-grid { grid-template-columns: 1fr; }
    }

    .preview-panel {
        background: rgba(7, 9, 14, 0.45);
        border: 1px solid var(--border-primary);
        border-radius: 12px;
        padding: 24px;
        min-height: 320px;
        line-height: 1.65;
        font-size: 0.95rem;
    }

    .preview-panel h3 {
        font-family: 'Share Tech Mono', monospace;
        color: var(--accent-gold);
        margin: 18px 0 10px;
        text-transform: uppercase;
        font-size: 1rem;
    }

    .preview-panel ul, .preview-panel ol {
        margin-left: 20px;
        margin-bottom: 14px;
    }

    .html-hint {
        font-size: 0.78rem;
        color: var(--text-secondary);
        margin-top: 8px;
        line-height: 1.45;
    }
</style>
@endsection

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.show', $lesson->course_id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Temario
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Editar Lección / Sección
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Curso: <strong style="color: var(--text-main);">{{ $lesson->course->title }}</strong>
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('admin.lessons.update', $lesson->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="editor-grid">
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Contenido de la Sección</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="form-label">Título de la Lección</label>
                        <input type="text" name="title" id="lesson-title" class="form-input" value="{{ old('title', $lesson->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Orden en el Mapa del Curso</label>
                        <input type="number" name="order" class="form-input" min="0" value="{{ old('order', $lesson->order) }}" required style="font-family: 'Share Tech Mono', monospace; max-width: 140px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contenido Teórico / Táctico</label>
                        <textarea name="content" id="lesson-content" class="form-input" rows="16" required style="resize: vertical; font-size: 0.88rem; line-height: 1.5;">{{ old('content', $lesson->content) }}</textarea>
                        <p class="html-hint">
                            <i class="fa-solid fa-circle-info" style="color: var(--accent-gold);"></i>
                            Puede usar HTML básico: <code>&lt;p&gt;</code>, <code>&lt;h3&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>.
                            Así se verá igual que en el portal del estudiante.
                        </p>
                    </div>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <button type="submit" class="btn-tactical">
                            <i class="fa-solid fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('admin.courses.show', $lesson->course_id) }}" class="btn-tactical btn-tactical-gold" style="text-decoration: none;">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-eye"></i>
                        <span>Vista Previa del Estudiante</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="preview-panel" id="lesson-preview">
                        <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 1.3rem; color: var(--accent-gold); margin-bottom: 16px; text-transform: uppercase;" id="preview-title">{{ $lesson->title }}</h1>
                        <div id="preview-body">{!! $lesson->content !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        const titleInput = document.getElementById('lesson-title');
        const contentInput = document.getElementById('lesson-content');
        const previewTitle = document.getElementById('preview-title');
        const previewBody = document.getElementById('preview-body');

        function refreshPreview() {
            previewTitle.textContent = titleInput.value || 'Sin título';
            previewBody.innerHTML = contentInput.value || '<p style="color: var(--text-secondary);">Escriba contenido para ver la vista previa.</p>';
        }

        titleInput.addEventListener('input', refreshPreview);
        contentInput.addEventListener('input', refreshPreview);
    </script>
@endsection
