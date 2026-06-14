@extends('layouts.admin')

@section('title', 'Evaluaciones - Sistema Militar UNEFA')

@section('content')
    <div style="margin-bottom: 20px;">
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
            Calificaciones y Notas Académicas
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Administre las calificaciones del adiestramiento práctico, tiro, táctica de combate y materias teóricas de la UNEFA Falcón.
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

    <!-- Double Column Command Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left Column: Evaluations History -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Buscador Táctico -->
            <div class="panel">
                <div class="panel-body" style="padding: 16px 20px;">
                    <form action="{{ route('admin.evaluations.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <div style="flex-grow: 1; min-width: 200px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Filtrar por Estudiante o Evaluador..." style="padding: 10px 15px;">
                        </div>
                        <button type="submit" class="btn-tactical btn-tactical-gold">
                            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.evaluations.index') }}" class="btn-tactical btn-tactical-danger">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Table Panel -->
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Historial de Calificaciones</span>
                    </div>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <div class="tactical-table-container">
                        <table class="tactical-table">
                            <thead>
                                <tr>
                                    <th>Estudiante / Efectivo</th>
                                    <th>Curso / Materia</th>
                                    <th>Calificación</th>
                                    <th>Evaluador</th>
                                    <th>Fecha</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $eval)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700;">{{ $eval->personnel->name }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                                {{ $eval->personnel->rank }}
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fa-solid fa-crosshairs" style="color: var(--accent-gold); margin-right: 6px; font-size: 0.85rem;"></i>
                                            <strong>{{ $eval->course->title ?? 'Curso Eliminado' }}</strong>
                                            @if($eval->comments)
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); font-style: italic; margin-left: 20px;">
                                                    "{{ $eval->comments }}"
                                                </div>
                                            @endif
                                        </td>
                                        <td style="font-family: 'Share Tech Mono', monospace; font-size: 1.1rem; font-weight: 700;">
                                            @if($eval->score >= 16)
                                                <span class="badge-status badge-status-green" style="font-size: 0.95rem; padding: 4px 10px;">{{ $eval->score }} / 20</span>
                                            @elseif($eval->score >= 10)
                                                <span class="badge-status badge-status-orange" style="font-size: 0.95rem; padding: 4px 10px;">{{ $eval->score }} / 20</span>
                                            @else
                                                <span class="badge-status badge-status-red" style="font-size: 0.95rem; padding: 4px 10px;">{{ $eval->score }} / 20</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fa-solid fa-signature" style="font-size: 0.75rem; color: var(--text-secondary); margin-right: 4px;"></i>
                                            {{ $eval->evaluator }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($eval->date)->format('d/m/Y') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <form action="{{ route('admin.evaluations.destroy', $eval->id) }}" method="POST" onsubmit="return confirm('¿Confirma la eliminación de esta calificación?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-trash-can"></i> Borrar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                            <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                            No hay calificaciones registradas en el sistema.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($evaluations->hasPages())
                <div style="margin-top: 10px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
                    {{ $evaluations->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Right Column: Grade Loading Panel -->
        <div class="panel">
            <div class="panel-header-bar">
                <div class="panel-title">
                    <i class="fa-solid fa-pen-nib"></i>
                    <span>Cargar Calificación</span>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.evaluations.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Estudiante / Oficial</label>
                        <select name="personnel_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione Integrante</option>
                            @foreach($personnel as $person)
                                <option value="{{ $person->id }}">
                                    {{ $person->name }} ({{ $person->rank }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Curso / Asignatura</label>
                        <select name="course_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione Curso</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->category }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Calificación (0 - 20)</label>
                        <input type="number" name="score" class="form-input" min="0" max="20" placeholder="ej. 19" value="20" required style="font-family: 'Share Tech Mono', monospace; font-size: 1.1rem; font-weight: 700;">
                        <span style="color: var(--text-secondary); font-size: 0.7rem;">* Escala aprobatoria de la UNEFA: 10 a 20 puntos.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Oficial Evaluador</label>
                        <input type="text" name="evaluator" class="form-input" value="{{ auth()->user()->name }}" placeholder="ej. Coronel Sierra" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fecha de Evaluación</label>
                        <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Observaciones / Detalles</label>
                        <textarea name="comments" class="form-input" rows="3" placeholder="ej. Excelente desempeño en la prueba táctica..." style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn-tactical" style="width: 100%; justify-content: center; margin-top: 10px;">
                        <i class="fa-solid fa-save"></i> Registrar Nota
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
