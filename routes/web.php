<?php

// Cambio demostrativo para el video del Avance 3

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MilitaryPersonnelController;
use App\Http\Controllers\WeaponController;
use App\Http\Controllers\GuardDutyController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return view('dashboard');
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

// Rutas de Recuperación de Contraseña
Route::get('/password/forgot', [AuthController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'sendResetOtp']);
Route::get('/password/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('password.verify_otp');
Route::post('/password/verify-otp', [AuthController::class, 'verifyResetOtp']);
Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Rutas de demostración del login para el pipeline CI/CD en Render
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas de inicio de sesión con Google
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Rutas de Ajustes de Seguridad del Dashboard
Route::post('/security/update', [AuthController::class, 'updateSecurityProfile'])->name('security.update')->middleware('auth');
Route::post('/security/2fa-activate', [AuthController::class, 'activateTwoFactorFromDashboard'])->name('security.2fa-activate')->middleware('auth');

// Rutas de Administración Protegidas por Autenticación
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
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
    Route::post('/courses/{course_id}/lessons', [CourseController::class, 'storeLesson'])->name('lessons.store');
    Route::delete('/lessons/{id}', [CourseController::class, 'destroyLesson'])->name('lessons.destroy');

    // Evaluaciones Académicas
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::delete('/evaluations/{id}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');
});

// Ruta de diagnóstico de base de datos
Route::get('/db-check', function () {
    try {
        $connection = \Illuminate\Support\Facades\DB::connection();
        $dbName = $connection->getDatabaseName();
        $driver = $connection->getDriverName();
        $connection->getPdo(); // Forzar conexión
        return response()->json([
            'status' => 'success',
            'message' => "Conexión exitosa a la base de datos!",
            'driver' => $driver,
            'database' => $dbName,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al conectar a la base de datos',
            'error_details' => $e->getMessage(),
            'code' => $e->getCode(),
        ], 500);
    }
});
