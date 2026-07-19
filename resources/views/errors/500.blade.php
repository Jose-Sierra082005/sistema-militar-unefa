<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema — SIAM</title>

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
            --accent-gold-hover: #f3cd4a;
            --text-main: #f0f4f8;
            --text-muted: rgba(180, 194, 200, 0.7);
            --danger-red: #c0392b;
            --danger-red-glow: rgba(192, 57, 43, 0.3);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg-dark);
            background-image:
                radial-gradient(ellipse at 20% 40%, rgba(192, 57, 43, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 60%, rgba(42, 71, 51, 0.08) 0%, transparent 60%);
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-container {
            max-width: 640px;
            width: 100%;
            text-align: center;
        }

        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(192, 57, 43, 0.12);
            border: 1px solid rgba(192, 57, 43, 0.4);
            border-radius: 999px;
            padding: 0.4rem 1.2rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #e74c3c;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        .error-code {
            font-size: 7rem;
            font-weight: 800;
            line-height: 1;
            color: var(--danger-red);
            text-shadow: 0 0 60px var(--danger-red-glow);
            font-family: 'Share Tech Mono', monospace;
            margin-bottom: 0.5rem;
            opacity: 0.85;
        }

        .error-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1rem;
        }

        .error-description {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .correlation-panel {
            background: var(--panel-bg);
            border: 1px solid var(--border-glow);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .correlation-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--accent-gold);
            margin-bottom: 0.75rem;
        }

        .correlation-id {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.92rem;
            color: var(--text-main);
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            word-break: break-all;
            letter-spacing: 0.04em;
            user-select: all;
            cursor: text;
        }

        .correlation-hint {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.75rem;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            background: transparent;
            border: 1px solid var(--border-glow);
            color: var(--accent-gold);
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.45rem 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--accent-gold);
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #2a4733, #1e3428);
            border: 1px solid rgba(46, 74, 51, 0.6);
            color: var(--accent-gold);
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.75rem 1.8rem;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
            letter-spacing: 0.03em;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3a6143, #2a4733);
            box-shadow: 0 0 20px rgba(42, 71, 51, 0.5);
            transform: translateY(-1px);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            border: 1px solid rgba(240, 244, 248, 0.15);
            color: var(--text-muted);
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.75rem 1.8rem;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-secondary:hover {
            border-color: rgba(240, 244, 248, 0.3);
            color: var(--text-main);
        }

        .divider {
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--accent-gold), transparent);
            margin: 1.5rem auto;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-badge">
            <i class="fas fa-shield-exclamation"></i>
            SIAM &mdash; Protocolo de Seguridad Activado
        </div>

        <div class="error-code">500</div>
        <h1 class="error-title">Error Interno del Sistema</h1>

        <div class="divider"></div>

        <p class="error-description">
            El sistema ha detectado una condición de error y ha activado el protocolo de seguridad.
            <strong>Ningún dato ha sido comprometido.</strong> El incidente ha sido registrado
            automáticamente y notificado al equipo de soporte.
        </p>

        @if(!empty($correlationId) && $correlationId !== 'N/A')
        <div class="correlation-panel">
            <div class="correlation-label">
                <i class="fas fa-fingerprint" style="margin-right: 0.4rem;"></i>
                Código de Incidente (Correlation ID)
            </div>
            <div class="correlation-id" id="correlationCode">{{ $correlationId }}</div>
            <p class="correlation-hint">
                Proporcione este código al equipo de soporte para rastrear el incidente en los registros del sistema.
            </p>
            <button class="copy-btn" onclick="copyCorrelationId()">
                <i class="fas fa-copy"></i>
                Copiar Código
            </button>
        </div>
        @endif

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

    <script>
        function copyCorrelationId() {
            const code = document.getElementById('correlationCode')?.innerText;
            if (!code) return;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.querySelector('.copy-btn');
                btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
                btn.style.color = '#27ae60';
                btn.style.borderColor = '#27ae60';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i> Copiar Código';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 2500);
            });
        }
    </script>
</body>
</html>
