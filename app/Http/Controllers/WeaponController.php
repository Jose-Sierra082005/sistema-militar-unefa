<?php

namespace App\Http\Controllers;

use App\Models\MilitaryPersonnel;
use App\Models\Weapon;
use Illuminate\Http\Request;

class WeaponController extends Controller
{
    public function index(Request $request)
    {
        $query = Weapon::with('assignee');

        if ($request->filled('search')) {
            $search = $request->search;
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
        $request->validate([
            'serial' => 'required|string|unique:weapons,serial',
            'type' => 'required|string',
            'model' => 'required|string',
            'condition' => 'required|string',
            'assigned_to' => 'nullable|exists:military_personnel,id',
            'status' => 'required|string',
        ], [
            'serial.unique' => 'Este número de serial de armamento ya se encuentra registrado.',
        ]);

        Weapon::create($request->all());

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

        $request->validate([
            'serial' => 'required|string|unique:weapons,serial,'.$weapon->id,
            'type' => 'required|string',
            'model' => 'required|string',
            'condition' => 'required|string',
            'assigned_to' => 'nullable|exists:military_personnel,id',
            'status' => 'required|string',
        ]);

        $weapon->update($request->all());

        return redirect()->route('admin.armory.index')->with('success', 'Detalles del armamento actualizados.');
    }

    public function destroy($id)
    {
        $weapon = Weapon::findOrFail($id);
        $weapon->delete();

        return redirect()->route('admin.armory.index')->with('success', 'Armamento removido del inventario del parque de armas.');
    }
}
