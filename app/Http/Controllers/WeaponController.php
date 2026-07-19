<?php

namespace App\Http\Controllers;

use App\Models\MilitaryPersonnel;
use App\Models\Weapon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class WeaponController — BlueTeam (Avance #6)
 *
 * Seguridad implementada:
 *  - Validación estricta de tipos, longitudes y valores enumerados para armamento
 *  - Sanitización de entradas antes de cualquier operación de persistencia
 *  - Logs de auditoría con identificación del administrador actuante
 *  - Uso de validated() para evitar mass-assignment sin validación previa
 */
class WeaponController extends Controller
{
    // Tipos de armamento permitidos en el sistema SIAM
    private const ALLOWED_TYPES = [
        'Fusil de Asalto', 'Pistola', 'Escopeta', 'Subfusil',
        'Ametralladora', 'Fusil de Precisión', 'Lanzagranadas',
    ];

    // Estados de condición física del armamento
    private const ALLOWED_CONDITIONS = [
        'Excelente', 'Operativo', 'En Mantenimiento', 'Inoperante',
    ];

    // Estado de custodia del armamento
    private const ALLOWED_STATUSES = [
        'Resguardado', 'Asignado', 'Mantenimiento',
    ];

    public function index(Request $request)
    {
        $query = Weapon::with('assignee');

        if ($request->filled('search')) {
            // Sanitizar búsqueda — limitar longitud para prevenir ataques
            $search = strip_tags(trim($request->search));
            $search = mb_substr($search, 0, 100);
            $query->where(function ($q) use ($search) {
                $q->where('serial', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('condition', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $weapons = $query->orderBy('type', 'asc')->paginate(10);

        return view('admin.armory.index', compact('weapons'));
    }

    public function create()
    {
        $personnel = MilitaryPersonnel::orderBy('name', 'asc')->get();

        return view('admin.armory.create', compact('personnel'));
    }

    public function store(Request $request)
    {
        // ─── BlueTeam: Sanitización de entradas de texto libre ────────────
        $request->merge([
            'serial' => strtoupper(strip_tags(trim($request->serial ?? ''))),
            'model'  => strip_tags(trim($request->model ?? '')),
        ]);

        // ─── BlueTeam: Validación estricta con tipos, longitudes y enumerados ──
        $validated = $request->validate([
            'serial'      => 'required|string|max:50|regex:/^[A-Z0-9\-]{3,50}$/|unique:weapons,serial',
            'type'        => 'required|string|in:'.implode(',', self::ALLOWED_TYPES),
            'model'       => 'required|string|max:100',
            'condition'   => 'required|string|in:'.implode(',', self::ALLOWED_CONDITIONS),
            'assigned_to' => 'nullable|integer|exists:military_personnel,id',
            'status'      => 'required|string|in:'.implode(',', self::ALLOWED_STATUSES),
        ], [
            'serial.regex'    => 'El serial debe contener solo letras mayúsculas, números y guiones (Ej: AK-12345).',
            'serial.unique'   => 'Este número de serial de armamento ya se encuentra registrado.',
            'type.in'         => 'El tipo de armamento seleccionado no es válido en el sistema.',
            'condition.in'    => 'La condición del armamento no es válida.',
            'status.in'       => 'El estado de custodia no es válido.',
            'assigned_to.exists' => 'El efectivo militar seleccionado no existe en el sistema.',
        ]);

        Weapon::create($validated);

        Log::info('Armamento registrado en el parque de armas.', [
            'action'   => 'weapon.store',
            'serial'   => $validated['serial'],
            'type'     => $validated['type'],
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.armory.index')->with('success', 'Armamento registrado con éxito en el parque de armas.');
    }

    public function edit($id)
    {
        $weapon = Weapon::findOrFail($id);
        $personnel = MilitaryPersonnel::orderBy('name', 'asc')->get();

        return view('admin.armory.edit', compact('weapon', 'personnel'));
    }

    public function update(Request $request, $id)
    {
        $weapon = Weapon::findOrFail($id);

        // ─── BlueTeam: Sanitización de entradas ──────────────────────────
        $request->merge([
            'serial' => strtoupper(strip_tags(trim($request->serial ?? ''))),
            'model'  => strip_tags(trim($request->model ?? '')),
        ]);

        $validated = $request->validate([
            'serial'      => 'required|string|max:50|regex:/^[A-Z0-9\-]{3,50}$/|unique:weapons,serial,'.$weapon->id,
            'type'        => 'required|string|in:'.implode(',', self::ALLOWED_TYPES),
            'model'       => 'required|string|max:100',
            'condition'   => 'required|string|in:'.implode(',', self::ALLOWED_CONDITIONS),
            'assigned_to' => 'nullable|integer|exists:military_personnel,id',
            'status'      => 'required|string|in:'.implode(',', self::ALLOWED_STATUSES),
        ], [
            'serial.regex' => 'El serial debe contener solo letras mayúsculas, números y guiones.',
            'type.in'      => 'El tipo de armamento seleccionado no es válido en el sistema.',
            'condition.in' => 'La condición del armamento no es válida.',
            'status.in'    => 'El estado de custodia no es válido.',
        ]);

        $weapon->update($validated);

        Log::info('Armamento actualizado.', [
            'action'    => 'weapon.update',
            'weapon_id' => $weapon->id,
            'serial'    => $weapon->serial,
            'admin_id'  => auth()->id(),
        ]);

        return redirect()->route('admin.armory.index')->with('success', 'Detalles del armamento actualizados.');
    }

    public function destroy($id)
    {
        $weapon = Weapon::findOrFail($id);

        Log::warning('Armamento removido del inventario.', [
            'action'    => 'weapon.destroy',
            'weapon_id' => $weapon->id,
            'serial'    => $weapon->serial,
            'type'      => $weapon->type,
            'admin_id'  => auth()->id(),
        ]);

        $weapon->delete();

        return redirect()->route('admin.armory.index')->with('success', 'Armamento removido del inventario del parque de armas.');
    }
}
