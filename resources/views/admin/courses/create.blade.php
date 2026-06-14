@extends('layouts.admin')

@section('title', 'Crear Curso - Sistema Militar UNEFA')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Listado
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Crear Nuevo Curso Militar
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Ingrese los detalles del curso académico. Luego podrá redactar y agregar lecciones detalladas en su respectivo temario.
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
                <i class="fa-solid fa-folder-plus"></i>
                <span>Ficha del Curso Académico</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.courses.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Título del Curso</label>
                    <input type="text" name="title" class="form-input" placeholder="ej. Tácticas de Infantería y Operaciones Urbanas" value="{{ old('title') }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Categoría Académica</label>
                        <select name="category" class="form-select" required>
                            <option value="" disabled selected>Seleccione Categoría</option>
                            <option value="Tácticas de Combate">Tácticas de Combate</option>
                            <option value="Armamento y Tiro">Armamento y Tiro</option>
                            <option value="Supervivencia y Evasión">Supervivencia y Evasión</option>
                            <option value="Navegación Terrestre">Navegación Terrestre</option>
                            <option value="Primeros Auxilios Militares">Primeros Auxilios Militares</option>
                            <option value="Liderazgo y Estrategia">Liderazgo y Estrategia</option>
                            <option value="Orden Cerrado y Disciplina">Orden Cerrado y Disciplina</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nivel de Dificultad</label>
                        <select name="difficulty" class="form-select" required>
                            <option value="Básico" selected>Básico (Primer Semestre)</option>
                            <option value="Intermedio">Intermedio (Mitad de Período)</option>
                            <option value="Avanzado">Avanzado (Oficiales de Comando)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción General / Sinopsis</label>
                    <textarea name="description" class="form-input" rows="4" placeholder="Describa brevemente los objetivos de aprendizaje, competencias tácticas a desarrollar y temarios generales del curso..." required style="resize: none;"></textarea>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar y Continuar
                </button>
            </form>
        </div>
    </div>
@endsection
