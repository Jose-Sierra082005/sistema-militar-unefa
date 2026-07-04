<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\Google2FAService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

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
        
        // Clean dots and dashes to match normalized database Cédula (e.g. 31.149.881 -> 31149881)
        $cleanCedula = str_replace(['.', '-', ' '], '', $loginInput);
        // If it starts with V or E, clean it too
        $cleanCedulaNumeric = preg_replace('/^[VEve]/', '', $cleanCedula);

        // Find user by email, exact input, or cleaned numeric Cédula
        $user = User::where('email', $loginInput)
            ->orWhere('cedula', $loginInput)
            ->orWhere('cedula', $cleanCedula)
            ->orWhere('cedula', $cleanCedulaNumeric)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if user has Two-Factor Authentication enabled
            if ($user->two_factor_enabled && !empty($user->two_factor_secret)) {
                // Save user ID temporarily in session
                session([
                    'auth.2fa.user_id' => $user->id,
                    'auth.2fa.remember' => $request->has('remember')
                ]);

                return redirect()->route('two-factor.verify')->with('info', 'Autenticación de Doble Factor requerida.');
            }

            // Normal login if 2FA is not enabled
            Auth::login($user, $request->has('remember'));
            return redirect()->intended('/')->with('success', '¡Conexión segura establecida con éxito!');
        }

        // Demo fallback for presentation/evaluation (admin user)
        if ($loginInput === 'admin@unefa.edu.ve' && $request->password === 'password123') {
            $demoUser = User::firstOrCreate(
                ['email' => 'admin@unefa.edu.ve'],
                [
                    'name' => 'Oficial UNEFA (Demo)',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                ]
            );

            if ($demoUser->role !== 'admin') {
                $demoUser->update(['role' => 'admin']);
            }

            Auth::login($demoUser, $request->has('remember'));
            return redirect()->intended('/')->with('success', '¡Conexión segura de demostración establecida!');
        }

        return back()->withErrors([
            'email' => 'Credenciales de seguridad incorrectas o firma digital no reconocida.'
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada de forma segura.');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Sanitize string inputs to prevent SQLi / XSS vulnerabilities
        $name = strip_tags(trim($request->name));
        $cedula = strip_tags(trim($request->cedula));
        $email = strip_tags(trim($request->email));

        // Clean dots/dashes from Cédula input before unique validation in DB
        $cleanCedula = str_replace(['.', '-', ' '], '', $cedula);

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
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
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
            'password' => Hash::make($request->password),
            'two_factor_enabled' => false,
        ]);

        // Store user ID in session for 2FA setup stage
        session(['auth.2fa.setup_user_id' => $user->id]);

        return redirect()->route('two-factor.setup')->with('success', 'Registro completado. Proceda a activar el Doble Factor (2FA).');
    }

    public function showTwoFactorSetup()
    {
        $userId = session('auth.2fa.setup_user_id');
        if (!$userId) {
            return redirect()->route('register')->withErrors(['email' => 'Sesión de registro expirada. Inicie el proceso nuevamente.']);
        }

        $user = User::findOrFail($userId);
        
        // Generate new Google 2FA Secret Key
        $secret = Google2FAService::generateSecretKey();
        
        // Generate QR code URI
        $qrCodeUrl = Google2FAService::getQRCodeUrl($user->name, $user->email, $secret);

        return view('auth.two-factor-setup', compact('secret', 'qrCodeUrl'));
    }

    public function activateTwoFactor(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $userId = session('auth.2fa.setup_user_id');
        if (!$userId) {
            return redirect()->route('register')->withErrors(['email' => 'Sesión de registro expirada.']);
        }

        $user = User::findOrFail($userId);

        // Verify the code input
        $isValid = Google2FAService::verifyCode($request->secret, $request->code);

        if ($isValid) {
            // Update user with active 2FA
            $user->update([
                'two_factor_secret' => $request->secret,
                'two_factor_enabled' => true,
            ]);

            // Login user
            Auth::login($user);

            // Clean up session
            session()->forget('auth.2fa.setup_user_id');

            return redirect()->route('dashboard')->with('success', '¡Autenticación de Doble Factor activada con éxito!');
        }

        return back()->withErrors(['code' => 'Código de verificación incorrecto. Inténtelo de nuevo.']);
    }

    public function showTwoFactorVerify()
    {
        if (!session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('auth.2fa.user_id');
        if (!$userId) {
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
        
        // If cedula is not set yet, require it
        if (empty($user->cedula)) {
            $cedula = strip_tags(trim($request->cedula));
            $cleanCedula = str_replace(['.', '-', ' '], '', $cedula);
            $request->merge(['cedula' => $cleanCedula]);
            
            $rules['cedula'] = 'required|string|unique:users,cedula,' . $user->id;
            $messages['cedula.required'] = 'La Cédula de Identidad es obligatoria.';
            $messages['cedula.unique'] = 'Esta Cédula ya está registrada en el sistema.';
        }
        
        // If a password change is requested
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ];
            $messages['password.min'] = 'La contraseña debe tener al menos :min caracteres.';
            $messages['password.confirmed'] = 'La confirmación de la contraseña no coincide.';
            $messages['password.regex'] = 'La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).';
        }
        
        $request->validate($rules, $messages);
        
        $updateData = [];
        if (empty($user->cedula)) {
            $updateData['cedula'] = $request->cedula;
        }
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        if (!empty($updateData)) {
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
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expiresAt = now()->addMinutes(15);

        // Store OTP in database
        \Illuminate\Support\Facades\DB::table('password_reset_otps')->insert([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);

        // Send Email via Resend
        $emailSent = \App\Services\EmailService::sendOtpEmail($request->email, $user->name, $otp);

        if ($emailSent) {
            session(['password.reset.email' => $request->email]);
            return redirect()->route('password.verify_otp')->with('success', 'Se ha enviado un código de seguridad OTP a su correo electrónico.');
        }

        return back()->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Inténtelo más tarde.'])->withInput();
    }

    public function showVerifyOtpForm()
    {
        $email = session('password.reset.email');
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)->first();
        $requires2fa = $user && $user->two_factor_enabled && !empty($user->two_factor_secret);

        return view('auth.verify-otp', compact('requires2fa', 'email'));
    }

    public function verifyResetOtp(Request $request)
    {
        $email = session('password.reset.email');
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)->firstOrFail();
        $requires2fa = $user->two_factor_enabled && !empty($user->two_factor_secret);

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
        $otpRecord = \Illuminate\Support\Facades\DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $request->code)
            ->where('expires_at', '>=', now())
            ->orderBy('id', 'desc')
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['code' => 'El código OTP ingresado es inválido o ha expirado.'])->withInput();
        }

        // Validate 2FA if active
        if ($requires2fa) {
            $isValid2fa = Google2FAService::verifyCode($user->two_factor_secret, $request->two_factor_code);
            if (!$isValid2fa) {
                return back()->withErrors(['two_factor_code' => 'El código de Doble Factor (2FA) es incorrecto o ha expirado.'])->withInput();
            }
        }

        // Mark OTP as verified in session
        session(['password.reset.verified' => true]);

        // Clean verified OTP record from database
        \Illuminate\Support\Facades\DB::table('password_reset_otps')->where('email', $email)->delete();

        return redirect()->route('password.reset')->with('success', 'Código(s) de seguridad verificado(s) con éxito. Proceda a cambiar su contraseña.');
    }

    public function showResetForm()
    {
        if (!session('password.reset.verified') || !session('password.reset.email')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset');
    }

    public function resetPassword(Request $request)
    {
        $email = session('password.reset.email');
        if (!session('password.reset.verified') || !$email) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Regex: at least 1 uppercase, 1 lowercase, 1 number, 1 special character
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'Seguridad de Tactic Force: La clave debe incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@, $, !, %, *, ?, &).',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear password reset session data
        session()->forget(['password.reset.email', 'password.reset.verified']);

        return redirect()->route('login')->with('success', 'Contraseña restablecida con éxito. Inicie sesión con su nueva clave.');
    }
}
