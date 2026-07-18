<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Doble Factor - SIAM</title>
    
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
            pointer-events: none;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            background: var(--panel-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 40px;
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
            margin-bottom: 25px;
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

        /* QR Area styling */
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .qr-image-wrapper {
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(42, 71, 51, 0.25);
            margin-bottom: 15px;
        }

        .qr-image-wrapper img {
            display: block;
            width: 180px;
            height: 180px;
        }

        .secret-key-box {
            font-family: 'Share Tech Mono', monospace;
            background: rgba(7, 9, 14, 0.85);
            border: 1px dashed rgba(201, 160, 84, 0.4);
            padding: 8px 16px;
            border-radius: 6px;
            color: var(--accent-gold-hover);
            letter-spacing: 2px;
            font-size: 1rem;
            margin-top: 10px;
            user-select: all;
            text-align: center;
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

        .instruction-step {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 12px;
            line-height: 1.4;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .instruction-step i {
            color: var(--accent-gold);
            margin-top: 2px;
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
                font-size: 1.35rem;
            }
            .subtitle {
                font-size: 0.8rem;
            }
            .qr-container {
                padding: 15px;
            }
            .qr-image-wrapper img {
                width: 140px;
                height: 140px;
            }
            .secret-key-box {
                font-size: 0.85rem;
                letter-spacing: 1px;
                padding: 6px 12px;
                width: 100%;
                box-sizing: border-box;
                word-break: break-all;
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
                <i class="fa-solid fa-key"></i> Doble Factor Requerido
            </div>
        </div>

        <div class="header">
            <h2 class="title">{{ !empty($recoverMode) ? 'Restablecer 2FA' : 'Configuración 2FA' }}</h2>
            <p class="subtitle">
                @if(!empty($recoverMode))
                    Escanee el nuevo código QR en Google Authenticator. El código anterior quedará invalidado.
                @else
                    Vincule su cuenta con Google Authenticator para habilitar el acceso seguro
                @endif
            </p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div style="margin-bottom: 25px;">
            <div class="instruction-step">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>1. Descargue e instale <strong>Google Authenticator</strong> (o Authy) en su dispositivo móvil.</span>
            </div>
            <div class="instruction-step">
                <i class="fa-solid fa-qrcode"></i>
                <span>2. Escanee el código QR que se muestra a continuación o ingrese manualmente la clave secreta.</span>
            </div>
        </div>

        <div class="qr-container">
            <div class="qr-image-wrapper">
                <!-- Google charts / QRServer API to generate QR Code safely -->
                <img src="{{ $qrCodeImageUrl }}" alt="Escanear Código QR de 2FA">
            </div>
            <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase;">Clave Secreta Manual:</span>
            <div class="secret-key-box">{{ $secret }}</div>
            @if(!empty($recoverMode))
                <p style="font-size: 0.75rem; color: var(--accent-gold); margin-top: 12px; line-height: 1.5; text-align: center;">
                    <i class="fa-solid fa-circle-info"></i>
                    Escanee este QR una sola vez. El código tiene <strong>6 dígitos</strong> (no 3). No recargue la página hasta confirmar.
                </p>
            @endif
        </div>

        <form action="{{ $activateRoute ?? route('two-factor.activate') }}" method="POST">
            @csrf
            <!-- We pass the secret in form to preserve it during submission validation -->
            <input type="hidden" name="secret" value="{{ $secret }}">
            
            <div class="form-group">
                <label class="form-label">Código de Verificación de 6 Dígitos</label>
                <input type="text" name="code" class="form-input-code" placeholder="000000" maxlength="6" minlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" required value="{{ old('code') }}">
                <p style="font-size: 0.72rem; color: var(--text-secondary); text-align: center; margin-top: 8px;">
                    Abra Google Authenticator y copie los <strong>6 números</strong> que aparecen para SIAM.
                </p>
            </div>

            <button type="submit" class="btn-submit">
                {{ !empty($recoverMode) ? 'Confirmar Nuevo Google Authenticator' : 'Verificar y Habilitar Acceso' }}
            </button>
        </form>
    </div>

</body>
</html>
