<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\LessonCompletion;
use App\Models\MilitaryPersonnel;
use App\Models\Weapon;
use App\Models\GuardShift;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $courses = Course::with('lessons')->get();

        // Calculate progress for each course
        foreach ($courses as $course) {
            $lessonIds = $course->lessons->pluck('id')->toArray();
            if (empty($lessonIds)) {
                $course->progress_percent = 0;
                $course->completed_count = 0;
            } else {
                $completedCount = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->count();
                $course->completed_count = $completedCount;
                $course->progress_percent = round(($completedCount / count($lessonIds)) * 100);
            }
        }

        return view('student.dashboard', compact('courses', 'user'));
    }

    public function personnel(Request $request)
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
        return view('student.personnel.index', compact('personnel'));
    }

    public function armory(Request $request)
    {
        $query = Weapon::with('assignee');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $weapons = $query->orderBy('serial', 'asc')->paginate(10);
        return view('student.armory.index', compact('weapons'));
    }

    public function guards(Request $request)
    {
        $query = GuardShift::with('personnel');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('post', 'like', "%{$search}%")
                  ->orWhere('shift_time', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('personnel', function($qp) use ($search) {
                      $qp->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $shifts = $query->orderBy('date', 'desc')->paginate(10);
        return view('student.guards.index', compact('shifts'));
    }

    public function showCourse($id)
    {
        $user = Auth::user();
        $course = Course::with(['lessons' => function($query) {
            $query->orderBy('order', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($id);

        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->pluck('lesson_id')
            ->toArray();

        // Process sequential unlocking logic
        $lessons = $course->lessons;
        $unlockedNext = true; // First lesson is always unlocked

        foreach ($lessons as $index => $lesson) {
            $lesson->is_completed = in_array($lesson->id, $completedLessonIds);
            $lesson->is_unlocked = $unlockedNext;
            
            // The next lesson is unlocked only if this one is completed
            $unlockedNext = $lesson->is_completed;
        }

        return view('student.courses.show', compact('course', 'lessons', 'user'));
    }

    public function showLesson($id)
    {
        $user = Auth::user();
        $lesson = Lesson::with('course')->findOrFail($id);

        // Verify sequential unlocking logic
        $course = Course::with(['lessons' => function($query) {
            $query->orderBy('order', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($lesson->course_id);

        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->pluck('lesson_id')
            ->toArray();

        $unlocked = false;
        $unlockedNext = true;

        foreach ($course->lessons as $l) {
            if ($l->id === $lesson->id) {
                $unlocked = $unlockedNext;
                break;
            }
            $unlockedNext = in_array($l->id, $completedLessonIds);
        }

        if (!$unlocked) {
            return redirect()->route('student.courses.show', $lesson->course_id)
                ->with('error', 'Esta lección táctica se encuentra bloqueada. Complete los cuestionarios previos.');
        }

        $isCompleted = in_array($lesson->id, $completedLessonIds);

        return view('student.lessons.show', compact('lesson', 'isCompleted', 'user'));
    }

    public function downloadPdf($id)
    {
        $lesson = Lesson::with('course')->findOrFail($id);
        
        $content = "=================================================================\n";
        $content .= "             SISTEMA MILITAR DE APRENDIZAJE UNEFA\n";
        $content .= "             MANUAL DE ESTUDIO TÁCTICO ACADÉMICO\n";
        $content .= "=================================================================\n\n";
        $content .= "CURSO: " . mb_strtoupper($lesson->course->title) . "\n";
        $content .= "CATEGORÍA: " . mb_strtoupper($lesson->course->category) . "\n";
        $content .= "DIFICULTAD: " . mb_strtoupper($lesson->course->difficulty) . "\n";
        $content .= "LECCIÓN: " . mb_strtoupper($lesson->title) . "\n";
        $content .= "FECHA DE EMISIÓN: " . date('d/m/Y H:i:s') . "\n";
        $content .= "-----------------------------------------------------------------\n\n";
        $content .= "CONTENIDO DE LA LECCIÓN:\n\n";
        
        // Clean HTML tags and replace paragraph/breaks with clean newlines
        $cleanContent = strip_tags(str_replace(['<p>', '</p>', '<br>', '<br/>', '</li>', '</ul>'], ["", "\n", "\n", "\n", "\n", ""], $lesson->content));
        $content .= trim($cleanContent) . "\n\n";
        
        $content .= "-----------------------------------------------------------------\n";
        $content .= "                  FIN DEL MATERIAL DE ESTUDIO\n";
        $content .= "          DOCUMENTO DE CONSULTA ESTUDIANTIL - UNEFA FALCÓN\n";
        $content .= "=================================================================\n";

        $filename = 'Manual_' . str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $lesson->title) . '.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function startQuiz($lesson_id)
    {
        $user = Auth::user();
        $lesson = Lesson::with(['course', 'questions.options'])->findOrFail($lesson_id);

        $questions = $lesson->questions;

        // If no questions are registered for this lesson, use dynamic mock questions to prevent block
        if ($questions->isEmpty()) {
            $questions = collect([
                $this->createFallbackQuestion($lesson, 1),
                $this->createFallbackQuestion($lesson, 2),
                $this->createFallbackQuestion($lesson, 3),
            ]);
        }

        // Format questions for JavaScript
        $formattedQuestions = $questions->map(function($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'points' => $q->points,
                'options' => $q->options->map(function($o) {
                    return [
                        'id' => $o->id,
                        'option_text' => $o->option_text,
                        'is_correct' => $o->is_correct,
                    ];
                })->shuffle()->values()->toArray() // Shuffle options for a true quiz experience
            ];
        })->toArray();

        return view('student.lessons.quiz', compact('lesson', 'formattedQuestions', 'user'));
    }

    public function completeQuiz(Request $request, $lesson_id)
    {
        $user = Auth::user();
        $lesson = Lesson::findOrFail($lesson_id);

        $alreadyCompleted = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$alreadyCompleted) {
            // Create completion record
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ]);

            // Add points (XP) to student profile
            $pointsEarned = (int) $request->input('points_earned', 50);
            $user->increment('points', $pointsEarned);

            return redirect()->route('student.courses.show', $lesson->course_id)
                ->with('success', "¡Excelente Soldado! Ha completado la lección '{$lesson->title}' y ha ganado +{$pointsEarned} XP.");
        }

        return redirect()->route('student.courses.show', $lesson->course_id)
            ->with('info', "Lección '{$lesson->title}' completada. (El repaso no sumó puntos adicionales).");
    }

    /**
     * Helper to build dynamic fallback questions to ensure the app is fully bulletproof.
     */
    private function createFallbackQuestion($lesson, $number)
    {
        $q = new Question();
        $q->id = 1000 + $number;
        $q->points = 15;

        if ($number === 1) {
            $q->question_text = "¿Cuál es el objetivo principal de la lección '{$lesson->title}'?";
            $q->options = collect([
                (object)['id' => 2001, 'option_text' => 'Comprender los principios técnicos y teóricos expuestos en el manual', 'is_correct' => true],
                (object)['id' => 2002, 'option_text' => 'Aumentar la velocidad de tiro en ráfaga', 'is_correct' => false],
                (object)['id' => 2003, 'option_text' => 'Realizar ejercicios de aptitud física sin supervisión', 'is_correct' => false],
                (object)['id' => 2004, 'option_text' => 'Ignorar las medidas de seguridad del parque de armas', 'is_correct' => false],
            ]);
        } elseif ($number === 2) {
            $q->question_text = "En relación con '{$lesson->title}', ¿qué aspecto describe el protocolo reglamentario de la UNEFA?";
            $q->options = collect([
                (object)['id' => 2011, 'option_text' => 'La disciplina académica, el respeto a los mandos y la seguridad operacional', 'is_correct' => true],
                (object)['id' => 2012, 'option_text' => 'El abandono de guardia sin relevo previo', 'is_correct' => false],
                (object)['id' => 2013, 'option_text' => 'El uso inapropiado del armamento de estudio', 'is_correct' => false],
                (object)['id' => 2014, 'option_text' => 'La omisión de informes de bitácora', 'is_correct' => false],
            ]);
        } else {
            $q->question_text = "Completar la afirmación táctica: El adiestramiento en '{$lesson->title}' requiere...";
            $q->options = collect([
                (object)['id' => 2021, 'option_text' => 'Estudio analítico de la teoría, simulaciones de guardia y disciplina táctica', 'is_correct' => true],
                (object)['id' => 2022, 'option_text' => 'Conexiones no cifradas en la nube del sistema', 'is_correct' => false],
                (object)['id' => 2023, 'option_text' => 'Uso del armamento sin autorización de un oficial superior', 'is_correct' => false],
                (object)['id' => 2024, 'option_text' => 'Evaluar a otros cadetes sin tener el rango de oficial', 'is_correct' => false],
            ]);
        }

        return $q;
    }
}
