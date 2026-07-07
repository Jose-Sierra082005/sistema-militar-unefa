<?php

// Cambio demostrativo para el video del Avance 3

use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GuardDutyController;
use App\Http\Controllers\MilitaryPersonnelController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\WeaponController;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        $studentCount = User::where('role', 'student')->count();
        $courseCount = Course::count();
        $lessonCount = Lesson::count();

        return view('dashboard', compact('studentCount', 'courseCount', 'lessonCount'));
    }

    return redirect()->route('student.dashboard');
})->middleware('auth')->name('dashboard');

// Ruta de cierre de sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de Registro de Oficiales
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rutas de Configuración del Doble Factor (2FA)
Route::get('/two-factor/setup', [AuthController::class, 'showTwoFactorSetup'])->name('two-factor.setup');
Route::post('/two-factor/activate', [AuthController::class, 'activateTwoFactor'])->name('two-factor.activate');

// Rutas de Verificación del Doble Factor (2FA)
Route::get('/two-factor/verify', [AuthController::class, 'showTwoFactorVerify'])->name('two-factor.verify');
Route::post('/two-factor/verify', [AuthController::class, 'verifyTwoFactor']);

// Rutas de Recuperación del Doble Factor (Google Authenticator perdido)
Route::get('/two-factor/recover', [AuthController::class, 'showTwoFactorRecoverForm'])->name('two-factor.recover');
Route::post('/two-factor/recover', [AuthController::class, 'sendTwoFactorRecoverOtp'])->name('two-factor.recover.send');
Route::get('/two-factor/recover/verify', [AuthController::class, 'showTwoFactorRecoverVerifyForm'])->name('two-factor.recover.verify');
Route::post('/two-factor/recover/verify', [AuthController::class, 'verifyTwoFactorRecoverOtp'])->name('two-factor.recover.verify.submit');
Route::get('/two-factor/recover/setup', [AuthController::class, 'showTwoFactorRecoverSetup'])->name('two-factor.recover.setup');
Route::post('/two-factor/recover/activate', [AuthController::class, 'activateTwoFactorRecover'])->name('two-factor.recover.activate');

// Rutas de Recuperación de Contraseña
Route::get('/password/forgot', [AuthController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'sendResetOtp']);
Route::get('/password/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('password.verify_otp');
Route::post('/password/verify-otp', [AuthController::class, 'verifyResetOtp']);
Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Rutas de demostración del login para el pipeline CI/CD en Render
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas de inicio de sesión con Google
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Rutas de Ajustes de Seguridad del Dashboard
Route::post('/security/update', [AuthController::class, 'updateSecurityProfile'])->name('security.update')->middleware('auth');
Route::post('/security/2fa-activate', [AuthController::class, 'activateTwoFactorFromDashboard'])->name('security.2fa-activate')->middleware('auth');

// Rutas de Administración Protegidas por Autenticación y Rol
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Personal Militar CRUD
    Route::get('/personnel', [MilitaryPersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/personnel/create', [MilitaryPersonnelController::class, 'create'])->name('personnel.create');
    Route::post('/personnel', [MilitaryPersonnelController::class, 'store'])->name('personnel.store');
    Route::get('/personnel/{id}/edit', [MilitaryPersonnelController::class, 'edit'])->name('personnel.edit');
    Route::put('/personnel/{id}', [MilitaryPersonnelController::class, 'update'])->name('personnel.update');
    Route::delete('/personnel/{id}', [MilitaryPersonnelController::class, 'destroy'])->name('personnel.destroy');

    // Parque de Armas CRUD (Manual de Consulta de Armamento)
    Route::get('/armory', [WeaponController::class, 'index'])->name('armory.index');
    Route::get('/armory/create', [WeaponController::class, 'create'])->name('armory.create');
    Route::post('/armory', [WeaponController::class, 'store'])->name('armory.store');
    Route::get('/armory/{id}/edit', [WeaponController::class, 'edit'])->name('armory.edit');
    Route::put('/armory/{id}', [WeaponController::class, 'update'])->name('armory.update');
    Route::delete('/armory/{id}', [WeaponController::class, 'destroy'])->name('armory.destroy');

    // Guardias y Roles (Manual de Turnos y Puestos)
    Route::get('/guards', [GuardDutyController::class, 'index'])->name('guards.index');
    Route::post('/guards', [GuardDutyController::class, 'store'])->name('guards.store');
    Route::delete('/guards/{id}', [GuardDutyController::class, 'destroy'])->name('guards.destroy');

    // Cursos y Temarios LMS
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Lecciones del LMS
    Route::get('/lessons/{id}/edit', [CourseController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/{id}', [CourseController::class, 'updateLesson'])->name('lessons.update');
    Route::post('/courses/{course_id}/lessons', [CourseController::class, 'storeLesson'])->name('lessons.store');
    Route::delete('/lessons/{id}', [CourseController::class, 'destroyLesson'])->name('lessons.destroy');

    // Cuestionarios del LMS
    Route::get('/questions/{id}/edit', [CourseController::class, 'editQuestion'])->name('questions.edit');
    Route::put('/questions/{id}', [CourseController::class, 'updateQuestion'])->name('questions.update');
    Route::post('/lessons/{lesson_id}/questions', [CourseController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/questions/{id}', [CourseController::class, 'destroyQuestion'])->name('questions.destroy');

    // Evaluaciones Académicas
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::delete('/evaluations/{id}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');

    // Perfil del Administrador
    Route::get('/profile', [AdminProfileController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile/update', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/2fa/activate', [AdminProfileController::class, 'activate2FA'])->name('profile.2fa.activate');
    Route::post('/profile/2fa/disable-send', [AdminProfileController::class, 'send2FADisableOtp'])->name('profile.2fa.disable.send');
    Route::post('/profile/2fa/disable-confirm', [AdminProfileController::class, 'disable2FA'])->name('profile.2fa.disable.confirm');
});

// Rutas de Estudiantes Protegidas por Autenticación y Rol
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', [StudentPortalController::class, 'index'])->name('index');
    Route::get('/dashboard', [StudentPortalController::class, 'index'])->name('dashboard');

    Route::get('/courses/{id}', [StudentPortalController::class, 'showCourse'])->name('courses.show');
    Route::get('/lessons/{id}', [StudentPortalController::class, 'showLesson'])->name('lessons.show');
    Route::get('/lessons/{id}/download', [StudentPortalController::class, 'downloadPdf'])->name('lessons.download');
    Route::get('/lessons/{id}/quiz', [StudentPortalController::class, 'startQuiz'])->name('lessons.quiz');
    Route::post('/lessons/{id}/quiz', [StudentPortalController::class, 'completeQuiz'])->name('lessons.complete_quiz');

    // Módulo de Configuración de Perfil
    Route::get('/profile', [StudentPortalController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [StudentPortalController::class, 'updatePassword'])->name('profile.password');

    // Desactivacion del Doble Factor (2FA) con verificacion OTP por correo
    Route::post('/profile/2fa/disable-send', [StudentPortalController::class, 'send2FADisableOtp'])->name('profile.2fa.disable.send');
    Route::post('/profile/2fa/disable-confirm', [StudentPortalController::class, 'disable2FA'])->name('profile.2fa.disable.confirm');
});
