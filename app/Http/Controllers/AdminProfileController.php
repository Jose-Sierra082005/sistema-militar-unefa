<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonCompletion;
use App\Models\User;
use App\Services\EmailService;
use App\Services\Google2FAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class AdminProfileController
 * Gestiona el perfil del administrador, incluyendo datos básicos, cambio de clave y 2FA.
 */
class AdminProfileController extends Controller
{
    /**
     * Muestra la vista de configuración del perfil del administrador con estadísticas rápidas.
     *
     * @return View
     */
    public function showProfile()
    {
        $user = Auth::user();
        $studentCount = User::where('role', 'student')->count();
        $courseCount = Course::count();
        $lessonCompletionCount = LessonCompletion::count();

        return view('admin.profile.index', compact('user', 'studentCount', 'courseCount', 'lessonCompletionCount'));
    }

    /**
     * Actualiza los datos básicos del administrador (nombre, correo y cédula).
     *
     * @return RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];

        // Solo se valida y actualiza la cédula si no ha sido registrada previamente y se ingresó un valor
        if (empty($user->cedula) && $request->filled('cedula')) {
            $cedula = strip_tags(trim($request->cedula));
            $cleanCedula = User::normalizeCedula($cedula);
            $request->merge(['cedula' => $cleanCedula]);
            $rules['cedula'] = 'nullable|string|unique:users,cedula,'.$user->id;
        }

        $request->validate($rules, [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'El correo ya está en uso.',
            'cedula.unique' => 'La cédula ya está registrada en el sistema.',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if (empty($user->cedula) && $request->filled('cedula')) {
                $updateData['cedula'] = $request->cedula;
            }

            $user->update($updateData);

            Log::info('[INFO] ['.now()->toDateString()."]: Administrador {$user->email} actualizó sus datos de perfil.");

            return redirect()->route('admin.profile.show')
                ->with('success', 'Perfil administrativo actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Error al actualizar el perfil del administrador. Detalle: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error inesperado al actualizar el perfil.');
        }
    }

    /**
     * Cambia la contraseña del administrador.
     *
     * @return RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ];

        // Si no inició con Google OAuth y tiene contraseña actual, se exige verificarla
        if (empty($user->google_id)) {
            $rules['current_password'] = 'required|string';
        }

        $request->validate($rules, [
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
            'new_password.regex' => 'Seguridad de SIAM: La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).',
            'current_password.required' => 'La contraseña actual es obligatoria para verificar su identidad.',
        ]);

        try {
            if (empty($user->google_id) && ! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual ingresada es incorrecta.']);
            }

            $user->update([
                'password' => $request->new_password,
            ]);

            Log::info('[INFO] ['.now()->toDateString()."]: Administrador {$user->email} cambió su contraseña de forma segura.");

            return redirect()->route('admin.profile.show')
                ->with('success', 'Contraseña de administrador actualizada con éxito.');

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Error al cambiar contraseña del administrador. Detalle: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error inesperado al actualizar la contraseña.');
        }
    }

    /**
     * Activa la autenticación de doble factor (2FA) para el administrador.
     *
     * @return RedirectResponse
     */
    public function activate2FA(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código de Google Authenticator es obligatorio.',
            'code.size' => 'El código debe tener exactamente 6 dígitos.',
        ]);

        try {
            $user = Auth::user();
            $isValid = Google2FAService::verifyCode($request->secret, $request->code);

            if ($isValid) {
                $user->update([
                    'two_factor_secret' => $request->secret,
                    'two_factor_enabled' => true,
                ]);

                Log::info('[INFO] ['.now()->toDateString()."]: Administrador {$user->email} activó el Doble Factor (2FA).");

                return redirect()->route('admin.profile.show')
                    ->with('success', '¡Autenticación de Doble Factor (2FA) activada con éxito!');
            }

            return back()->withErrors(['code' => 'Código de verificación incorrecto. Inténtelo de nuevo.']);

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Excepción al activar 2FA para el administrador. Detalle: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error inesperado al activar el doble factor.');
        }
    }

    /**
     * Solicita y envía un código OTP por correo electrónico para desactivar el 2FA.
     *
     * @return RedirectResponse
     */
    public function send2FADisableOtp(Request $request)
    {
        $user = Auth::user();

        try {
            $otp = sprintf('%06d', mt_rand(0, 999999));

            // Guardamos temporalmente los datos del OTP en sesión
            session([
                'admin.2fa.disable.otp' => $otp,
                'admin.2fa.disable.expires' => now()->addMinutes(15)->timestamp,
                'admin.2fa.disable.pending' => true,
            ]);

            Log::info('[INFO] ['.now()->toDateString().']: Solicitud de desactivación de 2FA para administrador: '.$user->email);

            $emailSent = EmailService::sendOtpEmail($user->email, $user->name, $otp);

            if (! $emailSent) {
                Log::error('[ERROR] ['.now()->toDateString().']: No se pudo enviar el correo OTP para desactivar 2FA al administrador.');

                return back()->with('error', 'No se pudo enviar el código de verificación al correo.');
            }

            return redirect()->route('admin.profile.show')
                ->with('success', 'Se ha enviado un código de desactivación OTP a su correo electrónico institucional.')
                ->with('admin_2fa_otp_sent', true);

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Excepción al solicitar desactivación 2FA. Detalle: '.$e->getMessage());

            return back()->with('error', 'Error del sistema: No se pudo procesar la solicitud.');
        }
    }

    /**
     * Confirma la desactivación del 2FA ingresando el código OTP enviado.
     *
     * @return RedirectResponse
     */
    public function disable2FA(Request $request)
    {
        $request->validate([
            'disable_otp' => 'required|string|size:6',
        ], [
            'disable_otp.required' => 'El código OTP es obligatorio.',
            'disable_otp.size' => 'El código OTP debe ser de 6 dígitos.',
        ]);

        try {
            $user = Auth::user();
            $storedOtp = session('admin.2fa.disable.otp');
            $expires = session('admin.2fa.disable.expires');

            if (! $storedOtp || ! $expires || now()->timestamp > $expires || $request->disable_otp !== $storedOtp) {
                return redirect()->route('admin.profile.show')
                    ->with('admin_2fa_otp_sent', true)
                    ->withErrors(['disable_otp' => 'El código OTP ingresado es inválido o ha expirado.']);
            }

            $user->update([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ]);

            session()->forget([
                'admin.2fa.disable.otp',
                'admin.2fa.disable.expires',
                'admin.2fa.disable.pending',
            ]);

            Log::info('[INFO] ['.now()->toDateString()."]: Administrador {$user->email} desactivó el Doble Factor (2FA).");

            return redirect()->route('admin.profile.show')
                ->with('success', 'La autenticación de doble factor ha sido desactivada correctamente.');

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Excepción al confirmar desactivación 2FA. Detalle: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error inesperado al procesar la confirmación.');
        }
    }
}
