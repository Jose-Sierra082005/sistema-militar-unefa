@extends('layouts.admin')

@section('title', 'Manual de Guardia - Portal Estudiante UNEFA')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.8rem; color: var(--accent-gold); margin-bottom: 4px;">
                <a href="{{ route('student.index') }}" style="color: var(--accent-gold); text-decoration: none;"><i class="fa-solid fa-chevron-left"></i> Portal Estudiante</a>
            </div>
            <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                <i class="fa-solid fa-shield-halved"></i> Manual de Guardia
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">
                Simulaciones de guardia, procedimientos de seguridad y turnos planificados. Solo lectura.
            </p>
        </div>
        <span class="badge-status badge-status-orange">
            <i class="fa-solid fa-eye"></i> Modo Consulta
        </span>
    </div>

    {{-- Buscador --}}
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('student.guards.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Filtrar por Oficial, Puesto, Estatus o Turno..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('student.guards.index') }}" class="btn-tactical btn-tactical-danger">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla --}}
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">{{ $shift->personnel->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        <i class="fa-solid fa-ribbon" style="margin-right: 3px; font-size: 0.7rem;"></i>{{ $shift->personnel->rank }}
                                    </div>
                                </td>
                                <td>
                                    <i class="fa-solid fa-location-dot" style="color: var(--accent-gold); margin-right: 6px; font-size: 0.8rem;"></i>
                                    {{ $shift->post }}
                                </td>
                                <td style="font-family: 'Share Tech Mono', monospace; font-size: 0.9rem;">
                                    {{ $shift->shift_time }}
                                </td>
                                <td style="font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; color: var(--accent-gold);">
                                    {{ \Carbon\Carbon::parse($shift->date)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if(strtolower($shift->status) == 'activa' || strtolower($shift->status) == 'en curso')
                                        <span class="badge-status badge-status-green">{{ $shift->status }}</span>
                                    @elseif(strtolower($shift->status) == 'programada' || strtolower($shift->status) == 'pendiente')
                                        <span class="badge-status badge-status-orange">{{ $shift->status }}</span>
                                    @else
                                        <span class="badge-status badge-status-blue">{{ $shift->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                    No se encontraron turnos de guardia registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($shifts->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $shifts->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
