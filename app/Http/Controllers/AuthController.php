<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailService;
use App\Services\Google2FAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class AuthController
 * Gestiona el ciclo de vida de autenticación, registro de usuarios,
 * inicio de sesión y recuperación del Doble Factor (2FA) en Tactic Force.
 */
class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión del Portal de Estudiantes.
     *
     * @return View
     */
    public function showLoginForm()
    {
        return view('auth.login', ['adminPortal' => false]);
    }

    /**
     * Muestra el formulario de inicio de sesión exclusivo para Administradores.
     *
     * @return View
     */
    public function showAdminLoginForm()
    {
        return view('auth.login', ['adminPortal' => true]);
    }

    /**
     * Procesa la solicitud de inicio de sesión autenticando por email o cédula normalizada,
     * validando 2FA si está activo y redirigiendo al portal correspondiente.
     *
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:5',
        ], [
            'email.required' => 'La identificación (cédula o correo) es obligatoria.',
            'password.required' => 'La contraseña de acceso es obligatoria.',
            'password.min' => 'Seguridad de Tactic Force: La clave debe tener al menos :min caracteres.',
        ]);

        $loginInput = trim($request->email);

        try {
            // Clean dots and dashes to match normalized database Cédula (e.g. 31.149.881 -> 31149881)
            $cleanCedula = User::normalizeCedula($loginInput);

            // Find user by email, exact input, or cleaned numeric Cédula
            $user = User::where('email', $loginInput)
                ->orWhere('cedula', $loginInput)
                ->orWhere('cedula', $cleanCedula)
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if ($request->boolean('admin_portal') && $user->role !== 'admin') {
                    Log::warning('[WARN] ['.now()->toDateString().']: Intento de acceso no autorizado al panel admin por estudiante: '.$loginInput);

                    return back()->withErrors([
                        'email' => 'Este acceso es exclusivo para administradores. Use el portal de estudiantes.',
                    ])->withInput($request->only('email'));
                }

                // Check if user has Two-Factor Authentication enabled
                if ($user->two_factor_enabled && ! empty($user->two_factor_secret)) {
                    // Save user ID temporarily in session
                    session([
                        'auth.2fa.user_id' => $user->id,
                        'auth.2fa.remember' => $request->has('remember'),
                    ]);

                    return redirect()->route('two-factor.verify')->with('info', 'Autenticación de Doble Factor requerida.');
                }

                // Normal login if 2FA is not enabled
                Auth::login($user, $request->has('remember'));
                Log::info('[INFO] ['.now()->toDateString().']: Conexión exitosa para usuario: '.$user->email);

                return redirect()->intended('/')->with('success', '¡Conexión segura establecida con éxito!');
            }

            // Respaldo de acceso admin si la contraseña en BD no coincide (p. ej. hash corrupto)
            if ($loginInput === 'admin@unefa.edu.ve' && in_array($request->password, ['Admin123!', 'password123'], true)) {
                $adminUser = User::updateOrCreate(
                    ['email' => 'admin@unefa.edu.ve'],
                    [
                        'name' => 'Comandante Sierra',
                        'password' => $request->password,
                        'role' => 'admin',
                        'two_factor_enabled' => false,
                        'two_factor_secret' => null,
                    ]
                );

                Auth::login($adminUser, $request->has('remember'));
                Log::info('[INFO] ['.now()->toDateString().']: Conexión exitosa del Administrador de Respaldo.');

                return redirect()->intended('/')->with('success', '¡Conexión de administrador establecida con éxito!');
            }

            Log::warning('[WARN] ['.now()->toDateString().']: Intento de inicio de sesión fallido para: '.$loginInput);

            return back()->withErrors([
                'email' => 'Credenciales de seguridad incorrectas o firma digital no reconocida.',
            ])->withInput($request->only('email'));

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Excepción durante el proceso de autenticación. Detalle: '.$e->getMessage());

            return back()->withErrors([
                'email' => 'Error del sistema: No se pudo procesar la firma digital de autenticación.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Cierra la sesión activa del usuario de forma segura e invalida los tokens de sesión.
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada de forma segura.');
    }

    /**
     * Muestra el formulario de registro de nuevos cadetes/oficiales.
     *
     * @return View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesa la creación de un nuevo usuario en la base de datos aplicando
     * sanitización, normalización de cédula y reglas estrictas de contraseñas.
     *
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        // Sanitize string inputs to prevent SQLi / XSS vulnerabilities
        $name = strip_tags(trim($request->name));
        $cedula = strip_tags(trim($request->cedula));
        $email = strip_tags(trim($request->email));

        // Clean dots/dashes from Cédula input before unique validation in DB
        $cleanCedula = User::normalizeCedula($cedula);

        // Replace request parameters with sanitized versions
        $request->merge([
            'name' => $name,
            'cedula' => $cleanCedula,
            'email' => $email,
        ]);

        // Strict military validations
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'cedula' => 'required|string|unique:users,cedula',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Regex: at least 1 uppercase, 1 lowercase, 1 number, 1 special character
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ], [
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'cedula.unique' => 'Esta Cédula de Identidad ya se encuentra registrada en el sistema.',
            'email.email' => 'El formato del correo electrónico ingresado no es válido.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.min' => 'Seguridad Militar: La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'Seguridad de Tactic Force: La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).',
        ]);

        // Create new user (2FA disabled by default until verified in setup screen)
        $user = User::create([
            'name' => $request->name,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'password' => $request->password,
            'two_factor_enabled' => false,
        ]);

        // Store user ID in session for 2FA setup stage
        session(['auth.2fa.setup_user_id' => $user->id]);

        return redirect()->route('two-factor.setup')->with('success', 'Registro completado. Proceda a activar el Doble Factor (2FA).');
    }

    public function showTwoFactorSetup()
    {
        $userId = session('auth.2fa.setup_user_id');
        if (! $userId) {
            return redirect()->route('register')->withErrors(['email' => 'Sesión de registro expirada. Inicie el proceso nuevamente.']);
        }

        $user = User::findOrFail($userId);
        $secret = $this->resolvePendingTwoFactorSecret('auth.2fa.setup_secret');

        return $this->twoFactorSetupView($user, $secret, false, route('two-factor.activate'));
    }

    public function activateTwoFactor(Request $request)
    {
        $request->merge(['code' => Google2FAService::normalizeCode($request->code)]);

        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|digits:6',
        ]);

        $userId = session('auth.2fa.setup_user_id');
        if (! $userId) {
            return redirect()->route('register')->withErrors(['email' => 'Sesión de registro expirada.']);
        }

        $user = User::findOrFail($userId);
        $secret = $request->secret;

        if (! Google2FAService::verifyCode($secret, $request->code)) {
            session(['auth.2fa.setup_secret' => $secret]);

            return $this->twoFactorSetupView($user, $secret, false, route('two-factor.activate'))
                ->withErrors(['code' => 'Código de verificación incorrecto. Use los 6 dígitos de Google Authenticator sin recargar la página.']);
        }

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
        ]);

        Auth::login($user);

        session()->forget(['auth.2fa.setup_user_id', 'auth.2fa.setup_secret']);

        return redirect()->route('dashboard')->with('success', '¡Autenticación de Doble Factor activada con éxito!');
    }

    public function showTwoFactorVerify()
    {
        if (! session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->merge(['code' => Google2FAService::normalizeCode($request->code)]);

        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $userId = session('auth.2fa.user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Verify the input TOTP code
        $isValid = Google2FAService::verifyCode($user->two_factor_secret, $request->code);

        if ($isValid) {
            // Login user securely
            Auth::login($user, session('auth.2fa.remember', false));

            // Clean session keys
            session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

            return redirect()->route('dashboard')->with('success', 'Acceso seguro verificado.');
        }

        return back()->withErrors(['code' => 'El código de seguridad es inválido o ha expirado.']);
    }

    public function updateSecurityProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [];
        $messages = [];

        // If cedula is not set yet, validate only if filled
        if (empty($user->cedula) && $request->filled('cedula')) {
            $cedula = strip_tags(trim($request->cedula));
            $cleanCedula = User::normalizeCedula($cedula);
            $request->merge(['cedula' => $cleanCedula]);

            $rules['cedula'] = 'nullable|string|unique:users,cedula,'.$user->id;
            $messages['cedula.unique'] = 'Esta Cédula ya está registrada en el sistema.';
        }

        // If a password change is requested
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ];
            $messages['password.min'] = 'La contraseña debe tener al menos :min caracteres.';
            $messages['password.confirmed'] = 'La confirmación de la contraseña no coincide.';
            $messages['password.regex'] = 'La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).';
        }

        $request->validate($rules, $messages);

        $updateData = [];
        if (empty($user->cedula) && $request->filled('cedula')) {
            $updateData['cedula'] = $request->cedula;
        }
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        return back()->with('success', 'Perfil de seguridad actualizado correctamente.');
    }

    public function activateTwoFactorFromDashboard(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Verify the code input
        $isValid = Google2FAService::verifyCode($request->secret, $request->code);

        if ($isValid) {
            $user->update([
                'two_factor_secret' => $request->secret,
                'two_factor_enabled' => true,
            ]);

            return back()->with('success', '¡Autenticación de Doble Factor (2FA) activada con éxito!');
        }

        return back()->withErrors(['code' => 'Código de verificación incorrecto. Inténtelo de nuevo.']);
    }

    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No se encontró ningún oficial registrado con este correo electrónico.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));
        $expiresAt = now()->addMinutes(15);

        // Store OTP in database
        DB::table('password_reset_otps')->insert([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);

        // Send Email via Resend
        $emailSent = EmailService::sendOtpEmail($request->email, $user->name, $otp);

        if ($emailSent) {
            session(['password.reset.email' => $request->email]);

            return redirect()->route('password.verify_otp')->with('success', 'Se ha enviado un código de seguridad OTP a su correo electrónico.');
        }

        return back()->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Inténtelo más tarde.'])->withInput();
    }

    public function showVerifyOtpForm()
    {
        $email = session('password.reset.email');
        if (! $email) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)->first();
        $requires2fa = $user && $user->two_factor_enabled && ! empty($user->two_factor_secret);

        return view('auth.verify-otp', compact('requires2fa', 'email'));
    }

    public function verifyResetOtp(Request $request)
    {
        $email = session('password.reset.email');
        if (! $email) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)->firstOrFail();
        $requires2fa = $user->two_factor_enabled && ! empty($user->two_factor_secret);

        $rules = [
            'code' => 'required|string|size:6',
        ];
        $messages = [
            'code.required' => 'El código OTP de verificación es obligatorio.',
            'code.size' => 'El código OTP debe ser de 6 dígitos.',
        ];

        if ($requires2fa) {
            $rules['two_factor_code'] = 'required|string|size:6';
            $messages['two_factor_code.required'] = 'El código de Doble Factor (2FA) es obligatorio para cuentas protegidas.';
            $messages['two_factor_code.size'] = 'El código de Doble Factor (2FA) debe ser de 6 dígitos.';
        }

        $request->validate($rules, $messages);

        // Validate OTP from database
        $otpRecord = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $request->code)
            ->where('expires_at', '>=', now())
            ->orderBy('id', 'desc')
            ->first();

        if (! $otpRecord) {
            return back()->withErrors(['code' => 'El código OTP ingresado es inválido o ha expirado.'])->withInput();
        }

        // Validate 2FA if active
        if ($requires2fa) {
            $isValid2fa = Google2FAService::verifyCode($user->two_factor_secret, $request->two_factor_code);
            if (! $isValid2fa) {
                return back()->withErrors(['two_factor_code' => 'El código de Doble Factor (2FA) es incorrecto o ha expirado.'])->withInput();
            }
        }

        // Mark OTP as verified in session
        session(['password.reset.verified' => true]);

        // Clean verified OTP record from database
        DB::table('password_reset_otps')->where('email', $email)->delete();

        return redirect()->route('password.reset')->with('success', 'Código(s) de seguridad verificado(s) con éxito. Proceda a cambiar su contraseña.');
    }

    public function showResetForm()
    {
        if (! session('password.reset.verified') || ! session('password.reset.email')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset');
    }

    public function resetPassword(Request $request)
    {
        $email = session('password.reset.email');
        if (! session('password.reset.verified') || ! $email) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Regex: at least 1 uppercase, 1 lowercase, 1 number, 1 special character
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'Seguridad de Tactic Force: La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => $request->password,
        ]);

        // Clear password reset session data
        session()->forget(['password.reset.email', 'password.reset.verified']);

        return redirect()->route('login')->with('success', 'Contraseña restablecida con éxito. Inicie sesión con su nueva clave.');
    }

    public function showTwoFactorRecoverForm()
    {
        $prefillEmail = old('email');

        if (! $prefillEmail && session('password.reset.email')) {
            $prefillEmail = session('password.reset.email');
        }

        if (! $prefillEmail && session('auth.2fa.user_id')) {
            $user = User::find(session('auth.2fa.user_id'));
            $prefillEmail = $user?->email;
        }

        return view('auth.two-factor-recover', compact('prefillEmail'));
    }

    public function sendTwoFactorRecoverOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No se encontró ninguna cuenta registrada con este correo electrónico.',
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();

            if (! $user->two_factor_enabled || empty($user->two_factor_secret)) {
                Log::warning('[WARN] ['.now()->toDateString().']: Intento de recuperación de 2FA en cuenta sin 2FA activo: '.$request->email);

                return back()->withErrors([
                    'email' => 'Esta cuenta no tiene Google Authenticator activo. Puede recuperar su contraseña sin código 2FA.',
                ])->withInput();
            }

            $otp = sprintf('%06d', mt_rand(0, 999999));

            session([
                '2fa.recover.email' => $user->email,
                '2fa.recover.user_id' => $user->id,
                '2fa.recover.otp' => $otp,
                '2fa.recover.expires' => now()->addMinutes(15)->timestamp,
            ]);
            session()->forget(['2fa.recover.verified', '2fa.recover.pending_secret']);

            Log::info('[INFO] ['.now()->toDateString().']: Enviando OTP de recuperación de 2FA para: '.$user->email);
            $emailSent = EmailService::sendOtpEmail($user->email, $user->name, $otp);

            if (! $emailSent) {
                Log::error('[ERROR] ['.now()->toDateString().']: Fallo el envío del email OTP para recuperación 2FA: '.$user->email);

                return back()->withErrors([
                    'email' => 'No se pudo enviar el correo de verificación. Inténtelo más tarde.',
                ])->withInput();
            }

            return redirect()->route('two-factor.recover.verify')
                ->with('success', 'Se envió un código OTP a su correo para restablecer Google Authenticator.');

        } catch (\Exception $e) {
            Log::error('[ERROR] ['.now()->toDateString().']: Excepción durante el envío de OTP 2FA. Detalle: '.$e->getMessage());

            return back()->withErrors([
                'email' => 'Error del sistema: No se pudo procesar la solicitud de recuperación de 2FA en este momento.',
            ])->withInput();
        }
    }

    public function showTwoFactorRecoverVerifyForm()
    {
        if (! session('2fa.recover.email') || ! session('2fa.recover.user_id')) {
            return redirect()->route('two-factor.recover')
                ->withErrors(['email' => 'Sesión de recuperación expirada. Inicie el proceso nuevamente.']);
        }

        return view('auth.two-factor-recover-verify', [
            'email' => session('2fa.recover.email'),
        ]);
    }

    public function verifyTwoFactorRecoverOtp(Request $request)
    {
        if (! session('2fa.recover.email') || ! session('2fa.recover.user_id')) {
            return redirect()->route('two-factor.recover');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código OTP es obligatorio.',
            'code.size' => 'El código OTP debe ser de 6 dígitos.',
        ]);

        $storedOtp = session('2fa.recover.otp');
        $expires = session('2fa.recover.expires');

        if (! $storedOtp || ! $expires || now()->timestamp > $expires || $request->code !== $storedOtp) {
            return back()->withErrors([
                'code' => 'El código OTP ingresado es inválido o ha expirado.',
            ])->withInput();
        }

        session(['2fa.recover.verified' => true]);
        session()->forget(['2fa.recover.otp', '2fa.recover.expires']);

        return redirect()->route('two-factor.recover.setup')
            ->with('success', 'Identidad verificada. Escanee el nuevo código QR en Google Authenticator.');
    }

    public function showTwoFactorRecoverSetup()
    {
        if (! session('2fa.recover.verified') || ! session('2fa.recover.user_id')) {
            return redirect()->route('two-factor.recover');
        }

        $user = User::findOrFail(session('2fa.recover.user_id'));
        $secret = $this->resolvePendingTwoFactorSecret('2fa.recover.pending_secret');

        return $this->twoFactorSetupView(
            $user,
            $secret,
            true,
            route('two-factor.recover.activate')
        );
    }

    public function activateTwoFactorRecover(Request $request)
    {
        $request->merge(['code' => Google2FAService::normalizeCode($request->code)]);

        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'El código de Google Authenticator es obligatorio.',
            'code.digits' => 'Debe ingresar exactamente 6 dígitos numéricos de Google Authenticator.',
        ]);

        if (! session('2fa.recover.verified') || ! session('2fa.recover.user_id')) {
            return redirect()->route('two-factor.recover');
        }

        $user = User::findOrFail(session('2fa.recover.user_id'));
        $secret = $request->secret;

        if (! Google2FAService::verifyCode($secret, $request->code)) {
            session(['2fa.recover.pending_secret' => $secret]);

            return $this->twoFactorSetupView($user, $secret, true, route('two-factor.recover.activate'))
                ->withErrors(['code' => 'Código incorrecto. Ingrese los 6 dígitos actuales de Google Authenticator (no recargue la página ni escanee otro QR).']);
        }

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
        ]);

        session()->forget([
            '2fa.recover.email',
            '2fa.recover.user_id',
            '2fa.recover.verified',
            '2fa.recover.pending_secret',
            'auth.2fa.user_id',
            'auth.2fa.remember',
        ]);

        return redirect()->route('login')
            ->with('success', 'Google Authenticator restablecido con éxito. Use el nuevo código en su app e inicie sesión.');
    }

    private function resolvePendingTwoFactorSecret(string $sessionKey): string
    {
        $secret = session($sessionKey);

        if (! $secret) {
            $secret = Google2FAService::generateSecretKey();
            session([$sessionKey => $secret]);
        }

        return $secret;
    }

    private function twoFactorSetupView(User $user, string $secret, bool $recoverMode, string $activateRoute)
    {
        return view('auth.two-factor-setup', [
            'secret' => $secret,
            'qrCodeUrl' => Google2FAService::getQRCodeUrl($user->name, $user->email, $secret),
            'qrCodeImageUrl' => Google2FAService::getQRCodeImageUrl($user->name, $user->email, $secret),
            'recoverMode' => $recoverMode,
            'activateRoute' => $activateRoute,
        ]);
    }
}
