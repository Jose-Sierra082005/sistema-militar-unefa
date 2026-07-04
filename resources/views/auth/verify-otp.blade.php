<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Código - Tactic Force</title>
    
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
            pointer-events: none;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 480px;
            background: var(--panel-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 45px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 10;
        }

        .corner-border {
            position: absolute;
            width: 12px;
            height: 12px;
            border-color: var(--accent-gold);
            border-style: solid;
            pointer-events: none;
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
        }

        .title {
            font-size: 1.5rem;
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
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Share Tech Mono', monospace;
            text-align: center;
        }

        .form-input-code {
            width: 100%;
            background: rgba(7, 9, 14, 0.9);
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            padding: 14px;
            color: var(--text-main);
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 8px;
            font-family: 'Share Tech Mono', monospace;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input-code:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.2);
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
        }

        .btn-submit:hover {
            border-color: var(--accent-gold);
            box-shadow: 0 6px 20px rgba(42, 71, 51, 0.4);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
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

        .forgot-link {
            color: var(--accent-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
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
            .title {
                font-size: 1.3rem;
            }
            .subtitle {
                font-size: 0.8rem;
            }
            .form-input-code {
                font-size: 1.25rem;
                padding: 10px;
                letter-spacing: 4px;
            }
            .btn-submit {
                padding: 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="corner-border cb-tl"></div>
        <div class="corner-border cb-tr"></div>
        <div class="corner-border cb-bl"></div>
        <div class="corner-border cb-br"></div>

        <div style="text-align: center;">
            <div class="security-badge">
                <i class="fa-solid fa-shield-halved"></i> Verificación Requerida
            </div>
        </div>

        <div class="header">
            <div class="logo-container">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="title">Verificación de Identidad</h2>
            <p class="subtitle">Introduzca los códigos correspondientes para continuar con la restauración de la clave militar.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="display: flex; flex-direction: column;">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('password.verify_otp') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Código OTP (Enviado al Correo)</label>
                <input type="text" name="code" class="form-input-code" placeholder="000000" maxlength="6" autocomplete="off" autofocus required>
            </div>

            @if($requires2fa)
                <div class="form-group" style="margin-top: 30px; border-top: 1px dashed rgba(201, 160, 84, 0.2); padding-top: 25px;">
                    <label class="form-label" style="color: var(--accent-gold);">Código de Doble Factor (Google Authenticator)</label>
                    <input type="text" name="two_factor_code" class="form-input-code" placeholder="000000" maxlength="6" autocomplete="off" required style="border-color: rgba(201, 160, 84, 0.45);">
                    <div style="font-size: 0.72rem; color: var(--text-secondary); text-align: center; margin-top: 8px;">
                        <i class="fa-solid fa-circle-info" style="color: var(--accent-gold); margin-right: 4px;"></i> Esta cuenta cuenta con protección estricta 2FA. Ingrese el token de su celular.
                    </div>
                </div>
            @endif

            <button type="submit" class="btn-submit" style="margin-top: 15px;">
                Verificar Códigos
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.9rem;">
            <a href="{{ route('password.forgot') }}" class="forgot-link">¿No recibió el correo? Volver a intentar</a>
        </div>
    </div>

</body>
</html>
