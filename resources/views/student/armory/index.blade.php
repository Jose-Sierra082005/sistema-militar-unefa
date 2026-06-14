@extends('layouts.admin')

@section('title', 'Manual de Armamento - Portal Estudiante UNEFA')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.8rem; color: var(--accent-gold); margin-bottom: 4px;">
                <a href="{{ route('student.index') }}" style="color: var(--accent-gold); text-decoration: none;"><i class="fa-solid fa-chevron-left"></i> Portal Estudiante</a>
            </div>
            <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                <i class="fa-solid fa-book-open"></i> Manual de Armamento
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">
                Catálogo técnico y reglamentario de armamento de estudio. Solo lectura.
            </p>
        </div>
        <span class="badge-status badge-status-orange">
            <i class="fa-solid fa-eye"></i> Modo Consulta
        </span>
    </div>

    {{-- Buscador --}}
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('student.armory.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Buscar por Serial, Tipo o Modelo..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('student.armory.index') }}" class="btn-tactical btn-tactical-danger">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="panel">
        <div class="panel-header-bar">
            <div class="panel-title">
                <i class="fa-solid fa-book-open"></i>
                <span>Modelos Reglamentarios de Estudio</span>
            </div>
        </div>
        <div class="panel-body" style="padding: 0;">
            <div class="tactical-table-container">
                <table class="tactical-table">
                    <thead>
                        <tr>
                            <th>Serial / Nro Registro</th>
                            <th>Tipo de Armamento</th>
                            <th>Modelo / Calibre</th>
                            <th>Estado de Conservación</th>
                            <th>Ubicación / Asignación</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($weapons as $weapon)
                            <tr>
                                <td style="font-family: 'Share Tech Mono', monospace; font-size: 0.95rem; font-weight: 700; color: var(--accent-gold);">
                                    {{ $weapon->serial }}
                                </td>
                                <td>{{ $weapon->type }}</td>
                                <td>{{ $weapon->model }}</td>
                                <td>
                                    @if(in_array(strtolower($weapon->condition), ['excelente','operativo']))
                                        <span class="badge-status badge-status-green">{{ $weapon->condition }}</span>
                                    @elseif(strtolower($weapon->condition) == 'en mantenimiento')
                                        <span class="badge-status badge-status-orange">{{ $weapon->condition }}</span>
                                    @else
                                        <span class="badge-status badge-status-red">{{ $weapon->condition }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($weapon->assignee)
                                        <div>
                                            <i class="fa-solid fa-user-shield" style="color: var(--accent-gold); margin-right: 6px; font-size: 0.8rem;"></i>
                                            <strong>{{ $weapon->assignee->name }}</strong>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-left: 18px;">
                                            Cédula: {{ $weapon->assignee->cedula }}
                                        </div>
                                    @else
                                        <span style="color: var(--text-secondary); font-style: italic;">
                                            <i class="fa-solid fa-warehouse" style="margin-right: 6px; font-size: 0.8rem;"></i>Resguardado en Bóveda
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($weapon->status) == 'resguardado')
                                        <span class="badge-status badge-status-blue">Resguardado</span>
                                    @elseif(strtolower($weapon->status) == 'asignado')
                                        <span class="badge-status badge-status-green">Asignado</span>
                                    @else
                                        <span class="badge-status badge-status-orange">Mantenimiento</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                    No se encontraron modelos de armamento en la biblioteca de estudio.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($weapons->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $weapons->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
