<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Evaluation;
use App\Models\MilitaryPersonnel;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = Evaluation::with(['personnel', 'course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('evaluator', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($qc) use ($search) {
                        $qc->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('personnel', function ($qp) use ($search) {
                        $qp->where('name', 'like', "%{$search}%")
                            ->orWhere('cedula', 'like', "%{$search}%");
                    });
            });
        }

        $evaluations = $query->orderBy('date', 'desc')
            ->paginate(10);

        // Get personnel for evaluations assignment dropdown
        $personnel = MilitaryPersonnel::orderBy('name', 'asc')->get();

        // Get active courses for evaluations dropdown
        $courses = Course::orderBy('title', 'asc')->get();

        return view('admin.evaluations.index', compact('evaluations', 'personnel', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:military_personnel,id',
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|integer|min:0|max:20',
            'evaluator' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'date' => 'required|date',
        ], [
            'score.min' => 'La nota mínima es 0.',
            'score.max' => 'La nota máxima es 20.',
        ]);

        Evaluation::create($request->all());

        return redirect()->route('admin.evaluations.index')->with('success', 'Calificación registrada y cargada con éxito.');
    }

    public function destroy($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();

        return redirect()->route('admin.evaluations.index')->with('success', 'Calificación académica militar eliminada del historial.');
    }
}
