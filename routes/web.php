<?php

// Cambio demostrativo para el video del Avance 3

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Ruta de cierre de sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de demostración del login para el pipeline CI/CD en Render
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas de inicio de sesión con Google
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

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
