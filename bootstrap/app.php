<?php

use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // ─── AVANCE #6: Trazabilidad Avanzada ────────────────────────────
        // Registrar CorrelationIdMiddleware como middleware GLOBAL de web.
        // Se ejecuta en CADA petición HTTP antes que cualquier controlador,
        // generando un UUID único para trazabilidad de logs e incidentes.
        $middleware->web(prepend: [
            CorrelationIdMiddleware::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ─── AVANCE #6: BlueTeam — Fail-Secure (Manejo Seguro de Errores) ──
        // Interceptar TODAS las excepciones no manejadas para:
        //  1. Registrar el error en logs JSON con el Correlation ID
        //  2. NUNCA exponer stack traces, rutas de archivos o info interna
        //  3. Mostrar al usuario un mensaje seguro con el UUID del incidente
        $exceptions->render(function (\Throwable $e, Request $request) {
            // ─── Entorno de testing: no interceptar excepciones ────────────
            // En tests, dejamos que Laravel propague los errores normalmente
            // para que los assertions de los tests funcionen correctamente.
            if (app()->environment('testing')) {
                return null;
            }

            // Recuperar el Correlation ID asignado por el middleware
            $correlationId = app()->has('correlation_id')
                ? app('correlation_id')
                : 'N/A';

            // Registrar el error completo en logs (solo el sistema lo ve)
            \Illuminate\Support\Facades\Log::error('Excepcion no manejada capturada por el handler global.', [
                'correlation_id' => $correlationId,
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id() ?? 'unauthenticated',
            ]);

            // ─── Respuesta para peticiones API: JSON seguro ────────────────
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Ha ocurrido un error interno.',
                    'code' => $correlationId,
                    'message' => 'Reporte este código al equipo de soporte.',
                ], 500);
            }

            // ─── Respuesta para peticiones Web: vista segura ───────────────
            // Solo para errores 500+ (no sobreescribir 404, 403, etc.)
            if (! ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException)) {
                return response()->view('errors.500', [
                    'correlationId' => $correlationId,
                ], 500);
            }

            // Para HttpExceptions (403, 404, etc.), dejar que Laravel
            // use las vistas errors/403.blade.php, errors/404.blade.php, etc.
            return null;
        });

        // Renderizar JSON solo cuando la petición espera JSON en rutas API
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
