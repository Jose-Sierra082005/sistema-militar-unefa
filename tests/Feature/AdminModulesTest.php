<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\MilitaryPersonnel;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: Create Admin User ──────────────────────────────────────
    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Comandante Sierra',
            'email' => 'sierra@unefa.edu.ve',
            'password' => bcrypt('Secret123!'),
            'role' => 'admin',
            'points' => 0,
        ]);
    }

    // ── Helper: Create Student User ────────────────────────────────────
    private function createStudent(): User
    {
        return User::create([
            'name' => 'Cadete Rodríguez',
            'email' => 'rodri@unefa.edu.ve',
            'password' => bcrypt('Student123!'),
            'role' => 'student',
            'points' => 0,
        ]);
    }

    // ── Helper: Create Military Personnel ─────────────────────────────
    private function createMilitaryPersonnel(): MilitaryPersonnel
    {
        return MilitaryPersonnel::create([
            'name' => 'Teniente José Sierra',
            'cedula' => '31149881',
            'rank' => 'Teniente',
            'role' => 'Oficial de Comando',
            'status' => 'Activo',
            'phone' => '0412-1234567',
            'email' => 'jose@unefa.edu.ve',
        ]);
    }

    // ── Helper: Create a full Course with Lesson + Quiz ───────────────
    private function createCourseWithLesson(): array
    {
        $course = Course::create([
            'title' => 'Táctica del Centinela',
            'description' => 'Procedimientos de guardia',
            'category' => 'Táctica',
            'difficulty' => 'Básico',
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Consigna General',
            'content' => 'El centinela debe...',
            'order' => 1,
        ]);

        $question = Question::create([
            'lesson_id' => $lesson->id,
            'question_text' => '¿Cuándo puede el centinela abandonar su puesto?',
            'points' => 15,
        ]);

        Option::create(['question_id' => $question->id, 'option_text' => 'Al ser relevado', 'is_correct' => true]);
        Option::create(['question_id' => $question->id, 'option_text' => 'A las 6 horas',  'is_correct' => false]);
        Option::create(['question_id' => $question->id, 'option_text' => 'Con autorización verbal', 'is_correct' => false]);
        Option::create(['question_id' => $question->id, 'option_text' => 'Nunca abandona', 'is_correct' => false]);

        return compact('course', 'lesson', 'question');
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: ACCESO NO AUTENTICADO
    // ══════════════════════════════════════════════════════════════════

    public function test_unauthenticated_user_cannot_access_admin_modules(): void
    {
        $response = $this->get(route('admin.armory.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.guards.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.evaluations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_access_student_portal(): void
    {
        $response = $this->get(route('student.index'));
        $response->assertRedirect(route('login'));
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: CONTROL DE ACCESO POR ROLES
    // ══════════════════════════════════════════════════════════════════

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('admin.courses.index'));
        // RoleMiddleware returns 403 Forbidden for wrong-role access
        $response->assertStatus(403);

        $response = $this->actingAs($student)->get(route('admin.armory.index'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_student_portal(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('student.index'));
        // RoleMiddleware returns 403 Forbidden for wrong-role access
        $response->assertStatus(403);
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: MÓDULO ADMIN - ARMERÍA
    // ══════════════════════════════════════════════════════════════════

    public function test_admin_can_manage_armory(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        // Index
        $response = $this->actingAs($user)->get(route('admin.armory.index'));
        $response->assertStatus(200);

        // Create form
        $response = $this->actingAs($user)->get(route('admin.armory.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($user)->post(route('admin.armory.store'), [
            'serial' => 'UNEFA-AK103-001',
            'type' => 'Fusil de Asalto',
            'model' => 'AK-103',
            'condition' => 'Excelente',
            'status' => 'Asignado',
            'assigned_to' => $personnel->id,
        ]);

        $response->assertRedirect(route('admin.armory.index'));
        $this->assertDatabaseHas('weapons', [
            'serial' => 'UNEFA-AK103-001',
            'assigned_to' => $personnel->id,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: MÓDULO ADMIN - GUARDIAS
    // ══════════════════════════════════════════════════════════════════

    public function test_admin_can_manage_guard_shifts(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        // Index
        $response = $this->actingAs($user)->get(route('admin.guards.index'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($user)->post(route('admin.guards.store'), [
            'personnel_id' => $personnel->id,
            'post' => 'Garita Principal (Acceso)',
            'shift_time' => 'Turno Alpha: 00:00 - 06:00',
            'date' => '2026-06-14',
            'status' => 'Programado',
        ]);

        $response->assertRedirect(route('admin.guards.index'));
        $this->assertDatabaseHas('guard_shifts', [
            'personnel_id' => $personnel->id,
            'post' => 'Garita Principal (Acceso)',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: MÓDULO ADMIN - EVALUACIONES
    // ══════════════════════════════════════════════════════════════════

    public function test_admin_can_manage_evaluations(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        $course = Course::create([
            'title' => 'Tiro de Precisión (AK-103)',
            'description' => 'Curso de tiro con fusil AK-103',
            'category' => 'Armamento',
            'difficulty' => 'Básico',
        ]);

        // Index
        $response = $this->actingAs($user)->get(route('admin.evaluations.index'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($user)->post(route('admin.evaluations.store'), [
            'personnel_id' => $personnel->id,
            'course_id' => $course->id,
            'score' => 19,
            'evaluator' => 'Cnel. José Sierra',
            'date' => '2026-06-14',
            'comments' => 'Agrupación excelente.',
        ]);

        $response->assertRedirect(route('admin.evaluations.index'));
        $this->assertDatabaseHas('evaluations', [
            'personnel_id' => $personnel->id,
            'course_id' => $course->id,
            'score' => 19,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: MÓDULO ADMIN - CURSOS Y LECCIONES
    // ══════════════════════════════════════════════════════════════════

    public function test_admin_can_manage_courses_and_lessons(): void
    {
        $user = $this->createAdmin();

        // Index
        $response = $this->actingAs($user)->get(route('admin.courses.index'));
        $response->assertStatus(200);

        // Store course
        $response = $this->actingAs($user)->post(route('admin.courses.store'), [
            'title' => 'Táctica del Centinela',
            'description' => 'Procedimientos para guardias y patrullas',
            'category' => 'Táctica',
            'difficulty' => 'Intermedio',
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', [
            'title' => 'Táctica del Centinela',
            'category' => 'Táctica',
        ]);

        $course = Course::where('title', 'Táctica del Centinela')->first();

        // Show
        $response = $this->actingAs($user)->get(route('admin.courses.show', $course->id));
        $response->assertStatus(200);

        // Store lesson
        $response = $this->actingAs($user)->post(route('admin.lessons.store', $course->id), [
            'title' => 'Consigna Particular de Garita',
            'content' => 'Texto detallado del manual de procedimientos.',
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.courses.show', $course->id));
        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'Consigna Particular de Garita',
        ]);

        $lesson = Lesson::where('title', 'Consigna Particular de Garita')->first();

        // Store question via CourseController
        $response = $this->actingAs($user)->post(route('admin.questions.store', $lesson->id), [
            'question_text' => '¿Qué es la consigna?',
            'points' => 15,
            'options' => ['Regla general del centinela', 'Un arma', 'Un puesto', 'Un relevo'],
            'correct_option' => 0,
        ]);

        $response->assertRedirect(route('admin.courses.show', $course->id));
        $this->assertDatabaseHas('questions', [
            'lesson_id' => $lesson->id,
            'question_text' => '¿Qué es la consigna?',
        ]);

        $question = Question::where('lesson_id', $lesson->id)->first();

        // Update lesson
        $response = $this->actingAs($user)->put(route('admin.lessons.update', $lesson->id), [
            'title' => 'Consigna Actualizada',
            'content' => '<p>Contenido <strong>HTML</strong> actualizado.</p><script>alert(1)</script>',
            'order' => 2,
        ]);
        $response->assertRedirect(route('admin.courses.show', $course->id));
        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'Consigna Actualizada',
            'order' => 2,
        ]);
        $lesson->refresh();
        $this->assertStringContainsString('<strong>HTML</strong>', $lesson->content);
        $this->assertStringNotContainsString('<script>', $lesson->content);

        // Edit views load the visual editor (Quill)
        $response = $this->actingAs($user)->get(route('admin.lessons.edit', $lesson->id));
        $response->assertStatus(200);
        $response->assertSee('quill.min.js', false);
        $response->assertSee('No necesita escribir código HTML', false);

        // Update question
        $response = $this->actingAs($user)->put(route('admin.questions.update', $question->id), [
            'question_text' => '¿Cuál es la consigna reglamentaria?',
            'points' => 20,
            'options' => ['Norma del centinela', 'Un arma', 'Un puesto', 'Un relevo'],
            'correct_option' => 0,
        ]);
        $response->assertRedirect(route('admin.courses.show', $course->id));
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question_text' => '¿Cuál es la consigna reglamentaria?',
            'points' => 20,
        ]);

        // Delete lesson
        $response = $this->actingAs($user)->delete(route('admin.lessons.destroy', $lesson->id));
        $response->assertRedirect(route('admin.courses.show', $course->id));
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);

        // Delete course
        $response = $this->actingAs($user)->delete(route('admin.courses.destroy', $course->id));
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_admin_can_login_with_seeded_credentials(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@unefa.edu.ve'],
            [
                'name' => 'Comandante Sierra',
                'password' => 'Admin123!',
                'role' => 'admin',
                'two_factor_enabled' => false,
            ]
        );

        $response = $this->post(route('login'), [
            'email' => 'admin@unefa.edu.ve',
            'password' => 'Admin123!',
            'admin_portal' => '1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs(User::where('email', 'admin@unefa.edu.ve')->first());
    }

    public function test_admin_login_portal_rejects_students(): void
    {
        $student = $this->createStudent();

        $response = $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'Student123!',
            'admin_portal' => '1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Portal Administrador');
    }

    // ══════════════════════════════════════════════════════════════════
    // TESTS: PORTAL DEL ESTUDIANTE
    // ══════════════════════════════════════════════════════════════════

    public function test_student_can_view_portal_dashboard(): void
    {
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('student.index'));
        $response->assertStatus(200);
    }

    public function test_student_can_view_course_map(): void
    {
        ['course' => $course] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('student.courses.show', $course->id));
        $response->assertStatus(200);
    }

    public function test_student_can_access_first_lesson_without_prerequisite(): void
    {
        ['course' => $course, 'lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('student.lessons.show', $lesson->id));
        $response->assertStatus(200);
    }

    public function test_student_cannot_access_locked_lesson(): void
    {
        ['course' => $course, 'lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        // Create a second lesson that requires first to be completed
        $lesson2 = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Segunda Lección',
            'content' => 'Contenido de la segunda lección.',
            'order' => 2,
        ]);

        // Try to access lesson 2 without completing lesson 1 → should redirect
        $response = $this->actingAs($student)->get(route('student.lessons.show', $lesson2->id));
        $response->assertRedirect(route('student.courses.show', $course->id));
    }

    public function test_student_can_start_quiz(): void
    {
        ['lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('student.lessons.quiz', $lesson->id));
        $response->assertStatus(200);
    }

    public function test_student_can_complete_quiz_and_earn_xp(): void
    {
        ['lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        $this->assertEquals(0, $student->fresh()->points);

        $response = $this->actingAs($student)->post(route('student.lessons.complete_quiz', $lesson->id), [
            'points_earned' => 45,
        ]);

        $response->assertRedirect(route('student.courses.show', $lesson->course_id));

        // XP should be added
        $this->assertEquals(45, $student->fresh()->points);

        // Completion record should exist
        $this->assertDatabaseHas('lesson_completions', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_replaying_quiz_does_not_award_duplicate_xp(): void
    {
        ['lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        // Complete once
        $this->actingAs($student)->post(route('student.lessons.complete_quiz', $lesson->id), [
            'points_earned' => 45,
        ]);

        // Complete again (replay)
        $this->actingAs($student)->post(route('student.lessons.complete_quiz', $lesson->id), [
            'points_earned' => 45,
        ]);

        // XP should NOT be doubled
        $this->assertEquals(45, $student->fresh()->points);
    }

    public function test_student_can_download_lesson_material(): void
    {
        ['lesson' => $lesson] = $this->createCourseWithLesson();
        $student = $this->createStudent();

        $response = $this->actingAs($student)->get(route('student.lessons.download', $lesson->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }
}
