<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class EvaluationController
 *
 * Sirve el módulo de Analítica de Progreso Estudiantil del panel administrador.
 * Muestra un dashboard con historial de lecciones completadas por cada estudiante,
 * XP acumulado, precisión promedio y avance por curso.
 */
class EvaluationController extends Controller
{
    /**
     * Muestra el dashboard de analítica de progreso estudiantil.
     */
    public function index(Request $request)
    {
        // ── KPIs globales ────────────────────────────────────────────────
        $totalStudents = User::where('role', 'student')->count();
        $totalCompletions = LessonCompletion::count();
        $avgXp = $totalCompletions > 0
            ? (int) LessonCompletion::avg('xp_earned')
            : 0;
        $avgAccuracy = $totalCompletions > 0
            ? (int) LessonCompletion::avg('accuracy_percent')
            : 0;

        // ── Listado de estudiantes con progreso ───────────────────────────
        $query = User::where('role', 'student');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('cedula', 'like', "%{$s}%");
            });
        }

        $students = $query->orderBy('points', 'desc')
            ->withCount([
                'lessonCompletions as completions_count',
            ])
            ->with([
                'lessonCompletions.lesson.course',
            ])
            ->paginate(10);

        // ── Cursos para el filtro de tabla ─────────────────────────────────
        $courses = Course::orderBy('title', 'asc')->get();

        // ── Progreso por curso para cada estudiante ────────────────────────
        $allCourses = Course::with('lessons')->get();
        foreach ($students as $student) {
            $completedIds = $student->lessonCompletions->pluck('lesson_id')->toArray();

            $student->course_progress = $allCourses->map(function ($course) use ($completedIds) {
                $total = $course->lessons->count();
                $done = collect($completedIds)
                    ->intersect($course->lessons->pluck('id'))
                    ->count();

                return [
                    'title' => $course->title,
                    'category' => $course->category,
                    'total' => $total,
                    'done' => $done,
                    'pct' => $total > 0 ? round(($done / $total) * 100) : 0,
                ];
            })->filter(fn ($c) => $c['total'] > 0)->values();

            // Stats agregadas del estudiante
            $completions = $student->lessonCompletions;
            $student->avg_accuracy = $completions->count() > 0
                ? (int) $completions->avg('accuracy_percent')
                : 0;
            $student->total_xp_from_lessons = $completions->sum('xp_earned');
        }

        return view('admin.evaluations.index', compact(
            'students',
            'courses',
            'totalStudents',
            'totalCompletions',
            'avgXp',
            'avgAccuracy'
        ));
    }
}
