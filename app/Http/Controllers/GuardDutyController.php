<?php

namespace App\Http\Controllers;

use App\Models\GuardShift;
use App\Models\MilitaryPersonnel;
use Illuminate\Http\Request;

class GuardDutyController extends Controller
{
    public function index(Request $request)
    {
        $query = GuardShift::with('personnel');

        if ($request->filled('search')) {
            $search = $request->search;
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

        // Get active personnel for assignment dropdown
        $personnel = MilitaryPersonnel::where('status', 'Activo')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.guards.index', compact('shifts', 'personnel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:military_personnel,id',
            'post' => 'required|string|max:255',
            'shift_time' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        GuardShift::create($request->all());

        return redirect()->route('admin.guards.index')->with('success', 'Turno de guardia asignado y planificado con éxito.');
    }

    public function destroy($id)
    {
        $shift = GuardShift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.guards.index')->with('success', 'Turno de guardia cancelado y removido del sistema.');
    }
}
