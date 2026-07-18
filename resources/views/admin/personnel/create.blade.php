@extends('layouts.admin')

@section('title', 'Registrar Oficial - SIAM')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.personnel.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Fichero
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Registrar Nuevo Personal Militar
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Complete los campos con la información oficial del integrante para darlo de alta en la plataforma.
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
                <i class="fa-solid fa-user-plus"></i>
                <span>Formulario de Alta Militar</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.personnel.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-input" placeholder="ej. Cnel. José Sierra" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cédula de Identidad</label>
                    <input type="text" name="cedula" class="form-input" placeholder="ej. 31149881 o V-31149881" value="{{ old('cedula') }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Rango Militar</label>
                        <select name="rank" class="form-select" required>
                            <option value="" disabled selected>Seleccione Rango</option>
                            <option value="General de División">General de División</option>
                            <option value="General de Brigada">General de Brigada</option>
                            <option value="Coronel">Coronel</option>
                            <option value="Teniente Coronel">Teniente Coronel</option>
                            <option value="Mayor">Mayor</option>
                            <option value="Capitán">Capitán</option>
                            <option value="Primer Teniente">Primer Teniente</option>
                            <option value="Teniente">Teniente</option>
                            <option value="Sargento Mayor">Sargento Mayor</option>
                            <option value="Sargento">Sargento</option>
                            <option value="Cabo">Cabo</option>
                            <option value="Soldado">Soldado</option>
                            <option value="Cadete">Cadete</option>
                            <option value="Estudiante UNEFA">Estudiante UNEFA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rol / Cargo</label>
                        <select name="role" class="form-select" required>
                            <option value="" disabled selected>Seleccione Rol</option>
                            <option value="Comandante de Sede">Comandante de Sede</option>
                            <option value="Oficial de Comando">Oficial de Comando</option>
                            <option value="Instructor de Tácticas">Instructor de Tácticas</option>
                            <option value="Oficial de Armamento">Oficial de Armamento</option>
                            <option value="Centinela / Guardia">Centinela / Guardia</option>
                            <option value="Estudiante Militar">Estudiante Militar</option>
                            <option value="Docente">Docente</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado Operacional</label>
                    <select name="status" class="form-select" required>
                        <option value="Activo" selected>Activo</option>
                        <option value="Comisión de Servicio">Comisión de Servicio</option>
                        <option value="Licencia Médica">Licencia Médica</option>
                        <option value="Retirado">Retirado / Inactivo</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-input" placeholder="ej. 0412-1234567" value="{{ old('phone') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-input" placeholder="ej. oficial@unefa.edu.ve" value="{{ old('email') }}">
                    </div>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar Registro
                </button>
            </form>
        </div>
    </div>
@endsection
