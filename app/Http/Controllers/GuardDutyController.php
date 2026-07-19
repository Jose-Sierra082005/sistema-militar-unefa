<?php

namespace App\Http\Controllers;

use App\Models\GuardShift;
use App\Models\MilitaryPersonnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class GuardDutyController — BlueTeam (Avance #6)
 *
 * Seguridad implementada:
 *  - Validación de formato de fecha y valores enumerados (in:) para turnos
 *  - Autorización explícita: solo el admin puede gestionar guardias (via middleware)
 *  - Logs de auditoría de asignación y eliminación de turnos
 */
class GuardDutyController extends Controller
{
    // Puestos de guardia permitidos en el sistema SIAM
    private const ALLOWED_POSTS = [
        'Garita Principal (Acceso)',
        'Garita 2 (Oeste)',
        'Garita 3 (Este)',
        'Polvorín / Bóveda de Armas',
        'Entrada Vehicular',
        'Patrulla Perimetral Nocturna',
        'Oficial de Guardia (Comando)',
    ];

    // Turnos operativos permitidos (nomenclatura NATO)
    private const ALLOWED_SHIFTS = [
        'Turno Alpha: 00:00 - 06:00',
        'Turno Bravo: 06:00 - 12:00',
        'Turno Charlie: 12:00 - 18:00',
        'Turno Delta: 18:00 - 00:00',
    ];

    // Estados operativos del turno de guardia
    private const ALLOWED_STATUSES = ['Programado', 'Activo', 'Completado'];

    public function index(Request $request)
    {
        $query = GuardShift::with('personnel');

        if ($request->filled('search')) {
            $search = strip_tags(trim($request->search));
            $search = mb_substr($search, 0, 100);
            $query->where(function ($q) use ($search) {
                $q->where('post', 'like', "%{$search}%")
                    ->orWhere('shift_time', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('personnel', function ($qp) use ($search) {
                        $qp->where('name', 'like', "%{$search}%")
                            ->orWhere('cedula', 'like', "%{$search}%");
                    });
            });
        }

        $shifts = $query->orderBy('date', 'desc')
            ->orderBy('shift_time', 'asc')
            ->paginate(10);

        // Solo personal activo disponible para asignación
        $personnel = MilitaryPersonnel::where('status', 'Activo')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.guards.index', compact('shifts', 'personnel'));
    }

    public function store(Request $request)
    {
        // ─── BlueTeam: Validación estricta de todos los campos del turno ──
        $validated = $request->validate([
            'personnel_id' => 'required|integer|exists:military_personnel,id',
            'post'         => 'required|string|in:'.implode(',', self::ALLOWED_POSTS),
            'shift_time'   => 'required|string|in:'.implode(',', self::ALLOWED_SHIFTS),
            'date'         => 'required|date|date_format:Y-m-d',
            'status'       => 'required|string|in:'.implode(',', self::ALLOWED_STATUSES),
        ], [
            'personnel_id.exists' => 'El efectivo seleccionado no existe o no está activo en el sistema.',
            'post.in'             => 'El puesto de guardia seleccionado no es válido.',
            'shift_time.in'       => 'El turno seleccionado no corresponde a la nomenclatura operativa del sistema.',
            'date.date_format'    => 'El formato de fecha debe ser AAAA-MM-DD.',
            'status.in'           => 'El estado del turno no es válido.',
        ]);

        GuardShift::create($validated);

        Log::info('Turno de guardia asignado.', [
            'action'       => 'guard.store',
            'personnel_id' => $validated['personnel_id'],
            'post'         => $validated['post'],
            'date'         => $validated['date'],
            'admin_id'     => auth()->id(),
        ]);

        return redirect()->route('admin.guards.index')->with('success', 'Turno de guardia asignado y planificado con éxito.');
    }

    public function destroy($id)
    {
        $shift = GuardShift::findOrFail($id);

        Log::warning('Turno de guardia cancelado.', [
            'action'       => 'guard.destroy',
            'shift_id'     => $shift->id,
            'post'         => $shift->post,
            'date'         => $shift->date,
            'admin_id'     => auth()->id(),
        ]);

        $shift->delete();

        return redirect()->route('admin.guards.index')->with('success', 'Turno de guardia cancelado y removido del sistema.');
    }
}
