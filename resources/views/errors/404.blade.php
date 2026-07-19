<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurso No Encontrado — SIAM</title>

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
            --tactical-green: #2a4733;
            --tactical-green-glow: rgba(42, 71, 51, 0.4);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg-dark);
            background-image: radial-gradient(ellipse at 50% 40%, rgba(42, 71, 51, 0.08) 0%, transparent 70%);
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
            align-items: center; gap: 0.6rem;
            background: rgba(42, 71, 51, 0.2);
            border: 1px solid rgba(42, 71, 51, 0.5);
            border-radius: 999px;
            padding: 0.4rem 1.2rem;
            font-size: 0.78rem; font-weight: 600;
            color: #5a9e6f;
            letter-spacing: 0.12em; text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .error-code {
            font-size: 7rem; font-weight: 800; line-height: 1;
            color: #4a7c5f;
            text-shadow: 0 0 60px rgba(42, 71, 51, 0.6);
            font-family: 'Share Tech Mono', monospace;
            margin-bottom: 0.5rem; opacity: 0.85;
        }
        .error-title { font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem; }
        .divider {
            width: 60px; height: 2px;
            background: linear-gradient(to right, transparent, var(--accent-gold), transparent);
            margin: 1.5rem auto;
        }
        .error-description { color: var(--text-muted); font-size: 1rem; line-height: 1.7; margin-bottom: 2.5rem; }
        .radar-animation {
            display: inline-flex;
            width: 80px; height: 80px;
            border-radius: 50%;
            border: 2px solid rgba(42, 71, 51, 0.4);
            align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .radar-animation::before {
            content: '';
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(90, 158, 111, 0.5);
            animation: radar-pulse 2s ease-out infinite;
        }
        @keyframes radar-pulse {
            0% { transform: scale(1); opacity: 0.7; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        .radar-animation i { font-size: 1.8rem; color: #5a9e6f; z-index: 1; }
        .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: linear-gradient(135deg, #2a4733, #1e3428);
            border: 1px solid rgba(46, 74, 51, 0.6);
            color: var(--accent-gold); font-family: 'Outfit', sans-serif; font-size: 0.9rem; font-weight: 600;
            padding: 0.75rem 1.8rem; border-radius: 10px;
            text-decoration: none; cursor: pointer; transition: all 0.25s ease;
        }
        .btn-primary:hover { background: linear-gradient(135deg, #3a6143, #2a4733); transform: translateY(-1px); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: transparent;
            border: 1px solid rgba(240, 244, 248, 0.15);
            color: var(--text-muted); font-family: 'Outfit', sans-serif; font-size: 0.9rem;
            padding: 0.75rem 1.8rem; border-radius: 10px;
            text-decoration: none; cursor: pointer; transition: all 0.25s ease;
        }
        .btn-secondary:hover { border-color: rgba(240, 244, 248, 0.3); color: var(--text-main); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="radar-animation">
            <i class="fas fa-crosshairs"></i>
        </div>

        <div class="error-badge">
            <i class="fas fa-map-marked-alt"></i>
            SIAM &mdash; Objetivo No Localizado
        </div>

        <div class="error-code">404</div>
        <h1 class="error-title">Recurso No Encontrado</h1>
        <div class="divider"></div>

        <p class="error-description">
            El recurso solicitado no existe o ha sido movido a otra ubicación.
            Verifique la URL e intente nuevamente, o regrese al inicio.
        </p>

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
