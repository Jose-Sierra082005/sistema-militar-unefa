@extends('layouts.admin')

@section('title', 'Editar Curso - Tactic Force')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Listado
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Editar Curso Militar
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Modifique la sinopsis, nivel de dificultad o categoría de esta materia de adiestramiento.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="panel" style="max-width: 600px;">
        <div class="panel-header-bar">
            <div class="panel-title">
                <i class="fa-solid fa-file-signature"></i>
                <span>Formulario de Edición de Curso</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Título del Curso</label>
                    <input type="text" name="title" class="form-input" value="{{ old('title', $course->title) }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Categoría Académica</label>
                        <select name="category" class="form-select" required>
                            <option value="Tácticas de Combate" {{ $course->category == 'Tácticas de Combate' ? 'selected' : '' }}>Tácticas de Combate</option>
                            <option value="Armamento y Tiro" {{ $course->category == 'Armamento y Tiro' ? 'selected' : '' }}>Armamento y Tiro</option>
                            <option value="Supervivencia y Evasión" {{ $course->category == 'Supervivencia y Evasión' ? 'selected' : '' }}>Supervivencia y Evasión</option>
                            <option value="Navegación Terrestre" {{ $course->category == 'Navegación Terrestre' ? 'selected' : '' }}>Navegación Terrestre</option>
                            <option value="Primeros Auxilios Militares" {{ $course->category == 'Primeros Auxilios Militares' ? 'selected' : '' }}>Primeros Auxilios Militares</option>
                            <option value="Liderazgo y Estrategia" {{ $course->category == 'Liderazgo y Estrategia' ? 'selected' : '' }}>Liderazgo y Estrategia</option>
                            <option value="Orden Cerrado y Disciplina" {{ $course->category == 'Orden Cerrado y Disciplina' ? 'selected' : '' }}>Orden Cerrado y Disciplina</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nivel de Dificultad</label>
                        <select name="difficulty" class="form-select" required>
                            <option value="Básico" {{ $course->difficulty == 'Básico' ? 'selected' : '' }}>Básico (Primer Semestre)</option>
                            <option value="Intermedio" {{ $course->difficulty == 'Intermedio' ? 'selected' : '' }}>Intermedio (Mitad de Período)</option>
                            <option value="Avanzado" {{ $course->difficulty == 'Avanzado' ? 'selected' : '' }}>Avanzado (Oficiales de Comando)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción General / Sinopsis</label>
                    <textarea name="description" class="form-input" rows="4" required style="resize: none;">{{ old('description', $course->description) }}</textarea>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
@endsection
