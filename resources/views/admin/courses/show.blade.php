@extends('layouts.admin')

@section('title', 'Temario del Curso - Sistema Militar UNEFA')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver a los Cursos
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Temario Académico: {{ $course->title }}
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Administre las lecturas, lecciones y materiales de estudio de esta asignatura táctica.
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

    <!-- Ficha del Curso -->
    <div class="panel" style="margin-bottom: 30px;">
        <div class="panel-body" style="padding: 20px; display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
            <div style="flex-grow: 1; max-width: 700px;">
                <span class="badge-status badge-status-blue" style="margin-bottom: 8px;">{{ $course->category }}</span>
                <span class="badge-status {{ strtolower($course->difficulty) == 'avanzado' ? 'badge-status-red' : (strtolower($course->difficulty) == 'intermedio' ? 'badge-status-orange' : 'badge-status-green') }}" style="margin-bottom: 8px; margin-left: 8px;">
                    Dificultad: {{ $course->difficulty }}
                </span>
                <p style="color: var(--text-main); font-size: 0.95rem; margin-top: 10px; line-height: 1.5;">
                    {{ $course->description }}
                </p>
            </div>
            <div style="text-align: right; min-width: 150px; font-family: 'Share Tech Mono', monospace;">
                <div style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">Total Lecciones</div>
                <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent-gold);">{{ $course->lessons->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Double Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left Column: Lessons list -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <h3 style="font-family: 'Share Tech Mono', monospace; font-size: 1.2rem; text-transform: uppercase; color: var(--accent-gold); border-bottom: 1px solid var(--border-primary); padding-bottom: 8px; margin-bottom: 5px;">
                Lecciones Planificadas
            </h3>

            @forelse($course->lessons as $lesson)
                <div class="panel">
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
                    <div class="panel-body" style="padding: 20px; font-size: 0.92rem; line-height: 1.6; color: var(--text-secondary); background: rgba(7, 9, 14, 0.3);">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
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

        <!-- Right Column: Add Lesson Form -->
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
@endsection

@section('styles')
    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection
