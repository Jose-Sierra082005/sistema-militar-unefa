<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CourseController
 * Administra el catálogo de cursos, lecciones y banco de preguntas de la
 * plataforma SIAM en el Panel de Administrador (CMS).
 */
class CourseController extends Controller
{
    /**
     * Muestra la lista paginada de cursos registrados en el sistema,
     * permitiendo búsquedas filtradas por título, categoría o dificultad.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('difficulty', 'like', "%{$search}%");
            });
        }

        $courses = $query->withCount('lessons')->orderBy('title', 'asc')->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|string|max:255',
        ]);

        $description = $this->sanitizeRichText($request->description);

        if ($description === '') {
            return back()->withErrors(['description' => 'La descripción del curso es obligatoria.'])->withInput();
        }

        Course::create([
            'title' => $request->title,
            'description' => $description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Curso académico militar registrado con éxito.');
    }

    public function show($id)
    {
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('order', 'asc')->orderBy('id', 'asc');
        }, 'lessons.questions.options'])->findOrFail($id);

        return view('admin.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);

        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|string|max:255',
        ]);

        $description = $this->sanitizeRichText($request->description);

        if ($description === '') {
            return back()->withErrors(['description' => 'La descripción del curso es obligatoria.'])->withInput();
        }

        $course->update([
            'title' => $request->title,
            'description' => $description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Curso académico actualizado con éxito.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Curso académico y su temario de lecciones eliminados del sistema.');
    }

    // Lecciones asociadas
    public function storeLesson(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
        ]);

        $content = $this->sanitizeRichText($request->content, allowHeadings: true);

        if ($content === '') {
            return back()->withErrors(['content' => 'El contenido de la lección es obligatorio.'])->withInput();
        }

        $course->lessons()->create([
            'title' => $request->title,
            'content' => $content,
            'order' => $request->order,
        ]);

        return redirect()->route('admin.courses.show', $course->id)->with('success', 'Nueva lección teórica/táctica agregada al curso.');
    }

    public function destroyLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $courseId = $lesson->course_id;
        $lesson->delete();

        return redirect()->route('admin.courses.show', $courseId)->with('success', 'Lección eliminada de la planificación del curso.');
    }

    public function editLesson($id)
    {
        $lesson = Lesson::with('course')->findOrFail($id);

        return view('admin.lessons.edit', compact('lesson'));
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
        ]);

        $content = $this->sanitizeRichText($request->content, allowHeadings: true);

        if ($content === '') {
            return back()->withErrors(['content' => 'El contenido de la lección es obligatorio.'])->withInput();
        }

        $lesson->update([
            'title' => $request->title,
            'content' => $content,
            'order' => $request->order,
        ]);

        return redirect()->route('admin.courses.show', $lesson->course_id)
            ->with('success', 'Lección actualizada correctamente.');
    }

    public function editQuestion($id)
    {
        $question = Question::with(['lesson.course', 'options' => fn ($q) => $q->orderBy('id')])->findOrFail($id);

        return view('admin.questions.edit', compact('question'));
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = Question::with(['options' => fn ($q) => $q->orderBy('id')])->findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'required|array|size:4',
            'options.*' => 'required|string|max:500',
            'correct_option' => 'required|integer|between:0,3',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'points' => $request->points,
        ]);

        foreach ($question->options as $index => $option) {
            $option->update([
                'option_text' => $request->options[$index],
                'is_correct' => ($index == $request->correct_option),
            ]);
        }

        return redirect()->route('admin.courses.show', $question->lesson->course_id)
            ->with('success', 'Pregunta del cuestionario actualizada correctamente.');
    }

    public function storeQuestion(Request $request, $lesson_id)
    {
        $lesson = Lesson::findOrFail($lesson_id);

        $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'options' => 'required|array|size:4',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|between:0,3',
        ]);

        $question = Question::create([
            'lesson_id' => $lesson->id,
            'question_text' => $request->question_text,
            'points' => $request->points,
        ]);

        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index == $request->correct_option),
            ]);
        }

        return redirect()->route('admin.courses.show', $lesson->course_id)->with('success', 'Pregunta agregada con éxito al cuestionario de la lección.');
    }

    public function destroyQuestion($id)
    {
        $question = Question::with('lesson')->findOrFail($id);
        $courseId = $question->lesson->course_id;
        $question->delete();

        return redirect()->route('admin.courses.show', $courseId)->with('success', 'Pregunta eliminada con éxito del cuestionario.');
    }

    private function sanitizeRichText(?string $html, bool $allowHeadings = false): string
    {
        $allowed = $allowHeadings
            ? '<p><br><strong><em><u><h3><ul><ol><li>'
            : '<p><br><strong><em><u><ul><ol><li>';

        $clean = strip_tags($html ?? '', $allowed);
        $plain = trim(html_entity_decode(strip_tags($clean)));

        return $plain === '' ? '' : $clean;
    }
}
