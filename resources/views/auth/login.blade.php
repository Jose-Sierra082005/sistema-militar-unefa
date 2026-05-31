<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Militar UNEFA</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #0c0f17;
            --card-bg: rgba(18, 24, 38, 0.65);
            --border-color: rgba(46, 74, 53, 0.4);
            --primary-green: #2e4a35;
            --primary-hover: #3d6347;
            --accent-gold: #c9a054;
            --accent-hover: #e0b35e;
            --text-light: #e6ebf5;
            --text-muted: #8c9ba5;
            --error-color: #ff5c5c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 20%, rgba(46, 74, 53, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(201, 160, 84, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Tactical Top Border Accent */
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--accent-gold));
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-area {
            font-size: 2.2rem;
            color: var(--accent-gold);
            margin-bottom: 12px;
            text-shadow: 0 0 10px rgba(201, 160, 84, 0.3);
            display: inline-block;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-light);
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            background: rgba(10, 12, 18, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            padding: 14px 15px 14px 45px;
            color: var(--text-light);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 8px rgba(201, 160, 84, 0.2);
            background: rgba(10, 12, 18, 0.95);
        }

        .form-input:focus + .input-icon {
            color: var(--accent-gold);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: var(--text-muted);
            user-select: none;
        }

        .remember-me input {
            margin-right: 8px;
            accent-color: var(--primary-green);
        }

        .forgot-link {
            color: var(--accent-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-green);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-light);
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            border-color: rgba(201, 160, 84, 0.4);
            box-shadow: 0 6px 15px rgba(46, 74, 53, 0.3);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 20px;
        }

        .footer a {
            color: var(--accent-gold);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Micro-animations and responsiveness */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="header">
            <div class="logo-area">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="title">Sistema Militar</h2>
            <p class="subtitle">Portal de Formación y Evaluaciones Académicas <br> UNEFA Falcón</p>
        </div>

        @if ($errors->any())
            <div style="background: rgba(255, 92, 92, 0.15); border: 1px solid var(--error-color); color: var(--error-color); padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.85rem; text-align: center; line-height: 1.4;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 5px;"></i> {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div style="background: rgba(46, 74, 53, 0.2); border: 1px solid var(--primary-green); color: var(--accent-gold); padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.85rem; text-align: center; line-height: 1.4;">
                <i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Cédula o Correo Electrónico</label>
                <div class="input-wrapper">
                    <input type="text" name="email" class="form-input" placeholder="ej. 31.149.881 o estudiante@unefa.ve" value="{{ old('email') }}" required>
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
                <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-submit">
                Ingresar al Sistema
            </button>
        </form>

        <div class="footer">
            <p>&copy; 2026 Sistema Militar UNEFA. Todos los derechos reservados.</p>
            <p>Soporte Técnico: <a href="mailto:soporte@unefafalcon.edu.ve">soporte@unefafalcon.edu.ve</a></p>
        </div>
    </div>

</body>
</html>
