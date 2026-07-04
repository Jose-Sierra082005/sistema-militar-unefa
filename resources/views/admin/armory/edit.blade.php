@extends('layouts.admin')

@section('title', 'Editar Armamento - Tactic Force')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.armory.index') }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 0.8rem; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inventario
        </a>
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-top: 10px;">
            Editar Ficha de Armamento
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Modifique los datos técnicos, el estado de conservación o la asignación de este material de guerra.
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
                <i class="fa-solid fa-file-pen"></i>
                <span>Formulario de Edición de Armas</span>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.armory.update', $weapon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Número de Serial / Registro</label>
                    <input type="text" name="serial" class="form-input" value="{{ old('serial', $weapon->serial) }}" required style="font-family: 'Share Tech Mono', monospace; letter-spacing: 1px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Tipo de Armamento</label>
                        <select name="type" class="form-select" required>
                            <option value="Fusil de Asalto" {{ $weapon->type == 'Fusil de Asalto' ? 'selected' : '' }}>Fusil de Asalto</option>
                            <option value="Pistola" {{ $weapon->type == 'Pistola' ? 'selected' : '' }}>Pistola</option>
                            <option value="Escopeta" {{ $weapon->type == 'Escopeta' ? 'selected' : '' }}>Escopeta</option>
                            <option value="Subfusil" {{ $weapon->type == 'Subfusil' ? 'selected' : '' }}>Subfusil</option>
                            <option value="Ametralladora" {{ $weapon->type == 'Ametralladora' ? 'selected' : '' }}>Ametralladora</option>
                            <option value="Fusil de Precisión" {{ $weapon->type == 'Fusil de Precisión' ? 'selected' : '' }}>Fusil de Precisión</option>
                            <option value="Lanzagranadas" {{ $weapon->type == 'Lanzagranadas' ? 'selected' : '' }}>Lanzagranadas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Modelo / Calibre</label>
                        <input type="text" name="model" class="form-input" value="{{ old('model', $weapon->model) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Estado de Conservación</label>
                        <select name="condition" class="form-select" required>
                            <option value="Excelente" {{ $weapon->condition == 'Excelente' ? 'selected' : '' }}>Excelente</option>
                            <option value="Operativo" {{ $weapon->condition == 'Operativo' ? 'selected' : '' }}>Operativo</option>
                            <option value="En Mantenimiento" {{ $weapon->condition == 'En Mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                            <option value="Inoperante" {{ $weapon->condition == 'Inoperante' ? 'selected' : '' }}>Inoperante / Dañado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estatus Técnico</label>
                        <select name="status" class="form-select" required>
                            <option value="Resguardado" {{ $weapon->status == 'Resguardado' ? 'selected' : '' }}>Resguardado (En Bóveda)</option>
                            <option value="Asignado" {{ $weapon->status == 'Asignado' ? 'selected' : '' }}>Asignado a Efectivo</option>
                            <option value="Mantenimiento" {{ $weapon->status == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Personal Asignado (Responsable)</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Bóveda / Sin Asignación Específica</option>
                        @foreach($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('assigned_to', $weapon->assigned_to) == $person->id ? 'selected' : '' }}>
                                {{ $person->name }} ({{ $person->rank }} - C.I: {{ $person->cedula }})
                            </option>
                        @endforeach
                    </select>
                    <p style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 5px; font-style: italic;">
                        * Si el arma está en mantenimiento o resguardada, se recomienda retirar la asignación activa.
                    </p>
                </div>

                <button type="submit" class="btn-tactical" style="margin-top: 10px;">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
@endsection
