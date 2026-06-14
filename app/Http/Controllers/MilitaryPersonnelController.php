<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MilitaryPersonnel;

class MilitaryPersonnelController extends Controller
{
    public function index(Request $request)
    {
        $query = MilitaryPersonnel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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
        $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'required|string|unique:military_personnel,cedula',
            'rank' => 'required|string',
            'role' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ], [
            'cedula.unique' => 'Esta Cédula de Identidad ya está registrada en el personal.',
        ]);

        MilitaryPersonnel::create($request->all());

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

        $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'required|string|unique:military_personnel,cedula,' . $person->id,
            'rank' => 'required|string',
            'role' => 'required|string',
            'status' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $person->update($request->all());

        return redirect()->route('admin.personnel.index')->with('success', 'Ficha de personal militar actualizada.');
    }

    public function destroy($id)
    {
        $person = MilitaryPersonnel::findOrFail($id);
        $person->delete();

        return redirect()->route('admin.personnel.index')->with('success', 'Registro de personal militar eliminado de forma segura.');
    }
}
