<?php

namespace App\Http\Controllers;

use App\Models\MilitaryPersonnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class MilitaryPersonnelController — BlueTeam (Avance #6)
 *
 * Seguridad implementada:
 *  - Validación estricta de tipos, longitudes máximas y valores enumerados (in:)
 *  - Sanitización con strip_tags() antes de persistir en base de datos
 *  - Uso de validated() en lugar de all() para evitar mass-assignment no validado
 *  - Logs de auditoría con Correlation ID automático (via contexto global de Log)
 */
class MilitaryPersonnelController extends Controller
{
    // Rangos militares permitidos (lista controlada — BlueTeam)
    private const ALLOWED_RANKS = [
        'General de División', 'General de Brigada', 'Coronel',
        'Teniente Coronel', 'Mayor', 'Capitán', 'Primer Teniente',
        'Teniente', 'Sargento Mayor', 'Sargento', 'Cabo',
        'Soldado', 'Cadete', 'Estudiante UNEFA',
    ];

    // Roles funcionales del personal
    private const ALLOWED_ROLES = [
        'Comandante', 'Oficial de Operaciones', 'Oficial de Inteligencia',
        'Oficial de Guardia', 'Oficial de Comando', 'Instructor', 'Médico',
        'Logística', 'Comunicaciones', 'Mantenimiento', 'Administrativo',
        'Estudiante', 'Operaciones',
    ];

    // Estados operativos del personal
    private const ALLOWED_STATUSES = ['Activo', 'Inactivo', 'Suspendido', 'Baja'];

    public function index(Request $request)
    {
        $query = MilitaryPersonnel::query();

        if ($request->filled('search')) {
            // Sanitizar la búsqueda para evitar inyección de patrones peligrosos
            $search = strip_tags(trim($request->search));
            $search = mb_substr($search, 0, 100); // límite de 100 caracteres
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cedula', 'like', "%{$search}%")
                    ->orWhere('rank', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $personnel = $query->orderBy('name', 'asc')->paginate(10);

        return view('admin.personnel.index', compact('personnel'));
    }

    public function create()
    {
        return view('admin.personnel.create');
    }

    public function store(Request $request)
    {
        // ─── BlueTeam: Sanitización de entradas antes de validar ──────────
        $request->merge([
            'name'   => strip_tags(trim($request->name ?? '')),
            'cedula' => strip_tags(trim($request->cedula ?? '')),
            'phone'  => strip_tags(trim($request->phone ?? '')),
            'email'  => strip_tags(trim($request->email ?? '')),
        ]);

        // ─── BlueTeam: Validación estricta con tipos, longitudes y enumerados ──
        $validated = $request->validate([
            'name'   => 'required|string|min:3|max:150|regex:/^[\pL\s\.\-]+$/u',
            'cedula' => 'required|string|max:20|unique:military_personnel,cedula|regex:/^[VEve]?\d{6,9}$/',
            'rank'   => 'required|string|in:'.implode(',', self::ALLOWED_RANKS),
            'role'   => 'required|string|in:'.implode(',', self::ALLOWED_ROLES),
            'status' => 'required|string|in:'.implode(',', self::ALLOWED_STATUSES),
            'phone'  => 'nullable|string|max:20|regex:/^[\d\s\+\-\(\)]{7,20}$/',
            'email'  => 'nullable|email|max:150',
        ], [
            'name.regex'   => 'El nombre solo puede contener letras, espacios, puntos y guiones.',
            'cedula.regex' => 'El formato de cédula es inválido (Ej: V12345678).',
            'cedula.unique' => 'Esta Cédula de Identidad ya está registrada en el personal.',
            'rank.in'      => 'El rango seleccionado no es válido en el sistema.',
            'role.in'      => 'El rol funcional seleccionado no es válido.',
            'status.in'    => 'El estado operativo seleccionado no es válido.',
            'phone.regex'  => 'El formato del teléfono es inválido.',
        ]);

        MilitaryPersonnel::create($validated);

        Log::info('Personal militar registrado.', [
            'action'   => 'personnel.store',
            'cedula'   => $validated['cedula'],
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'Personal militar registrado con éxito.');
    }

    public function edit($id)
    {
        $person = MilitaryPersonnel::findOrFail($id);

        return view('admin.personnel.edit', compact('person'));
    }

    public function update(Request $request, $id)
    {
        $person = MilitaryPersonnel::findOrFail($id);

        // ─── BlueTeam: Sanitización de entradas ──────────────────────────
        $request->merge([
            'name'   => strip_tags(trim($request->name ?? '')),
            'cedula' => strip_tags(trim($request->cedula ?? '')),
            'phone'  => strip_tags(trim($request->phone ?? '')),
            'email'  => strip_tags(trim($request->email ?? '')),
        ]);

        $validated = $request->validate([
            'name'   => 'required|string|min:3|max:150|regex:/^[\pL\s\.\-]+$/u',
            'cedula' => 'required|string|max:20|unique:military_personnel,cedula,'.$person->id.'|regex:/^[VEve]?\d{6,9}$/',
            'rank'   => 'required|string|in:'.implode(',', self::ALLOWED_RANKS),
            'role'   => 'required|string|in:'.implode(',', self::ALLOWED_ROLES),
            'status' => 'required|string|in:'.implode(',', self::ALLOWED_STATUSES),
            'phone'  => 'nullable|string|max:20|regex:/^[\d\s\+\-\(\)]{7,20}$/',
            'email'  => 'nullable|email|max:150',
        ], [
            'name.regex'   => 'El nombre solo puede contener letras, espacios, puntos y guiones.',
            'cedula.regex' => 'El formato de cédula es inválido (Ej: V12345678).',
            'rank.in'      => 'El rango seleccionado no es válido en el sistema.',
            'role.in'      => 'El rol funcional seleccionado no es válido.',
            'status.in'    => 'El estado operativo seleccionado no es válido.',
            'phone.regex'  => 'El formato del teléfono es inválido.',
        ]);

        $person->update($validated);

        Log::info('Ficha de personal militar actualizada.', [
            'action'       => 'personnel.update',
            'personnel_id' => $person->id,
            'admin_id'     => auth()->id(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'Ficha de personal militar actualizada.');
    }

    public function destroy($id)
    {
        $person = MilitaryPersonnel::findOrFail($id);

        Log::warning('Registro de personal militar eliminado.', [
            'action'       => 'personnel.destroy',
            'personnel_id' => $person->id,
            'cedula'       => $person->cedula,
            'admin_id'     => auth()->id(),
        ]);

        $person->delete();

        return redirect()->route('admin.personnel.index')->with('success', 'Registro de personal militar eliminado de forma segura.');
    }
}
