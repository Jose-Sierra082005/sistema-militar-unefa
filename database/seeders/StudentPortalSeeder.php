<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Option;

class StudentPortalSeeder extends Seeder
{
    /**
     * Crea usuarios de prueba (admin y estudiante) con cursos, lecciones
     * y cuestionarios interactivos de demostración.
     */
    public function run(): void
    {
        // ── Usuarios de prueba ───────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@unefa.edu.ve'],
            [
                'name'     => 'Comandante Sierra',
                'password' => Hash::make('Admin123!'),
                'role'     => 'admin',
                'points'   => 0,
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'estudiante@unefa.edu.ve'],
            [
                'name'     => 'Cadete José Rodríguez',
                'password' => Hash::make('Student123!'),
                'role'     => 'student',
                'points'   => 0,
            ]
        );

        $this->command->info('✔ Usuarios creados: admin@unefa.edu.ve / Student@unefa.edu.ve');

        // ── Curso 1: Tácticas de Guardia ─────────────────────────────
        $course1 = Course::firstOrCreate(
            ['title' => 'Tácticas de Guardia y Centinela'],
            [
                'description' => 'Adiestramiento en procedimientos de guardia, consignas, relevos y protocolos de seguridad para centinelas de la UNEFA.',
                'category'    => 'Táctica Militar',
                'difficulty'  => 'Básico',
            ]
        );

        $lesson1_1 = Lesson::firstOrCreate(
            ['title' => 'La Consigna General del Centinela', 'course_id' => $course1->id],
            [
                'content' => "<p>La consigna general del centinela es el conjunto de órdenes fijas que todo soldado de guardia debe memorizar y aplicar sin excepción. Estas reglas son de carácter universal y aplican en cualquier puesto de guardia.</p>\n<h3>Reglas Fundamentales</h3>\n<ul>\n<li>Hacerse cargo del puesto y de todo lo que en él se encuentra.</li>\n<li>Caminar en mi puesto de una manera militar, manteniendo siempre una actitud de alerta.</li>\n<li>Informar al cabo de guardia de todas las violaciones de las órdenes que estoy obligado a hacer cumplir.</li>\n<li>Dejar pasar solamente a los oficiales de guardia, oficiales de servicio y personas que tengan la contraseña correcta.</li>\n<li>Abandonar mi puesto sólo cuando sea relevado.</li>\n<li>Llamar al cabo de guardia en cualquier caso de duda.</li>\n</ul>",
                'order'   => 1,
            ]
        );

        $lesson1_2 = Lesson::firstOrCreate(
            ['title' => 'El Relevo de Guardia', 'course_id' => $course1->id],
            [
                'content' => "<p>El relevo de guardia es el procedimiento formal mediante el cual un centinela es sustituido por otro en su puesto de vigilancia. Este procedimiento debe ejecutarse de forma reglamentaria.</p>\n<h3>Procedimiento Estándar</h3>\n<ul>\n<li>El centinela entrante se aproxima al puesto en posición de firmes.</li>\n<li>El cabo de guardia presenta al centinela saliente las novedades del puesto.</li>\n<li>El centinela entrante revisa el armamento e instalaciones.</li>\n<li>Se entrega el puesto con la frase: \"Hago entrega del puesto\".</li>\n<li>El relevo confirma: \"Recibo el puesto\".</li>\n</ul>",
                'order'   => 2,
            ]
        );

        // ── Curso 2: Manual de Armamento ─────────────────────────────
        $course2 = Course::firstOrCreate(
            ['title' => 'Manual de Armamento Reglamentario'],
            [
                'description' => 'Estudio técnico del armamento reglamentario de las Fuerzas Armadas Venezolanas: características, mantenimiento y normas de seguridad.',
                'category'    => 'Armamento',
                'difficulty'  => 'Intermedio',
            ]
        );

        $lesson2_1 = Lesson::firstOrCreate(
            ['title' => 'Fusil de Asalto AK-103: Características y Seguridad', 'course_id' => $course2->id],
            [
                'content' => "<p>El AK-103 es un fusil de asalto de calibre 7.62x39mm ampliamente utilizado en las Fuerzas Armadas Nacionales Bolivarianas (FANB). Es la versión modernizada del legendario AKM.</p>\n<h3>Especificaciones Técnicas</h3>\n<ul>\n<li><strong>Calibre:</strong> 7.62 x 39 mm</li>\n<li><strong>Longitud:</strong> 943 mm (con culata extendida)</li>\n<li><strong>Peso sin cargador:</strong> 3.6 kg</li>\n<li><strong>Cadencia:</strong> 600 disparos/min (ráfaga)</li>\n<li><strong>Capacidad del cargador:</strong> 30 cartuchos</li>\n</ul>\n<h3>Medidas de Seguridad Fundamentales</h3>\n<ul>\n<li>Tratar siempre el arma como si estuviera cargada.</li>\n<li>No apuntar jamás a algo que no se desee destruir.</li>\n<li>Mantener el dedo fuera del guardamonte hasta estar listo para disparar.</li>\n<li>Identificar el blanco y lo que hay más allá de él.</li>\n</ul>",
                'order'   => 1,
            ]
        );

        $this->command->info('✔ Cursos y lecciones creados.');

