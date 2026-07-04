@extends('layouts.admin')

@section('title', 'Cursos Tácticos - Tactic Force')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                Cursos Académicos de Adiestramiento
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Administración y temarios de asignaturas táctico-militares para cadetes y estudiantes de la sede UNEFA Falcón.
            </p>
        </div>
        <a href="{{ route('admin.courses.create') }}" class="btn-tactical">
            <i class="fa-solid fa-folder-plus"></i> Crear Nuevo Curso
        </a>
    </div>

    <!-- Buscador Táctico -->
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('admin.courses.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Buscar por Nombre, Categoría o Dificultad..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-danger">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabla de Cursos -->
    <div class="panel">
        <div class="panel-header-bar">
            <div class="panel-title">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Cursos Registrados en la Academia</span>
            </div>
        </div>
        <div class="panel-body" style="padding: 0;">
            <div class="tactical-table-container">
                <table class="tactical-table">
                    <thead>
                        <tr>
                            <th>Título del Curso</th>
                            <th>Descripción / Ficha</th>
                            <th>Categoría</th>
                            <th>Nivel / Dificultad</th>
                            <th style="text-align: center;">Lecciones Cargadas</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                                    {{ $course->title }}
                                </td>
                                <td style="color: var(--text-secondary); font-size: 0.85rem; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $course->description }}
                                </td>
                                <td>
                                    <span style="font-family: 'Share Tech Mono', monospace; color: var(--accent-gold);">
                                        {{ $course->category }}
                                    </span>
                                </td>
                                <td>
                                    @if(strtolower($course->difficulty) == 'avanzado')
                                        <span class="badge-status badge-status-red">Avanzado</span>
                                    @elseif(strtolower($course->difficulty) == 'intermedio')
                                        <span class="badge-status badge-status-orange">Intermedio</span>
                                    @else
                                        <span class="badge-status badge-status-green">Básico</span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-family: 'Share Tech Mono', monospace; font-size: 1.1rem; font-weight: 700;">
                                    <span style="background: rgba(42, 71, 51, 0.3); border: 1px dashed var(--border-primary); padding: 2px 10px; border-radius: 4px;">
                                        {{ $course->lessons->count() }} Lección(es)
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.courses.show', $course->id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.75rem;">
                                            <i class="fa-solid fa-book-open"></i> Ver Temario
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.75rem; background: transparent; border-color: var(--border-glow); color: var(--accent-gold);">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('¿Confirma la eliminación del curso y todas sus lecciones asociadas?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                    No se encontraron cursos académicos en la plataforma.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    @if($courses->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
