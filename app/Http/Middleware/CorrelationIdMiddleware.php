<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * CorrelationIdMiddleware — Trazabilidad Avanzada (SIAM - Avance #6)
 *
 * Asigna un identificador único (UUID v4) a cada petición HTTP entrante.
 * Este Correlation ID se:
 *  - Almacena en el contenedor de servicios de Laravel como singleton de request.
 *  - Inyecta en el contexto global de logs (Log::withContext) para que aparezca
 *    automáticamente en TODOS los mensajes de log de esa petición.
 *  - Añade a la respuesta HTTP como header 'X-Correlation-ID' para trazabilidad
 *    desde el cliente o herramientas de monitoreo.
 *
 * Si el sistema falla, el usuario verá: "Reporte el código: [UUID]" y el equipo
 * de soporte puede usar ese UUID para encontrar el log exacto del incidente.
 */
class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generar un UUID v4 único para esta petición
        $correlationId = (string) Str::uuid();

        // Registrar el Correlation ID en el contenedor de la aplicación
        // para que esté disponible desde cualquier capa del sistema
        app()->instance('correlation_id', $correlationId);

        // Inyectar el Correlation ID en el contexto global de Monolog
        // Esto garantiza que TODOS los logs de esta petición incluyan el UUID
        Log::withContext([
            'correlation_id' => $correlationId,
            'request_method' => $request->method(),
            'request_url'    => $request->fullUrl(),
            'user_ip'        => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        // Procesar la petición normalmente
        $response = $next($request);

        // Añadir el Correlation ID al header de la respuesta HTTP
        // Permite rastrear la petición desde el frontend o APIs externas
        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}
