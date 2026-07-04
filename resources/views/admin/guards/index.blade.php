@extends('layouts.admin')

@section('title', 'Procedimientos y Turnos de Guardia - Tactic Force')

@section('content')
    <div style="margin-bottom: 20px;">
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
            Procedimientos y Turnos de Guardia
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Manual de procedimientos de seguridad, roles diarios de guardia y simulaciones tácticas para centinelas.
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
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start; flex-wrap: wrap;">
        
        <!-- Left Column: Shifts List -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Buscador Táctico -->
            <div class="panel">
                <div class="panel-body" style="padding: 16px 20px;">
                    <form action="{{ route('admin.guards.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <div style="flex-grow: 1; min-width: 200px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Filtrar por Oficial, Puesto, Estatus o Turno..." style="padding: 10px 15px;">
                        </div>
                        <button type="submit" class="btn-tactical btn-tactical-gold">
                            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.guards.index') }}" class="btn-tactical btn-tactical-danger">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- List table -->
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Simulaciones y Turnos Planificados</span>
                    </div>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <div class="tactical-table-container">
                        <table class="tactical-table">
                            <thead>
                                <tr>
                                    <th>Efectivo Asignado</th>
                                    <th>Puesto Táctico</th>
                                    <th>Turno / Horario</th>
                                    <th>Fecha</th>
                                    <th>Estatus</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700;">{{ $shift->personnel->name }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                                {{ $shift->personnel->rank }}
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fa-solid fa-person-military-pointing" style="color: var(--accent-gold); margin-right: 6px;"></i>
                                            <strong>{{ $shift->post }}</strong>
                                        </td>
                                        <td style="font-family: 'Share Tech Mono', monospace; font-size: 0.9rem;">
                                            {{ $shift->shift_time }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($shift->date)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if(strtolower($shift->status) == 'activo')
                                                <span class="badge-status badge-status-green">Activo</span>
                                            @elseif(strtolower($shift->status) == 'completado')
                                                <span class="badge-status badge-status-blue">Completado</span>
                                            @else
                                                <span class="badge-status badge-status-orange">Programado</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <form action="{{ route('admin.guards.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('¿Confirma la cancelación de esta guardia?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-ban"></i> Cancelar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                            <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                            No hay simulaciones de guardia registradas en la planificación.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($shifts->hasPages())
                <div style="margin-top: 10px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
                    {{ $shifts->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Right Column: Quick Assignment Form -->
        <div class="panel">
            <div class="panel-header-bar">
                <div class="panel-title">
                    <i class="fa-solid fa-user-clock"></i>
                    <span>Planificar Simulación / Puesto</span>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.guards.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Efectivo Militar</label>
                        <select name="personnel_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione Efectivo</option>
                            @foreach($personnel as $person)
                                <option value="{{ $person->id }}">
                                    {{ $person->name }} ({{ $person->rank }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Puesto Táctico</label>
                        <select name="post" class="form-select" required>
                            <option value="" disabled selected>Seleccione Puesto</option>
                            <option value="Garita Principal (Acceso)">Garita Principal (Acceso)</option>
                            <option value="Garita 2 (Oeste)">Garita 2 (Oeste)</option>
                            <option value="Garita 3 (Este)">Garita 3 (Este)</option>
                            <option value="Polvorín / Bóveda de Armas">Polvorín / Bóveda de Armas</option>
                            <option value="Entrada Vehicular">Entrada Vehicular</option>
                            <option value="Patrulla Perimetral Nocturna">Patrulla Perimetral Nocturna</option>
                            <option value="Oficial de Guardia (Comando)">Oficial de Guardia (Comando)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Turno / Horario</label>
                        <select name="shift_time" class="form-select" required>
                            <option value="" disabled selected>Seleccione Turno</option>
                            <option value="Turno Alpha: 00:00 - 06:00">Turno Alpha (00:00 - 06:00)</option>
                            <option value="Turno Bravo: 06:00 - 12:00">Turno Bravo (06:00 - 12:00)</option>
                            <option value="Turno Charlie: 12:00 - 18:00">Turno Charlie (12:00 - 18:00)</option>
                            <option value="Turno Delta: 18:00 - 00:00">Turno Delta (18:00 - 00:00)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fecha de Guardia</label>
                        <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estatus Inicial</label>
                        <select name="status" class="form-select" required>
                            <option value="Programado" selected>Programado</option>
                            <option value="Activo">Activo (En Turno)</option>
                            <option value="Completado">Completado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-tactical" style="width: 100%; justify-content: center; margin-top: 10px;">
                        <i class="fa-solid fa-circle-plus"></i> Registrar Turno
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
