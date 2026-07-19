<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Question;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class StudentPortalController
 * Controla el Portal del Estudiante, sirviendo el mapa de cursos (estilo Duolingo),
 * la visualización de lecciones, quizzes, perfiles académicos y de seguridad.
 */
class StudentPortalController extends Controller
{
    // =========================================================
    // DASHBOARD
    // =========================================================

    /**
     * Muestra el dashboard del estudiante con el progreso acumulado en cada curso militar.
     *
     * @return View
     */
    public function index()
    {
        $user = Auth::user();
        $courses = Course::with('lessons')->get();

        foreach ($courses as $course) {
            $lessonIds = $course->lessons->pluck('id')->toArray();
            if (empty($lessonIds)) {
                $course->progress_percent = 0;
                $course->completed_count = 0;
            } else {
                $completedCount = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)->count();
                $course->completed_count = $completedCount;
                $course->progress_percent = round(($completedCount / count($lessonIds)) * 100);
            }
        }

        return view('student.dashboard', compact('courses', 'user'));
    }

    // =========================================================
    // PERFIL DE USUARIO
    // =========================================================

    public function showProfile()
    {
        $user = Auth::user();
        $totalXp = $user->points ?? 0;
        $completedLessons = LessonCompletion::where('user_id', $user->id)->count();
        $totalCourses = Course::count();

        return view('student.profile.index', compact('user', 'totalXp', 'completedLessons', 'totalCourses'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('student.profile.show')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Cambiar / establecer contrasena.
     * - Usuarios con google_id: no necesitan la contrasena actual (sesion OAuth prueba identidad).
     * - Usuarios normales: deben confirmar la contrasena actual.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $pwRules = [
            'required', 'string', 'min:8', 'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ];

        $pwMessages = [
            'new_password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmacion no coincide.',
            'new_password.regex' => 'Debe incluir mayuscula, minuscula, numero y caracter especial (@$!%*?&).',
        ];

        if ($user->google_id) {
            $request->validate(['new_password' => $pwRules], $pwMessages);
            $user->password = $request->new_password;
            $user->save();

            return redirect()->route('student.profile.show')
                ->with('success', 'Contrasena establecida. Ya puede iniciar sesion con su correo y contrasena.');
        }

        $request->validate(
            array_merge(['current_password' => 'required'], ['new_password' => $pwRules]),
            array_merge(['current_password.required' => 'La contrasena actual es obligatoria.'], $pwMessages)
        );

        if (! \Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contrasena actual no es correcta.'])->withInput();
        }

        $user->password = $request->new_password;
        $user->save();

        return redirect()->route('student.profile.show')
            ->with('success', 'Contrasena actualizada correctamente.');
    }

    // =========================================================
    // DESACTIVACION DE 2FA — verificacion por OTP al correo
    // =========================================================

    /**
     * Genera y envia por correo un codigo OTP de 6 digitos para confirmar
     * la desactivacion del Doble Factor (2FA).
     */
    public function send2FADisableOtp()
    {
        $user = Auth::user();

        if (! $user->two_factor_enabled) {
            return back()->with('error', 'El 2FA no esta activo en su cuenta.');
        }

        $otp = sprintf('%06d', mt_rand(0, 999999));

        // Guardar OTP en sesion (10 minutos de validez)
        session([
            '2fa.disable.otp' => $otp,
            '2fa.disable.expires' => now()->addMinutes(10)->timestamp,
            '2fa.disable.uid' => $user->id,
        ]);

        $sent = EmailService::sendOtpEmail($user->email, $user->name, $otp);

        if ($sent) {
            return back()
                ->with('2fa_otp_sent', true)
                ->with('info_2fa', 'Codigo enviado a '.$user->email.'. Caduca en 10 minutos.');
        }

        return back()->with('error', 'No se pudo enviar el correo de verificacion. Intente nuevamente.');
    }

    /**
     * Verifica el OTP recibido por correo y desactiva el 2FA.
     */
    public function disable2FA(Request $request)
    {
        $request->validate(
            ['disable_otp' => 'required|string|digits:6'],
            [
                'disable_otp.required' => 'El codigo OTP es obligatorio.',
                'disable_otp.digits' => 'El codigo debe ser de 6 digitos numericos.',
            ]
        );

        $user = Auth::user();

        $storedOtp = session('2fa.disable.otp');
        $expires = session('2fa.disable.expires');
        $storedUid = session('2fa.disable.uid');

        if (! $storedOtp || $storedUid !== $user->id) {
            return back()->withErrors(['disable_otp' => 'Solicitud invalida o sesion expirada. Solicite un nuevo codigo.']);
        }

        if (now()->timestamp > $expires) {
            session()->forget(['2fa.disable.otp', '2fa.disable.expires', '2fa.disable.uid']);

            return back()->withErrors(['disable_otp' => 'El codigo OTP ha expirado. Solicite uno nuevo.']);
        }

        if ($request->disable_otp !== $storedOtp) {
            return back()
                ->with('2fa_otp_sent', true)
                ->withErrors(['disable_otp' => 'Codigo incorrecto. Verifique su correo e intente nuevamente.']);
        }

        // Desactivar el 2FA
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        session()->forget(['2fa.disable.otp', '2fa.disable.expires', '2fa.disable.uid']);

        return redirect()->route('student.profile.show')
            ->with('success', 'Doble Factor (2FA) desactivado correctamente. Su cuenta ya no requiere el codigo de Google Authenticator.');
    }

    // =========================================================
    // CURSOS
    // =========================================================

    public function showCourse($id)
    {
        $user = Auth::user();

        // ─── BlueTeam: Autorización explícita ─────────────────────────────
        // Los cursos son accesibles a todos los estudiantes autenticados.
        // La autenticación y el rol 'student' ya fueron verificados por el middleware.
        // Registramos el acceso para trazabilidad.
        $course = Course::with(['lessons' => function ($q) {
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($id);

        Log::info('Estudiante accedió a un curso.', [
            'action'    => 'student.course.view',
            'course_id' => $course->id,
            'user_id'   => $user->id,
        ]);

        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->pluck('lesson_id')->toArray();

        $unlockedNext = true;
        foreach ($course->lessons as $lesson) {
            $lesson->is_completed = in_array($lesson->id, $completedLessonIds);
            $lesson->is_unlocked = $unlockedNext;
            $unlockedNext = $lesson->is_completed;
        }

        return view('student.courses.show', compact('course', 'user',
            'completedLessonIds'))->with('lessons', $course->lessons);
    }

    public function showLesson($id)
    {
        $user = Auth::user();
        $lesson = Lesson::with('course')->findOrFail($id);

        $course = Course::with(['lessons' => function ($q) {
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($lesson->course_id);

        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->pluck('lesson_id')->toArray();

        $unlocked = false;
        $unlockedNext = true;

        foreach ($course->lessons as $l) {
            if ($l->id === $lesson->id) {
                $unlocked = $unlockedNext;
                break;
            }
            $unlockedNext = in_array($l->id, $completedLessonIds);
        }

        if (! $unlocked) {
            // ─── BlueTeam: Bloqueo de acceso secuencial ───────────────────
            // El sistema verifica explícitamente la condición de desbloqueo.
            Log::warning('Intento de acceso a lección bloqueada.', [
                'action'    => 'student.lesson.blocked',
                'lesson_id' => $lesson->id,
                'user_id'   => $user->id,
            ]);

            return redirect()->route('student.courses.show', $lesson->course_id)
                ->with('error', 'Esta leccion esta bloqueada. Complete las anteriores primero.');
        }

        Log::info('Estudiante accedió a una lección.', [
            'action'    => 'student.lesson.view',
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id,
        ]);

        $isCompleted = in_array($lesson->id, $completedLessonIds);

        return view('student.lessons.show', compact('lesson', 'isCompleted', 'user'));
    }

    public function downloadPdf($id)
    {
        try {
            $lesson = Lesson::with('course')->findOrFail($id);
            Log::info('[INFO] ['.now()->toDateString()."]: Generando material táctico de descarga para lección '{$lesson->title}' (ID: {$id}).");

            $content = "=================================================================\n";
            $content .= "                 SIAM\n";
            $content .= "             MANUAL DE ESTUDIO TACTICO ACADEMICO\n";
            $content .= "=================================================================\n\n";
            $content .= 'CURSO: '.mb_strtoupper($lesson->course->title)."\n";
            $content .= 'CATEGORIA: '.mb_strtoupper($lesson->course->category)."\n";
            $content .= 'DIFICULTAD: '.mb_strtoupper($lesson->course->difficulty)."\n";
            $content .= 'LECCION: '.mb_strtoupper($lesson->title)."\n";
            $content .= 'FECHA: '.date('d/m/Y H:i:s')."\n";
            $content .= "-----------------------------------------------------------------\n\n";
            $content .= "CONTENIDO:\n\n";

            $clean = strip_tags(str_replace(
                ['<p>', '</p>', '<br>', '<br/>', '</li>', '</ul>'],
                ['', "\n", "\n", "\n", "\n", ''],
                $lesson->content
            ));
            $content .= trim($clean)."\n\n";
            $content .= "-----------------------------------------------------------------\n";
            $content .= "                FIN DEL MATERIAL DE ESTUDIO\n";
            $content .= "=================================================================\n";

            $filename = 'Manual_'.str_replace(
                [' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $lesson->title
            ).'.txt';

            return response($content, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString()."]: Error al generar material de estudio de lección (ID: {$id}). Detalle: ".$e->getMessage());

            return back()->with('error', 'No se pudo descargar el material táctico de estudio en este momento.');
        }
    }

    // =========================================================
    // CUESTIONARIOS
    // =========================================================

    public function startQuiz($lesson_id)
    {
        $user = Auth::user();
        $lesson = Lesson::with(['course', 'questions.options'])->findOrFail($lesson_id);

        $questions = $lesson->questions;

        if ($questions->isEmpty()) {
            $questions = collect([
                $this->fallbackQuestion($lesson, 1),
                $this->fallbackQuestion($lesson, 2),
                $this->fallbackQuestion($lesson, 3),
            ]);
        }

        $formattedQuestions = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'points' => $q->points,
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'option_text' => $o->option_text,
                    'is_correct' => $o->is_correct,
                ])->shuffle()->values()->toArray(),
            ];
        })->toArray();

        return view('student.lessons.quiz', compact('lesson', 'formattedQuestions', 'user'));
    }

    public function completeQuiz(Request $request, $lesson_id)
    {
        try {
            $user = Auth::user();
            $lesson = Lesson::findOrFail($lesson_id);

            $alreadyDone = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)->exists();

            $pts = max(0, (int) $request->input('points_earned', 50));
            $accuracy = max(0, min(100, (int) $request->input('accuracy_percent', 0)));

            if (! $alreadyDone) {
                LessonCompletion::create([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'xp_earned' => $pts,
                    'accuracy_percent' => $accuracy,
                ]);
                $user->increment('points', $pts);
                Log::info('[INFO] ['.now()->toDateString()."]: Estudiante {$user->email} completó la lección '{$lesson->title}' — XP: {$pts}, Precisión: {$accuracy}%.");

                return redirect()->route('student.courses.show', $lesson->course_id)
                    ->with('success', "Lección '{$lesson->title}' completada. +{$pts} XP · Precisión: {$accuracy}%.");
            }

            Log::info('[INFO] ['.now()->toDateString()."]: Estudiante {$user->email} repasó la lección '{$lesson->title}'.");

            return redirect()->route('student.courses.show', $lesson->course_id)
                ->with('info', "Lección '{$lesson->title}' repasada (el repaso no suma puntos).");

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString()."]: Fallo al completar el quiz para la lección {$lesson_id}. Detalle: ".$e->getMessage());

            return redirect()->route('student.dashboard')
                ->with('error', 'Ocurrió un error inesperado al procesar tu progreso de lección.');
        }
    }

    // =========================================================
    // PREGUNTAS DE APOYO (fallback)
    // =========================================================

    private function fallbackQuestion($lesson, $n)
    {
        $q = new Question;
        $q->id = 1000 + $n;
        $q->points = 15;

        $data = [
            1 => [
                'q' => "Cual es el objetivo principal de la leccion '{$lesson->title}'?",
                'opts' => [
                    [2001, 'Comprender los principios tecnicos y teoricos del manual', true],
                    [2002, 'Aumentar la velocidad de tiro en rafaga',                  false],
                    [2003, 'Realizar ejercicios de aptitud fisica sin supervision',    false],
                    [2004, 'Ignorar las medidas de seguridad del parque de armas',     false],
                ],
            ],
            2 => [
                'q' => "Que aspecto describe el protocolo reglamentario de la UNEFA en '{$lesson->title}'?",
                'opts' => [
                    [2011, 'La disciplina academica, el respeto a los mandos y la seguridad operacional', true],
                    [2012, 'El abandono de guardia sin relevo previo',   false],
                    [2013, 'El uso inapropiado del armamento de estudio', false],
                    [2014, 'La omision de informes de bitacora',          false],
                ],
            ],
            3 => [
                'q' => "El adiestramiento en '{$lesson->title}' requiere...",
                'opts' => [
                    [2021, 'Estudio analitico de la teoria, simulaciones de guardia y disciplina tactica', true],
                    [2022, 'Conexiones no cifradas en la nube del sistema',              false],
                    [2023, 'Uso del armamento sin autorizacion de un oficial superior',  false],
                    [2024, 'Evaluar a otros cadetes sin tener el rango de oficial',      false],
                ],
            ],
        ];

        $q->question_text = $data[$n]['q'];
        $q->options = collect(array_map(
            fn ($o) => (object) ['id' => $o[0], 'option_text' => $o[1], 'is_correct' => $o[2]],
            $data[$n]['opts']
        ));

        return $q;
    }
}
