<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Google Authenticator - SIAM</title>
    
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
            position: relative;
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

        .btn-submit:hover {
            border-color: var(--accent-gold);
            box-shadow: 
                0 6px 20px rgba(42, 71, 51, 0.4),
                0 0 10px rgba(212, 175, 55, 0.1);
        }

        .forgot-link {
            color: var(--accent-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--accent-gold-hover);
            text-decoration: underline;
        }

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

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 20px;
            line-height: 1.6;
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
            .btn-submit {
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
                <i class="fa-solid fa-mobile-screen"></i> Recuperación 2FA
            </div>
        </div>

        <div class="header">
            <div class="logo-container">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <h2 class="title">Restablecer Authenticator</h2>
            <p class="subtitle">Si perdió el acceso a Google Authenticator, verificaremos su identidad por correo y generará un nuevo código QR.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('two-factor.recover.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Correo Electrónico de la Cuenta</label>
                <div class="input-wrapper">
                    <input type="email" name="email" class="form-input" placeholder="ej. estudiante@unefa.edu.ve" value="{{ old('email', $prefillEmail ?? '') }}" required autofocus>
                    <i class="fa-solid fa-envelope input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Enviar Código OTP al Correo
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.9rem;">
            <a href="{{ route('login') }}" class="forgot-link" style="font-weight: 600;"><i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Volver al Login</a>
            <a href="{{ route('password.forgot') }}" class="forgot-link" style="display: block; margin-top: 12px; font-size: 0.82rem;">¿Solo olvidó su contraseña?</a>
        </div>

        <div class="footer">
            <p>&copy; 2026 SIAM. Todos los derechos reservados.</p>
        </div>
    </div>

</body>
</html>
