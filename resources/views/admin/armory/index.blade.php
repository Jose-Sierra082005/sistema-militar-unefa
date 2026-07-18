@extends('layouts.admin')

@section('title', 'Manual de Armamento y Estudio Táctico - SIAM')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                Manual de Armamento y Estudio Táctico
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Biblioteca digital y catálogo técnico de armamento de estudio militar para alumnos de la UNEFA Falcón.
            </p>
        </div>
        <a href="{{ route('admin.armory.create') }}" class="btn-tactical">
            <i class="fa-solid fa-square-plus"></i> Registrar Modelo de Estudio
        </a>
    </div>

    <!-- Buscador Táctico -->
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('admin.armory.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Buscar por Serial, Tipo o Modelo..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.armory.index') }}" class="btn-tactical btn-tactical-danger">
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

    <!-- Tabla de Resultados -->
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
                            <th style="text-align: center;">Acciones</th>
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
                                    @if(strtolower($weapon->condition) == 'excelente' || strtolower($weapon->condition) == 'operativo')
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
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.armory.edit', $weapon->id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.75rem;">
                                            <i class="fa-solid fa-file-pen"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.armory.destroy', $weapon->id) }}" method="POST" onsubmit="return confirm('¿Confirma la remoción del arma del inventario oficial?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash-can"></i> Deletear
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                    No se encontraron modelos de armamento registrados en la biblioteca de estudio.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación Táctica -->
    @if($weapons->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $weapons->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
