<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send OTP reset code to user via Resend API
     */
    public static function sendOtpEmail(string $email, string $name, string $otp): bool
    {
        // Cargar el API Key a través del archivo de configuración para ser compatible con config:cache en producción.
        $apiKey = config('services.resend.key', 're_cHiqWxwE_Fj7oFFV9vy8r9tyYBNzGhxDA');

        $htmlContent = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    background-color: #07090e;
                    color: #f0f4f8;
                    font-family: Arial, sans-serif;
                    padding: 40px 20px;
                    margin: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: rgba(13, 17, 27, 0.85);
                    border: 1px solid rgba(46, 74, 53, 0.4);
                    border-radius: 12px;
                    padding: 40px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                }
                .header {
                    text-align: center;
                    border-bottom: 1px solid rgba(46, 74, 53, 0.2);
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .title {
                    font-size: 24px;
                    font-weight: bold;
                    color: #d4af37;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }
                .subtitle {
                    font-size: 14px;
                    color: #9aa5b1;
                    margin-top: 5px;
                }
                .content {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #f0f4f8;
                }
                .otp-box {
                    text-align: center;
                    font-family: monospace;
                    background-color: #090c13;
                    border: 1px dashed rgba(201, 160, 84, 0.5);
                    padding: 20px;
                    border-radius: 8px;
                    color: #f3cd4a;
                    font-size: 32px;
                    font-weight: bold;
                    letter-spacing: 10px;
                    margin: 30px 0;
                }
                .warning-text {
                    font-size: 13px;
                    color: #ff4d4d;
                    background: rgba(255, 77, 77, 0.1);
                    border: 1px solid rgba(255, 77, 77, 0.3);
                    border-radius: 6px;
                    padding: 12px;
                    margin-top: 25px;
                }
                .footer {
                    margin-top: 40px;
                    border-top: 1px solid rgba(46, 74, 53, 0.2);
                    padding-top: 20px;
                    font-size: 12px;
                    color: #9aa5b1;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="title">SIAM</div>
                    <div class="subtitle">CONFIDENCIALIDAD Y SEGURIDAD NACIONAL</div>
                </div>
                <div class="content">
                    <p>Estimado Oficial <strong>'.htmlspecialchars($name).'</strong>,</p>
                    <p>Se ha iniciado una solicitud de recuperación de contraseña de acceso para su cuenta registrada en la plataforma SIAM.</p>
                    <p>Para autorizar esta acción y establecer una nueva clave, ingrese el siguiente código único de seguridad en la pantalla de verificación del sistema:</p>
                    
                    <div class="otp-box">'.htmlspecialchars($otp).'</div>
                    
                    <p>Este código OTP tiene una <strong>validez de 15 minutos</strong> y puede ser utilizado una única vez. Si usted no ha iniciado este proceso, ignore este correo inmediatamente y reporte la novedad al oficial de seguridad informática.</p>
                    
                    <div class="warning-text">
                        <strong>ADVERTENCIA DE SEGURIDAD:</strong> Nunca comparta este código con terceros ni proporcione sus credenciales a través de canales no autorizados.
                    </div>
                </div>
                <div class="footer">
                    <p>&copy; 2026 SIAM. Todos los derechos reservados.</p>
                    <p>Soporte de Seguridad Informática - UNEFA Falcón</p>
                </div>
            </div>
        </body>
        </html>';

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.resend.com/emails', [
                    'from' => 'SIAM <onboarding@resend.dev>',
                    'to' => [$email],
                    'subject' => '🛡️ Código de Seguridad - Recuperación de Contraseña',
                    'html' => $htmlContent,
                ]);

            if ($response->successful()) {
                Log::info("OTP reset email sent successfully to {$email}");

                return true;
            }

            // ─── Manejo de restricción Sandbox de Resend (HTTP 403) ──────────────
            // El plan gratuito de Resend sin dominio verificado solo permite enviar
            // correos a la dirección del dueño de la cuenta (RESEND_AUTHORIZED_EMAIL).
            // Si el destinatario original es rechazado, reintentamos automáticamente
            // al buzón autorizado para que el código llegue de forma real al correo.
            if ($response->status() === 403) {
                $authorizedEmail = config('services.resend.authorized_email',
                    env('RESEND_AUTHORIZED_EMAIL', 'jose.unefa.asignaciones@gmail.com'));

                Log::warning("Resend sandbox restriction: retrying OTP delivery to authorized mailbox [{$authorizedEmail}] on behalf of [{$email}]", [
                    'action'             => 'email.sandbox_retry',
                    'original_recipient' => $email,
                    'retry_recipient'    => $authorizedEmail,
                    'otp'                => $otp,
                ]);

                // Reenviar al buzón autorizado indicando para quién es el código
                $retryResponse = Http::withToken($apiKey)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post('https://api.resend.com/emails', [
                        'from'    => 'SIAM <onboarding@resend.dev>',
                        'to'      => [$authorizedEmail],
                        'subject' => "🛡️ OTP para [{$email}] — Recuperación de Contraseña",
                        'html'    => $htmlContent,
                    ]);

                if ($retryResponse->successful()) {
                    Log::info("OTP sandbox retry delivered to [{$authorizedEmail}] on behalf of [{$email}]");
                    // Notificar al usuario dónde revisar el correo
                    session()->flash('warning',
                        "Modo prueba activo: El código OTP fue enviado al buzón de administración del sistema ({$authorizedEmail}). Revise esa bandeja de entrada para obtener su código.");

                    return true;
                }
            }

            // ─── FALLBACK FINAL — Log del OTP si todo falla ────────────────────
            Log::error("Failed to send OTP email via Resend to {$email}. Error: ".$response->body());
            Log::warning("CONTINGENCIA (Correo Caído): OTP para {$email} en logs: [{$otp}]", [
                'action' => 'email.fallback_otp',
                'email'  => $email,
                'otp'    => $otp,
            ]);

            session()->flash('warning',
                'El servicio de correo se encuentra en modo contingencia. El código de verificación ha sido registrado en los logs del servidor.');

            return true;

        } catch (\Exception $e) {
            Log::error('Exception occurred while sending OTP email: '.$e->getMessage());
            Log::warning("CONTINGENCIA (Excepción): OTP para {$email} en logs: [{$otp}]", [
                'action' => 'email.fallback_otp_exception',
                'email'  => $email,
                'otp'    => $otp,
            ]);

            session()->flash('warning',
                'El servicio de correo se encuentra en modo contingencia. El código de verificación ha sido registrado en los logs del servidor.');

            return true;
        }
    }
}

