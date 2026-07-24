<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ !empty($adminPortal) ? 'Acceso Administrador' : 'Acceso Seguro' }} - SIAM</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #07090e;
            --panel-bg: rgba(13, 17, 27, 0.75);
            --border-primary: rgba(46, 74, 53, 0.35);
            --border-glow: rgba(201, 160, 84, 0.25);
            --tactical-green: #2a4733;
            --tactical-green-glow: rgba(42, 71, 51, 0.4);
            --accent-gold: #d4af37;
            --accent-gold-hover: #f3cd4a;
            --text-main: #f0f4f8;
            --text-secondary: #9aa5b1;
            --error-red: #ff4d4d;
            --success-green: #2ecc71;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(42, 71, 51, 0.25) 0%, transparent 45%),
                radial-gradient(circle at 90% 90%, rgba(212, 175, 55, 0.08) 0%, transparent 45%),
                linear-gradient(rgba(7, 9, 14, 0.8), rgba(7, 9, 14, 0.9));
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Tactical HUD grid overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.007) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.007) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center;
            pointer-events: none;
            z-index: 1;
        }

        /* Abstract Radar scan sweep line animation */
        .radar-sweep {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(42, 71, 51, 0.3), transparent);
            animation: sweep 8s linear infinite;
            z-index: 2;
            pointer-events: none;
        }

        @keyframes sweep {
            0% { transform: translateY(0); }
            100% { transform: translateY(100vh); }
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--panel-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 45px 40px;
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.6),
                inset 0 0 20px rgba(42, 71, 51, 0.15);
            position: relative;
            z-index: 10;
            overflow: hidden;
            transition: border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .login-card:hover {
            border-color: var(--border-glow);
            box-shadow: 
                0 25px 60px rgba(0, 0, 0, 0.7),
                0 0 30px rgba(212, 175, 55, 0.05),
                inset 0 0 25px rgba(42, 71, 51, 0.2);
        }

        /* Tactical corner borders */
        .corner-border {
            position: absolute;
            width: 12px;
            height: 12px;
            border-color: var(--accent-gold);
            border-style: solid;
            pointer-events: none;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }
        .login-card:hover .corner-border {
            opacity: 1;
        }
        .cb-tl { top: 15px; left: 15px; border-width: 2px 0 0 2px; }
        .cb-tr { top: 15px; right: 15px; border-width: 2px 2px 0 0; }
        .cb-bl { bottom: 15px; left: 15px; border-width: 0 0 2px 2px; }
        .cb-br { bottom: 15px; right: 15px; border-width: 0 2px 2px 0; }

        /* Security encrypted connection badge */
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(42, 71, 51, 0.3);
            border: 1px solid rgba(42, 71, 51, 0.6);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--accent-gold);
            margin-bottom: 25px;
            font-family: 'Share Tech Mono', monospace;
            box-shadow: 0 0 10px rgba(42, 71, 51, 0.2);
        }

        .security-badge i {
            font-size: 0.65rem;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.4; }
            50% { opacity: 1; }
            100% { opacity: 0.4; }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container {
            width: 70px;
            height: 70px;
            background: radial-gradient(circle, rgba(42, 71, 51, 0.4) 0%, rgba(7, 9, 14, 0.8) 100%);
            border: 1.5px solid var(--accent-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            font-size: 2rem;
            color: var(--accent-gold);
            box-shadow: 
                0 0 15px rgba(212, 175, 55, 0.2),
                inset 0 0 10px rgba(212, 175, 55, 0.1);
            position: relative;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 1px dashed rgba(212, 175, 55, 0.4);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        .title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--text-main), var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 400;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Share Tech Mono', monospace;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            background: rgba(7, 9, 14, 0.9);
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            padding: 14px 15px 14px 45px;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .form-input:focus {
            border-color: var(--accent-gold);
            box-shadow: 
                0 0 10px rgba(212, 175, 55, 0.15),
                inset 0 1px 3px rgba(0, 0, 0, 0.8);
            background: #090c13;
        }

        .form-input:focus + .input-icon {
            color: var(--accent-gold);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 22px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: var(--text-secondary);
            user-select: none;
            transition: color 0.2s ease;
        }

        .remember-me:hover {
            color: var(--text-main);
        }

        .remember-me input {
            margin-right: 8px;
            accent-color: var(--accent-gold);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--accent-gold);
            text-decoration: none;
            transition: color 0.2s ease, text-shadow 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--accent-gold-hover);
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--tactical-green), #1b2f21);
            border: 1px solid var(--tactical-green);
            border-radius: 8px;
            color: var(--text-main);
            padding: 14px;
            font-size: 0.95rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            transition: 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            border-color: var(--accent-gold);
            box-shadow: 
                0 6px 20px rgba(42, 71, 51, 0.4),
                0 0 10px rgba(212, 175, 55, 0.1);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.75rem;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'Share Tech Mono', monospace;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .divider:not(:empty)::before {
            margin-right: 15px;
        }

        .divider:not(:empty)::after {
            margin-left: 15px;
        }

        /* Google Sign-in Button */
        .btn-google {
            width: 100%;
            background-color: #ffffff;
            color: #1f2937;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .btn-google:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        .btn-google:active {
            transform: translateY(0);
        }

        .google-icon {
            width: 20px;
            height: 20px;
            display: block;
        }

        /* Status messages styling */
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
            font-weight: 500;
        }

        .alert-danger {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid var(--error-red);
            color: #ff9999;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--success-green);
            color: #9fedc0;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 20px;
            line-height: 1.6;
        }

        .footer a {
            color: var(--accent-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer a:hover {
            color: var(--accent-gold-hover);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .login-card {
                padding: 30px 20px;
                border-radius: 12px;
            }
            .logo-container {
                width: 60px;
                height: 60px;
                font-size: 1.6rem;
            }
            .title {
                font-size: 1.35rem;
            }
            .subtitle {
                font-size: 0.8rem;
            }
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .btn-submit, .btn-google {
                padding: 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <div class="radar-sweep"></div>

    <div class="login-card">
        <!-- Tactical borders -->
        <div class="corner-border cb-tl"></div>
        <div class="corner-border cb-tr"></div>
        <div class="corner-border cb-bl"></div>
        <div class="corner-border cb-br"></div>

        <div style="text-align: center;">
            <div class="security-badge">
                <i class="fa-solid fa-{{ !empty($adminPortal) ? 'user-shield' : 'circle-dot' }}"></i>
                {{ !empty($adminPortal) ? 'Portal Administrador' : 'Conexión Cifrada SSL' }}
            </div>
        </div>

        <div class="header">
            <div class="logo-container" style="background:none; border:none; box-shadow:none; padding:0;">
                <img src="/images/logo.png" alt="SIAM" style="height:90px; width:auto; object-fit:contain; filter: drop-shadow(0 0 10px rgba(212,175,55,0.4));">
            </div>
            @if(!empty($adminPortal))
                <p class="subtitle">Panel de Administración Académica<br>Gestión de cursos, lecciones y evaluaciones</p>
            @else
                <p class="subtitle">Sistema Interactivo de Aprendizaje Militar<br>Acceso Seguro al Portal Académico</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            @if(!empty($adminPortal))
                <input type="hidden" name="admin_portal" value="1">
            @endif
            <div class="form-group">
                <label class="form-label">Cédula o Correo Electrónico</label>
                <div class="input-wrapper">
                    <input type="text" name="email" class="form-input" placeholder="ej. 31.149.881 o su correo electrónico" value="{{ old('email') }}" required>
                    <i class="fa-solid fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    <i class="fa-solid fa-lock input-icon"></i>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Recordarme
                </label>
                <a href="{{ route('password.forgot') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <div style="text-align: center; margin-bottom: 8px;">
                <a href="{{ route('two-factor.recover') }}" class="forgot-link" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-mobile-screen"></i> ¿Perdió su Google Authenticator?
                </a>
            </div>

            <button type="submit" class="btn-submit">
                {{ !empty($adminPortal) ? 'Ingresar como Administrador' : 'Ingresar a SIAM' }}
            </button>
        </form>

        @if(!empty($adminPortal))
            <div style="text-align: center; margin-top: 18px; font-size: 0.85rem;">
                <a href="{{ route('login') }}" class="forgot-link">¿Es estudiante? Ir al portal cadete</a>
            </div>
        @else
            <div style="text-align: center; margin-bottom: 8px; margin-top: 4px;">
                <a href="{{ route('admin.login') }}" class="forgot-link" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-user-shield"></i> Acceso administrador
                </a>
            </div>

            <div class="divider">Ó</div>

            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                <span>Acceder con Google</span>
            </a>

            <div style="text-align: center; margin-top: 25px; font-size: 0.9rem;">
                <span style="color: var(--text-secondary);">¿No tienes una cuenta?</span>
                <a href="{{ route('register') }}" class="forgot-link" style="margin-left: 5px; font-weight: 600;">Registrar Oficial</a>
            </div>
        @endif

        <div class="footer">
            <p>&copy; 2026 SIAM. Todos los derechos reservados.</p>
            <p>Sistema Interactivo de Aprendizaje Militar</p>
        </div>
    </div>

</body>
</html>
