@extends('layouts.admin')

@section('title', 'Editar Oficial - Tactic Force')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.personnel.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Fichero
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Editar Personal Militar
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Modifique los campos de la ficha oficial del oficial o integrante.
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
                <i class="fa-solid fa-user-pen"></i>
                <span>Formulario de Edición Militar</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.personnel.update', $person->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $person->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cédula de Identidad</label>
                    <input type="text" name="cedula" class="form-input" value="{{ old('cedula', $person->cedula) }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Rango Militar</label>
                        <select name="rank" class="form-select" required>
                            <option value="General de División" {{ $person->rank == 'General de División' ? 'selected' : '' }}>General de División</option>
                            <option value="General de Brigada" {{ $person->rank == 'General de Brigada' ? 'selected' : '' }}>General de Brigada</option>
                            <option value="Coronel" {{ $person->rank == 'Coronel' ? 'selected' : '' }}>Coronel</option>
                            <option value="Teniente Coronel" {{ $person->rank == 'Teniente Coronel' ? 'selected' : '' }}>Teniente Coronel</option>
                            <option value="Mayor" {{ $person->rank == 'Mayor' ? 'selected' : '' }}>Mayor</option>
                            <option value="Capitán" {{ $person->rank == 'Capitán' ? 'selected' : '' }}>Capitán</option>
                            <option value="Primer Teniente" {{ $person->rank == 'Primer Teniente' ? 'selected' : '' }}>Primer Teniente</option>
                            <option value="Teniente" {{ $person->rank == 'Teniente' ? 'selected' : '' }}>Teniente</option>
                            <option value="Sargento Mayor" {{ $person->rank == 'Sargento Mayor' ? 'selected' : '' }}>Sargento Mayor</option>
                            <option value="Sargento" {{ $person->rank == 'Sargento' ? 'selected' : '' }}>Sargento</option>
                            <option value="Cabo" {{ $person->rank == 'Cabo' ? 'selected' : '' }}>Cabo</option>
                            <option value="Soldado" {{ $person->rank == 'Soldado' ? 'selected' : '' }}>Soldado</option>
                            <option value="Cadete" {{ $person->rank == 'Cadete' ? 'selected' : '' }}>Cadete</option>
                            <option value="Estudiante UNEFA" {{ $person->rank == 'Estudiante UNEFA' ? 'selected' : '' }}>Estudiante UNEFA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rol / Cargo</label>
                        <select name="role" class="form-select" required>
                            <option value="Comandante de Sede" {{ $person->role == 'Comandante de Sede' ? 'selected' : '' }}>Comandante de Sede</option>
                            <option value="Oficial de Comando" {{ $person->role == 'Oficial de Comando' ? 'selected' : '' }}>Oficial de Comando</option>
                            <option value="Instructor de Tácticas" {{ $person->role == 'Instructor de Tácticas' ? 'selected' : '' }}>Instructor de Tácticas</option>
                            <option value="Oficial de Armamento" {{ $person->role == 'Oficial de Armamento' ? 'selected' : '' }}>Oficial de Armamento</option>
                            <option value="Centinela / Guardia" {{ $person->role == 'Centinela / Guardia' ? 'selected' : '' }}>Centinela / Guardia</option>
                            <option value="Estudiante Militar" {{ $person->role == 'Estudiante Militar' ? 'selected' : '' }}>Estudiante Militar</option>
                            <option value="Docente" {{ $person->role == 'Docente' ? 'selected' : '' }}>Docente</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado Operacional</label>
                    <select name="status" class="form-select" required>
                        <option value="Activo" {{ $person->status == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Comisión de Servicio" {{ $person->status == 'Comisión de Servicio' ? 'selected' : '' }}>Comisión de Servicio</option>
                        <option value="Licencia Médica" {{ $person->status == 'Licencia Médica' ? 'selected' : '' }}>Licencia Médica</option>
                        <option value="Retirado" {{ $person->status == 'Retirado' ? 'selected' : '' }}>Retirado / Inactivo</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $person->phone) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $person->email) }}">
                    </div>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
@endsection
