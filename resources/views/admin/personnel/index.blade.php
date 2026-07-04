@extends('layouts.admin')

@section('title', 'Fichero Académico - Tactic Force')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                Fichero Académico Militar
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Administración de fichas técnicas de instructores, oficiales y estudiantes militares adscritos a la academia.
            </p>
        </div>
        <a href="{{ route('admin.personnel.create') }}" class="btn-tactical">
            <i class="fa-solid fa-user-plus"></i> Registrar Integrante
        </a>
    </div>

    <!-- Buscador Táctico -->
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('admin.personnel.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Buscar por Nombre, Cédula, Rango o Rol..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.personnel.index') }}" class="btn-tactical btn-tactical-danger">
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
                <i class="fa-solid fa-list-check"></i>
                <span>Integrantes de la Academia</span>
            </div>
        </div>
        <div class="panel-body" style="padding: 0;">
            <div class="tactical-table-container">
                <table class="tactical-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Rango</th>
                            <th>Rol / Cargo</th>
                            <th>Estado</th>
                            <th>Contacto</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnel as $person)
                            <tr>
                                <td style="font-weight: 700;">{{ $person->name }}</td>
                                <td style="font-family: 'Share Tech Mono', monospace; font-size: 0.95rem;">{{ $person->cedula }}</td>
                                <td><i class="fa-solid fa-ribbon" style="color: var(--accent-gold); margin-right: 6px;"></i>{{ $person->rank }}</td>
                                <td>{{ $person->role }}</td>
                                <td>
                                    @if(strtolower($person->status) == 'activo')
                                        <span class="badge-status badge-status-green">Activo</span>
                                    @elseif(strtolower($person->status) == 'licencia' || strtolower($person->status) == 'comisión')
                                        <span class="badge-status badge-status-orange">{{ $person->status }}</span>
                                    @else
                                        <span class="badge-status badge-status-red">{{ $person->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                        <div><i class="fa-solid fa-phone" style="margin-right: 4px; font-size: 0.75rem;"></i>{{ $person->phone ?? 'Sin Teléfono' }}</div>
                                        <div><i class="fa-solid fa-envelope" style="margin-right: 4px; font-size: 0.75rem;"></i>{{ $person->email ?? 'Sin Correo' }}</div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.personnel.edit', $person->id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.75rem;">
                                            <i class="fa-solid fa-user-pen"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.personnel.destroy', $person->id) }}" method="POST" onsubmit="return confirm('¿Confirma la eliminación del registro militar?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-tactical btn-tactical-danger" style="padding: 6px 12px; font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash-can"></i> Deletrear
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                                    No se encontraron registros en el fichero académico.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación Táctica -->
    @if($personnel->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $personnel->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
