@extends('layouts.admin')

@section('title', 'Editar Pregunta - SIAM')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.show', $question->lesson->course_id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Temario
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Editar Pregunta del Cuestionario
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Curso: <strong style="color: var(--text-main);">{{ $question->lesson->course->title }}</strong>
            &nbsp;|&nbsp;
            Lección: <strong style="color: var(--text-main);">{{ $question->lesson->title }}</strong>
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="panel" style="max-width: 720px;">
        <div class="panel-header-bar">
            <div class="panel-title">
                <i class="fa-solid fa-circle-question"></i>
                <span>Formulario de Edición</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Enunciado de la Pregunta</label>
                    <textarea name="question_text" class="form-input" rows="3" required style="resize: vertical;">{{ old('question_text', $question->question_text) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Puntos XP</label>
                    <input type="number" name="points" class="form-input" min="5" max="100" value="{{ old('points', $question->points) }}" required style="font-family: 'Share Tech Mono', monospace; max-width: 120px;">
                </div>

                @php
                    $options = $question->options->sortBy('id')->values();
                    $correctIndex = old('correct_option', $options->search(fn ($o) => $o->is_correct));
                    if ($correctIndex === false) $correctIndex = 0;
                @endphp

                <div style="margin-bottom: 16px;">
                    <label class="form-label">Opciones de Respuesta (marque la correcta)</label>
                    @for($i = 0; $i < 4; $i++)
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <input type="radio" name="correct_option" value="{{ $i }}" required
                                   {{ (int) $correctIndex === $i ? 'checked' : '' }}
                                   style="accent-color: #22c55e; width: 16px; height: 16px; flex-shrink: 0;">
                            <input type="text" name="options[]" class="form-input" required
                                   value="{{ old('options.'.$i, $options[$i]->option_text ?? '') }}"
                                   placeholder="Opción {{ chr(65 + $i) }}"
                                   style="flex-grow: 1;">
                        </div>
                    @endfor
                </div>

                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn-tactical">
                        <i class="fa-solid fa-save"></i> Guardar Pregunta
                    </button>
                    <a href="{{ route('admin.courses.show', $question->lesson->course_id) }}" class="btn-tactical btn-tactical-gold" style="text-decoration: none;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