        // ── Cuestionario: Lección 1.1 ────────────────────────────────
        $this->seedQuestionsForLesson($lesson1_1, [
            [
                'text'    => '¿Cuántas reglas conforman la consigna general del centinela?',
                'points'  => 15,
                'options' => [
                    ['text' => '11 reglas reglamentarias', 'correct' => true],
                    ['text' => '5 reglas básicas', 'correct' => false],
                    ['text' => '7 reglas esenciales', 'correct' => false],
                    ['text' => '15 órdenes especiales', 'correct' => false],
                ],
            ],
            [
                'text'    => '¿Cuándo puede un centinela abandonar legítimamente su puesto de guardia?',
                'points'  => 15,
                'options' => [
                    ['text' => 'Únicamente al ser relevado reglamentariamente', 'correct' => true],
                    ['text' => 'Al finalizar su turno de 6 horas', 'correct' => false],
                    ['text' => 'Cuando lo autorice cualquier oficial presente', 'correct' => false],
                    ['text' => 'Si recibe una orden escrita de cualquier superior', 'correct' => false],
                ],
            ],
            [
                'text'    => '¿A quién debe notificar el centinela sobre violaciones a las órdenes?',
                'points'  => 10,
                'options' => [
                    ['text' => 'Al cabo de guardia', 'correct' => true],
                    ['text' => 'Directamente al oficial de mayor rango disponible', 'correct' => false],
                    ['text' => 'Al siguiente centinela del turno', 'correct' => false],
                    ['text' => 'Al director del pelotón de seguridad', 'correct' => false],
                ],
            ],
        ]);

        // ── Cuestionario: Lección 1.2 ────────────────────────────────
        $this->seedQuestionsForLesson($lesson1_2, [
            [
                'text'    => '¿Qué frase utiliza el centinela saliente para ceder el puesto?',
                'points'  => 15,
                'options' => [
                    ['text' => '"Hago entrega del puesto"', 'correct' => true],
                    ['text' => '"Puesto desocupado, proceda"', 'correct' => false],
                    ['text' => '"Turno finalizado, es suyo"', 'correct' => false],
                    ['text' => '"Guardia terminada, relevado"', 'correct' => false],
                ],
            ],
            [
                'text'    => '¿Qué debe verificar el centinela entrante durante el relevo?',
                'points'  => 20,
                'options' => [
                    ['text' => 'El armamento e instalaciones del puesto', 'correct' => true],
                    ['text' => 'Solo el libro de novedades del turno', 'correct' => false],
                    ['text' => 'La identidad del cabo de guardia', 'correct' => false],
                    ['text' => 'El estado del equipo de comunicaciones únicamente', 'correct' => false],
                ],
            ],
        ]);

        // ── Cuestionario: Lección 2.1 ────────────────────────────────
        $this->seedQuestionsForLesson($lesson2_1, [
            [
                'text'    => '¿Cuál es el calibre reglamentario del fusil AK-103?',
                'points'  => 15,
                'options' => [
                    ['text' => '7.62 x 39 mm', 'correct' => true],
                    ['text' => '5.56 x 45 mm NATO', 'correct' => false],
                    ['text' => '9 x 19 mm Parabellum', 'correct' => false],
                    ['text' => '7.62 x 51 mm NATO', 'correct' => false],
                ],
            ],
            [
                'text'    => '¿Cuál es la capacidad estándar del cargador del AK-103?',
                'points'  => 10,
                'options' => [
                    ['text' => '30 cartuchos', 'correct' => true],
                    ['text' => '20 cartuchos', 'correct' => false],
                    ['text' => '40 cartuchos', 'correct' => false],
                    ['text' => '25 cartuchos', 'correct' => false],
                ],
            ],
            [
                'text'    => 'Según las normas de seguridad, ¿cuándo se coloca el dedo en el guardamonte?',
                'points'  => 20,
                'options' => [
                    ['text' => 'Solo cuando se está listo para disparar', 'correct' => true],
                    ['text' => 'Siempre que el arma esté desasegurada', 'correct' => false],
                    ['text' => 'Al momento de entrar a la zona de tiro', 'correct' => false],
                    ['text' => 'Cuando el blanco ha sido identificado', 'correct' => false],
                ],
            ],
            [
                'text'    => '¿Qué peso aproximado tiene el AK-103 sin cargador?',
                'points'  => 10,
                'options' => [
                    ['text' => '3.6 kg', 'correct' => true],
                    ['text' => '4.2 kg', 'correct' => false],
                    ['text' => '2.9 kg', 'correct' => false],
                    ['text' => '5.1 kg', 'correct' => false],
                ],
            ],
        ]);

        $this->command->info('✔ Cuestionarios sembrados con éxito.');
        $this->command->line('');
        $this->command->line('  ┌─────────────────────────────────────────────┐');
        $this->command->line('  │  Credenciales de Acceso al Sistema           │');
        $this->command->line('  │  Admin:     admin@unefa.edu.ve / Admin123!   │');
        $this->command->line('  │  Estudiante: estudiante@unefa.edu.ve          │');
        $this->command->line('  │              Student123!                      │');
        $this->command->line('  └─────────────────────────────────────────────┘');
    }

    /**
     * Siembra preguntas con opciones para una lección,
     * evitando duplicados si ya existen.
     */
    private function seedQuestionsForLesson(Lesson $lesson, array $questionsData): void
    {
        foreach ($questionsData as $qData) {
            // Skip if this question already exists for this lesson
            $exists = Question::where('lesson_id', $lesson->id)
                ->where('question_text', $qData['text'])
                ->exists();

            if ($exists) continue;

            $question = Question::create([
                'lesson_id'     => $lesson->id,
                'question_text' => $qData['text'],
                'points'        => $qData['points'],
            ]);

            foreach ($qData['options'] as $optData) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $optData['text'],
                    'is_correct'  => $optData['correct'],
                ]);
            }
        }
    }
}
