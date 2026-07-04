@extends('layouts.admin')

@section('title', 'Temario del Curso - Tactic Force')

@section('styles')
<style>
    .quiz-panel {
        border-top: 1px dashed rgba(46, 74, 53, 0.35);
        padding: 16px 20px;
        background: rgba(7, 9, 14, 0.45);
    }

    .quiz-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        gap: 10px;
        user-select: none;
    }

    .quiz-panel-header h4 {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.82rem;
        text-transform: uppercase;
        color: var(--accent-gold);
        letter-spacing: 1px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quiz-panel-body { display: none; margin-top: 14px; }
    .quiz-panel-body.open { display: block; }

    .question-item {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(46, 74, 53, 0.25);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .question-item-text {
        font-size: 0.88rem;
        color: var(--text-main);
        flex-grow: 1;
        line-height: 1.4;
    }

    .question-item-meta {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.75rem;
        color: var(--accent-gold);
        white-space: nowrap;
    }

    .question-options-list {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .opt-badge {
        font-size: 0.72rem;
        padding: 3px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.04);
        color: var(--text-secondary);
    }

    .opt-badge.correct {
        border-color: rgba(34, 197, 94, 0.5);
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        font-weight: 700;
    }

    .add-q-form {
        background: rgba(7, 9, 14, 0.6);
        border: 1px dashed rgba(46, 74, 53, 0.4);
        border-radius: 8px;
        padding: 16px;
        margin-top: 14px;
        display: none;
    }
    .add-q-form.open { display: block; }

    @media (max-width: 1024px) {
        .two-col-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver a los Cursos
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Temario Académico: {{ $course->title }}
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Administre las lecciones, su contenido teórico y los cuestionarios de evaluación táctica.
        </p>
    </div>

    @if (session('success'))
        <div class="alert" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Ficha del Curso --}}
    <div class="panel" style="margin-bottom: 30px;">
        <div class="panel-body" style="padding: 20px; display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
            <div style="flex-grow: 1; max-width: 700px;">
                <span class="badge-status badge-status-blue" style="margin-bottom: 8px;">{{ $course->category }}</span>
                <span class="badge-status {{ strtolower($course->difficulty) == 'avanzado' ? 'badge-status-red' : (strtolower($course->difficulty) == 'intermedio' ? 'badge-status-orange' : 'badge-status-green') }}" style="margin-bottom: 8px; margin-left: 8px;">
                    Dificultad: {{ $course->difficulty }}
                </span>
                <p style="color: var(--text-main); font-size: 0.95rem; margin-top: 10px; line-height: 1.5;">{{ $course->description }}</p>
            </div>
            <div style="text-align: right; min-width: 150px; font-family: 'Share Tech Mono', monospace;">
                <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Total Lecciones</div>
                <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent-gold);">{{ $course->lessons->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Double Column Layout --}}
    <div class="two-col-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        
        {{-- Left Column: Lessons list --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <h3 style="font-family: 'Share Tech Mono', monospace; font-size: 1.2rem; text-transform: uppercase; color: var(--accent-gold); border-bottom: 1px solid var(--border-primary); padding-bottom: 8px; margin-bottom: 5px;">
                Lecciones Planificadas
            </h3>

            @forelse($course->lessons as $lesson)
                <div class="panel">
                    {{-- Lesson Header --}}
                    <div class="panel-header-bar" style="background: rgba(18, 24, 38, 0.95); padding: 12px 20px;">
                        <div class="panel-title" style="font-size: 0.9rem; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                            <span style="font-family: 'Share Tech Mono', monospace; background: var(--tactical-green); border: 1px solid var(--border-primary); color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;">
                                Orden: {{ $lesson->order }}
                            </span>
                            <span>{{ $lesson->title }}</span>
                        </div>
                        <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('¿Confirma la eliminación de esta lección?');" style="margin-left: auto;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 4px 10px; font-size: 0.7rem;">
                                <i class="fa-solid fa-trash-can"></i> Quitar
                            </button>
                        </form>
                    </div>

                    {{-- Lesson Content --}}
                    <div class="panel-body" style="padding: 20px; font-size: 0.92rem; line-height: 1.6; color: var(--text-secondary); background: rgba(7, 9, 14, 0.3);">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>

                    {{-- Quiz Management Panel --}}
                    <div class="quiz-panel">
                        <div class="quiz-panel-header" onclick="toggleQuizPanel({{ $lesson->id }})">
                            <h4>
                                <i class="fa-solid fa-circle-question"></i>
                                Cuestionario de Evaluación
                                <span class="badge-status {{ $lesson->questions->count() > 0 ? 'badge-status-green' : 'badge-status-orange' }}" style="font-size: 0.7rem; padding: 2px 8px;">
                                    {{ $lesson->questions->count() }} Pregunta(s)
                                </span>
                            </h4>
                            <i class="fa-solid fa-chevron-down" id="quiz-chevron-{{ $lesson->id }}" style="color: var(--accent-gold); font-size: 0.8rem; transition: transform 0.25s;"></i>
                        </div>

                        <div class="quiz-panel-body" id="quiz-body-{{ $lesson->id }}">

                            {{-- Existing Questions --}}
                            @forelse($lesson->questions as $question)
                                <div class="question-item">
                                    <div style="flex-grow: 1;">
                                        <div class="question-item-text">
                                            <i class="fa-solid fa-circle-dot" style="color: var(--accent-gold); margin-right: 6px; font-size: 0.75rem;"></i>
                                            {{ $question->question_text }}
                                        </div>
                                        <div class="question-options-list">
                                            @foreach($question->options as $option)
                                                <span class="opt-badge {{ $option->is_correct ? 'correct' : '' }}">
                                                    {{ $option->is_correct ? '✓ ' : '' }}{{ Str::limit($option->option_text, 40) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0;">
                                        <span class="question-item-meta">+{{ $question->points }} XP</span>
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta pregunta?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 3px 8px; font-size: 0.7rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p style="color: var(--text-muted); font-size: 0.82rem; font-style: italic; margin-bottom: 10px;">
                                    <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
                                    Esta lección usa preguntas de respaldo automáticas. Agregue preguntas personalizadas aquí.
                                </p>
                            @endforelse

                            {{-- Add Question Button --}}
                            <button type="button" class="btn-tactical btn-tactical-gold" style="padding: 6px 14px; font-size: 0.78rem; margin-top: 4px;"
                                    onclick="toggleAddForm({{ $lesson->id }})">
                                <i class="fa-solid fa-plus"></i> Agregar Pregunta
                            </button>

                            {{-- Add Question Form --}}
                            <div class="add-q-form" id="add-q-form-{{ $lesson->id }}">
                                <form action="{{ route('admin.questions.store', $lesson->id) }}" method="POST">
                                    @csrf

                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label class="form-label" style="font-size: 0.82rem;">Pregunta</label>
                                        <input type="text" name="question_text" class="form-input" required
                                               placeholder="¿Cuál es el procedimiento reglamentario para...?" style="font-size: 0.85rem; padding: 8px 12px;">
                                    </div>

                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label class="form-label" style="font-size: 0.82rem;">XP de la Pregunta</label>
                                        <input type="number" name="points" class="form-input" value="15" min="5" max="50" required
                                               style="font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; padding: 8px 12px; max-width: 120px;">
                                    </div>

                                    <div style="margin-bottom: 12px;">
                                        <label class="form-label" style="font-size: 0.82rem;">Opciones de Respuesta (marque la correcta con el radio)</label>
                                        @for($i = 0; $i < 4; $i++)
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                                <input type="radio" name="correct_option" value="{{ $i }}" required
                                                       style="accent-color: #22c55e; width: 16px; height: 16px; flex-shrink: 0;">
                                                <input type="text" name="options[]" class="form-input" required
                                                       placeholder="Opción {{ chr(65 + $i) }}"
                                                       style="font-size: 0.82rem; padding: 7px 10px; flex-grow: 1;">
                                            </div>
                                        @endfor
                                    </div>

                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" class="btn-tactical" style="padding: 8px 18px; font-size: 0.8rem;">
                                            <i class="fa-solid fa-save"></i> Guardar Pregunta
                                        </button>
                                        <button type="button" class="btn-tactical btn-tactical-danger" style="padding: 8px 18px; font-size: 0.8rem;"
                                                onclick="toggleAddForm({{ $lesson->id }})">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>{{-- /quiz-panel-body --}}
                    </div>{{-- /quiz-panel --}}

                </div>
            @empty
                <div class="panel" style="background: rgba(7, 9, 14, 0.4); border-style: dashed;">
                    <div class="panel-body" style="text-align: center; color: var(--text-secondary); padding: 40px;">
                        <i class="fa-solid fa-book-open" style="font-size: 2rem; display: block; margin-bottom: 10px; color: var(--border-primary);"></i>
                        No se han cargado lecciones teóricas o prácticas para este curso militar. 
                        Redacte e ingrese una lección utilizando el formulario de la derecha.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Right Column: Add Lesson Form --}}
        <div class="panel">
            <div class="panel-header-bar">
                <div class="panel-title">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Cargar Nueva Lección</span>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.lessons.store', $course->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Título de la Lección</label>
                        <input type="text" name="title" class="form-input" placeholder="ej. Procedimiento de Desarme del Fusil" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Orden Secuencial</label>
                        <input type="number" name="order" class="form-input" placeholder="0" min="0" value="{{ old('order', $course->lessons->max('order') !== null ? $course->lessons->max('order') + 1 : 1) }}" required style="font-family: 'Share Tech Mono', monospace;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contenido Táctico / Teórico</label>
                        <textarea name="content" class="form-input" rows="8" placeholder="Redacte el contenido oficial de la lección. Puede estructurar el texto utilizando guiones, párrafos y pasos procedurales..." required style="resize: none; font-size: 0.85rem; line-height: 1.4; font-family: sans-serif;"></textarea>
                    </div>

                    <button type="submit" class="btn-tactical" style="width: 100%; justify-content: center; margin-top: 10px;">
                        <i class="fa-solid fa-upload"></i> Publicar Lección
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleQuizPanel(lessonId) {
            const body    = document.getElementById('quiz-body-' + lessonId);
            const chevron = document.getElementById('quiz-chevron-' + lessonId);
            body.classList.toggle('open');
            chevron.style.transform = body.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        function toggleAddForm(lessonId) {
            const form = document.getElementById('add-q-form-' + lessonId);
            form.classList.toggle('open');
            const body = document.getElementById('quiz-body-' + lessonId);
            if (!body.classList.contains('open')) toggleQuizPanel(lessonId);
        }
    </script>
@endsection
