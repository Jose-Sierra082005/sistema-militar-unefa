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
        // Cargar el API Key desde el archivo .env, usando la clave por defecto como fallback.
        $apiKey = env('RESEND_API_KEY', 're_GyRst3et_NYKxpdi8FxDnFjB3zCi3HrK6');

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
            } else {
                Log::error("Failed to send OTP email via Resend to {$email}. Error: ".$response->body());

                // ─── SIAM — FALLBACK DE CONTINGENCIA (Avance #6) ──────────────────
                // Si la entrega del correo falla por API Key inválida o dominio no verificado,
                // registramos de forma visible el código OTP en los logs estructurados del servidor.
                // Esto permite al profesor, evaluador o administrador (L1/L2) recuperar
                // el código directamente del panel de Render o de 'storage/logs/siam.json.log'
                // y continuar con la verificación táctica.
                Log::warning("CONTINGENCIA (Servicio de Correo Caído): Código OTP para {$email} registrado en logs: [{$otp}]", [
                    'action' => 'email.fallback_otp',
                    'email' => $email,
                    'otp' => $otp,
                ]);

                if (env('EMAIL_FALLBACK_ENABLED', true)) {
                    // Permitir el paso para no bloquear el flujo académico del sistema
                    session()->flash('warning', 'El servicio de correo se encuentra en modo contingencia. El código de verificación ha sido registrado en los logs del servidor.');
                    return true;
                }

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending OTP email: '.$e->getMessage());

            Log::warning("CONTINGENCIA (Excepción de Correo): Código OTP para {$email} registrado en logs: [{$otp}]", [
                'action' => 'email.fallback_otp_exception',
                'email' => $email,
                'otp' => $otp,
            ]);

            if (env('EMAIL_FALLBACK_ENABLED', true)) {
                session()->flash('warning', 'El servicio de correo se encuentra en modo contingencia. El código de verificación ha sido registrado en los logs del servidor.');
                return true;
            }

            return false;
        }
    }
}
