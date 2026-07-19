<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado — SIAM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #07090e;
            --panel-bg: rgba(13, 17, 27, 0.85);
            --border-primary: rgba(46, 74, 53, 0.4);
            --border-glow: rgba(201, 160, 84, 0.3);
            --accent-gold: #d4af37;
            --text-main: #f0f4f8;
            --text-muted: rgba(180, 194, 200, 0.7);
            --warning-amber: #e67e22;
            --warning-amber-glow: rgba(230, 126, 34, 0.25);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg-dark);
            background-image:
                radial-gradient(ellipse at 20% 40%, rgba(230, 126, 34, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 60%, rgba(42, 71, 51, 0.08) 0%, transparent 60%);
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-container { max-width: 580px; width: 100%; text-align: center; }
        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(230, 126, 34, 0.1);
            border: 1px solid rgba(230, 126, 34, 0.4);
            border-radius: 999px;
            padding: 0.4rem 1.2rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--warning-amber);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .error-code {
            font-size: 7rem;
            font-weight: 800;
            line-height: 1;
            color: var(--warning-amber);
            text-shadow: 0 0 60px var(--warning-amber-glow);
            font-family: 'Share Tech Mono', monospace;
            margin-bottom: 0.5rem;
            opacity: 0.85;
        }
        .error-title { font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem; }
        .divider {
            width: 60px; height: 2px;
            background: linear-gradient(to right, transparent, var(--accent-gold), transparent);
            margin: 1.5rem auto;
        }
        .error-description { color: var(--text-muted); font-size: 1rem; line-height: 1.7; margin-bottom: 2.5rem; }
        .panel {
            background: var(--panel-bg);
            border: 1px solid rgba(230, 126, 34, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(12px);
        }
        .panel-text { font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; }
        .panel-text strong { color: var(--text-main); }
        .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex;
            align-items: center; gap: 0.5rem;
            background: linear-gradient(135deg, #2a4733, #1e3428);
            border: 1px solid rgba(46, 74, 51, 0.6);
            color: var(--accent-gold);
            font-family: 'Outfit', sans-serif; font-size: 0.9rem; font-weight: 600;
            padding: 0.75rem 1.8rem; border-radius: 10px;
            text-decoration: none; cursor: pointer; transition: all 0.25s ease;
        }
        .btn-primary:hover { background: linear-gradient(135deg, #3a6143, #2a4733); transform: translateY(-1px); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: transparent;
            border: 1px solid rgba(240, 244, 248, 0.15);
            color: var(--text-muted); font-family: 'Outfit', sans-serif; font-size: 0.9rem; font-weight: 500;
            padding: 0.75rem 1.8rem; border-radius: 10px;
            text-decoration: none; cursor: pointer; transition: all 0.25s ease;
        }
        .btn-secondary:hover { border-color: rgba(240, 244, 248, 0.3); color: var(--text-main); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-badge">
            <i class="fas fa-ban"></i>
            SIAM &mdash; Acceso Bloqueado
        </div>

        <div class="error-code">403</div>
        <h1 class="error-title">Acceso Denegado</h1>
        <div class="divider"></div>

        <p class="error-description">
            No cuenta con los permisos necesarios para acceder a este recurso.
            La solicitud ha sido registrada por el sistema de seguridad.
        </p>

        <div class="panel">
            <p class="panel-text">
                <strong>Protocolo de Seguridad SIAM:</strong> Si cree que debería tener acceso a esta sección,
                contacte al administrador del sistema. Este intento de acceso ha sido registrado
                con su identificador de sesión.
            </p>
        </div>

        <div class="error-actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-primary">
                <i class="fas fa-arrow-left"></i>
                Regresar
            </a>
            <a href="{{ route('login') }}" class="btn-secondary">
                <i class="fas fa-home"></i>
                Inicio
            </a>
        </div>
    </div>
</body>
</html>
