@extends('layouts.admin')

@section('title', 'Directorio del Personal - Portal Estudiante UNEFA')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.8rem; color: var(--accent-gold); margin-bottom: 4px;">
                <a href="{{ route('student.index') }}" style="color: var(--accent-gold); text-decoration: none;"><i class="fa-solid fa-chevron-left"></i> Portal Estudiante</a>
            </div>
            <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px;">
                <i class="fa-solid fa-users"></i> Directorio del Personal
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">
                Fichero académico de instructores, oficiales y cadetes militares. Solo lectura.
            </p>
        </div>
        <span class="badge-status badge-status-orange">
            <i class="fa-solid fa-eye"></i> Modo Consulta
        </span>
    </div>

    {{-- Buscador --}}
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-body" style="padding: 16px 20px;">
            <form action="{{ route('student.personnel.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Buscar por Nombre, Cédula, Rango o Rol..." style="padding: 10px 15px;">
                </div>
                <button type="submit" class="btn-tactical btn-tactical-gold">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('student.personnel.index') }}" class="btn-tactical btn-tactical-danger">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla --}}
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
                                    @elseif(in_array(strtolower($person->status), ['licencia','comisión']))
                                        <span class="badge-status badge-status-orange">{{ $person->status }}</span>
                                    @else
                                        <span class="badge-status badge-status-red">{{ $person->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                        <div><i class="fa-solid fa-phone" style="margin-right: 4px;"></i>{{ $person->phone ?? 'N/D' }}</div>
                                        <div><i class="fa-solid fa-envelope" style="margin-right: 4px;"></i>{{ $person->email ?? 'N/D' }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
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

    @if($personnel->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center; font-family: 'Share Tech Mono', monospace;">
            {{ $personnel->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
