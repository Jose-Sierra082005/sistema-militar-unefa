@extends('layouts.admin')

@section('title', 'Registrar Armamento - SIAM')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.armory.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inventario
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Registrar Armamento Oficial
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Registre el ingreso de material de guerra, fusiles o armamento reglamentario en el inventario del Parque de Armas.
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
                <i class="fa-solid fa-square-plus"></i>
                <span>Alta de Material de Guerra</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.armory.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Número de Serial / Registro</label>
                    <input type="text" name="serial" class="form-input" placeholder="ej. AK103-99827" value="{{ old('serial') }}" required style="font-family: 'Share Tech Mono', monospace; letter-spacing: 1px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Tipo de Armamento</label>
                        <select name="type" class="form-select" required>
                            <option value="" disabled selected>Seleccione Tipo</option>
                            <option value="Fusil de Asalto">Fusil de Asalto</option>
                            <option value="Pistola">Pistola</option>
                            <option value="Escopeta">Escopeta</option>
                            <option value="Subfusil">Subfusil</option>
                            <option value="Ametralladora">Ametralladora</option>
                            <option value="Fusil de Precisión">Fusil de Precisión</option>
                            <option value="Lanzagranadas">Lanzagranadas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Modelo / Calibre</label>
                        <input type="text" name="model" class="form-input" placeholder="ej. AK-103 (7.62x39mm)" value="{{ old('model') }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Estado de Conservación</label>
                        <select name="condition" class="form-select" required>
                            <option value="Excelente">Excelente</option>
                            <option value="Operativo" selected>Operativo</option>
                            <option value="En Mantenimiento">En Mantenimiento</option>
                            <option value="Inoperante">Inoperante / Dañado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estatus Inicial</label>
                        <select name="status" class="form-select" required>
                            <option value="Resguardado" selected>Resguardado (En Bóveda)</option>
                            <option value="Asignado">Asignado a Efectivo</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Personal Asignado (Responsable)</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Bóveda / Sin Asignación Específica</option>
                        @foreach($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('assigned_to') == $person->id ? 'selected' : '' }}>
                                {{ $person->name }} ({{ $person->rank }} - C.I: {{ $person->cedula }})
                            </option>
                        @endforeach
                    </select>
                    <p style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 5px; font-style: italic;">
                        * Seleccione un efectivo militar si el arma va a estar asignada directamente para servicio.
                    </p>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar Armamento
                </button>
            </form>
        </div>
    </div>
@endsection
