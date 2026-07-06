@extends('layouts.admin')

@section('title', 'Panel de Control - Tactic Force')

@section('styles')
    <style>
        /* Welcome Banner Card */
        .welcome-card {
            background: linear-gradient(135deg, rgba(13, 20, 32, 0.9) 0%, rgba(8, 11, 19, 0.95) 100%);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
        }

        .welcome-card-content h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, var(--text-main), var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-card-content p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            max-width: 500px;
            line-height: 1.5;
        }

        .welcome-card-coordinates {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.8rem;
            color: var(--accent-gold);
            text-align: right;
            border-left: 2px solid var(--accent-gold);
            padding-left: 15px;
        }

        /* Grid of KPIs */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }

        .kpi-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-primary);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .kpi-card:hover {
            border-color: var(--border-glow);
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.05);
        }

        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Share Tech Mono', monospace;
        }

        .kpi-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(42, 71, 51, 0.3);
            color: var(--accent-gold);
            font-size: 1.1rem;
            border: 1px solid rgba(42, 71, 51, 0.6);
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .kpi-trend {
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }

        .trend-up { color: var(--success-green); }
        .trend-neutral { color: var(--accent-gold); }
        .trend-down { color: var(--error-red); }

        /* Complex Grid Layout for Charts and Panels */
        .workspace-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        @media (max-width: 1024px) {
            .workspace-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modules Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .module-card {
            background: rgba(7, 9, 14, 0.6);
            border: 1px solid rgba(46, 74, 53, 0.25);
            border-radius: 10px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            transition: all 0.25s ease;
        }

        .module-card:hover {
            background: rgba(13, 17, 27, 0.5);
            border-color: var(--border-primary);
            box-shadow: inset 0 0 10px rgba(42, 71, 51, 0.1);
        }

        .module-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .module-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .module-desc {
            font-size: 0.82rem;
            color: var(--text-secondary);
            line-height: 1.45;
            min-height: 52px;
        }

        .module-action {
            align-self: flex-start;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--accent-gold);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            transition: color 0.2s ease;
        }

        .module-action:hover {
            color: var(--accent-gold-hover);
        }

        /* Radar Widget */
        .radar-widget-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 20px 0;
        }

        .radar-screen {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 2px solid var(--border-primary);
            background: radial-gradient(circle, rgba(42, 71, 51, 0.1) 0%, rgba(7, 9, 14, 0.95) 100%);
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(42, 71, 51, 0.3);
        }

        .radar-grid-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 1px dashed rgba(42, 71, 51, 0.4);
            border-radius: 50%;
        }

        .c1 { width: 140px; height: 140px; }
        .c2 { width: 90px; height: 90px; }
        .c3 { width: 40px; height: 40px; }

        .radar-crosshair-h {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: rgba(42, 71, 51, 0.25);
        }

        .radar-crosshair-v {
            position: absolute;
            left: 50%;
            top: 0;
            width: 1px;
            height: 100%;
            background: rgba(42, 71, 51, 0.25);
        }

        .radar-sweep {
            position: absolute;
            width: 100%;
            height: 100%;
            background: conic-gradient(from 0deg, transparent 40%, rgba(42, 71, 51, 0.5) 100%);
            animation: radar-rotate 5s linear infinite;
            transform-origin: center;
        }

        .radar-blip {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #2ecc71;
            border-radius: 50%;
            box-shadow: 0 0 8px #2ecc71;
            animation: pulse-blip 2s infinite;
        }

        .b1 { top: 40px; left: 110px; }
        .b2 { top: 120px; left: 55px; animation-delay: 0.8s; }

        @keyframes radar-rotate {
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse-blip {
            0% { opacity: 0.2; }
            50% { opacity: 1; }
            100% { opacity: 0.2; }
        }

        /* Progress bars */
        .progress-indicator {
            width: 100%;
            margin-bottom: 8px;
        }

        .progress-label-bar {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: 6px;
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
        }

        .progress-track {
            width: 100%;
            height: 6px;
            background: rgba(7, 9, 14, 0.8);
            border-radius: 3px;
            overflow: hidden;
            border: 1px solid rgba(46, 74, 53, 0.2);
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        .fill-gold {
            background: linear-gradient(90deg, #d4af37, #f3cd4a);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.4);
        }

        .fill-green {
            background: linear-gradient(90deg, #2ecc71, #27ae60);
            box-shadow: 0 0 8px rgba(46, 204, 113, 0.4);
        }

        /* Activity log */
        .activity-log {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            font-size: 0.82rem;
            border-bottom: 1px dashed rgba(46, 74, 53, 0.15);
            padding-bottom: 12px;
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-time {
            font-family: 'Share Tech Mono', monospace;
            color: var(--accent-gold);
            min-width: 65px;
        }

        .activity-icon {
            color: var(--border-primary);
            display: flex;
            align-items: center;
        }

        .activity-text {
            color: var(--text-main);
        }

        .activity-text strong {
            color: var(--accent-gold-hover);
        }

        @media (max-width: 768px) {
            .welcome-card {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
                padding: 20px;
            }
            .welcome-card-coordinates {
                border-left: none;
                border-top: 1px solid var(--accent-gold);
                padding-left: 0;
                padding-top: 10px;
                width: 100%;
                text-align: left;
            }
            .workspace-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .welcome-card-content h1 {
                font-size: 1.4rem;
            }
            .welcome-card-content p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .kpi-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .modules-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .kpi-card {
                padding: 15px;
            }
            #seccion-seguridad .panel-body > div {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
            #seccion-seguridad img {
                width: 100px;
                height: 100px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Alerta de Seguridad por perfil incompleto -->
    @if(empty(auth()->user()->cedula) || !auth()->user()->two_factor_enabled)
        <div class="alert alert-warning" style="margin-bottom: 20px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 1.4rem;"></i>
            <div>
                <strong>ALERTA DE SEGURIDAD GENERAL:</strong> Su perfil se encuentra incompleto o sin doble factor de autenticación activo. 
                Para cumplir el protocolo militar, complete su <a href="#seccion-seguridad" style="color: var(--accent-gold-hover); font-weight: 700; text-decoration: underline;">configuración de seguridad</a> obligatoria al final de la página.
            </div>
        </div>
    @endif

    <!-- Welcome Tactical Header -->
    <div class="welcome-card">
        <div class="welcome-card-content">
            <h1>Bienvenido, {{ auth()->user()->name }}</h1>
            <p>Acceso táctico concedido al Portal de Formación y Evaluaciones Académicas Militares de la UNEFA Falcón. Use la barra lateral para navegar a través de los módulos activos y autorizados.</p>
        </div>
        <div class="welcome-card-coordinates">
            <div>LAT: 11.4167° N</div>
            <div>LONG: 69.6667° W</div>
            <div style="margin-top: 4px; color: var(--text-secondary);">CORO - FALCÓN</div>
        </div>
    </div>

    <!-- Dashboard Stats Tiles (KPIs) -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-header">
                <span>Estudiantes</span>
                <div class="kpi-icon"><i class="fa-solid fa-user-graduate"></i></div>
            </div>
            <div class="kpi-value">{{ $studentCount }}</div>
            <div class="kpi-trend trend-neutral">
                <i class="fa-solid fa-users"></i> <span>Registrados en el portal</span>
            </div>
        </div>
    </div>

    <!-- Two Column Section -->
    <div class="workspace-grid">
        
        <!-- Column 1: Main Modules Area -->
        <div class="panel">
            <div class="panel-header-bar">
                <div class="panel-title">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Módulos Académicos de Adiestramiento</span>
                </div>
            </div>
            <div class="panel-body">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
                    Seleccione el área académica del sistema a gestionar. Los módulos activos forman parte del pensum de capacitación táctica.
                </p>
                
                <div class="modules-grid">
                    <!-- Module 1 -->
                    <div class="module-card">
                        <div class="module-header">
                            <span class="module-title">Fichero Académico</span>
                            <span class="module-badge badge-active">Activo</span>
                        </div>
                        <p class="module-desc">Gestión integral del personal de instructores, oficiales y estudiantes militares adscritos a la academia.</p>
                        <a href="{{ route('admin.personnel.index') }}" class="module-action">Ingresar <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <!-- Module 2 -->
                    <div class="module-card">
                        <div class="module-header">
                            <span class="module-title">Cursos y Temarios LMS</span>
                            <span class="module-badge badge-active">Activo</span>
                        </div>
                        <p class="module-desc">Gestión de cursos formativos, redacción de lecciones de estrategia y adiestramiento táctico para los cadetes.</p>
                        <a href="{{ route('admin.courses.index') }}" class="module-action">Ingresar <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <!-- Module 3 -->
                    <div class="module-card">
                        <div class="module-header">
                            <span class="module-title">Evaluaciones y Notas</span>
                            <span class="module-badge badge-active">Activo</span>
                        </div>
                        <p class="module-desc">Carga y consulta de calificaciones de adiestramiento, aptitud y evaluaciones teóricas vinculadas a cursos.</p>
                        <a href="{{ route('admin.evaluations.index') }}" class="module-action">Ingresar <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Tactical Widget & Diagnostic Log -->
        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <!-- Resumen Académico -->
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Resumen Académico</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="progress-indicator">
                        <div class="progress-label-bar">
                            <span>Cursos Activos</span>
                            <span>{{ $courseCount }}</span>
                        </div>
                    </div>

                    <div class="progress-indicator">
                        <div class="progress-label-bar">
                            <span>Lecciones en Temarios</span>
                            <span>{{ $lessonCount }}</span>
                        </div>
                    </div>

                    <div class="progress-indicator">
                        <div class="progress-label-bar">
                            <span>Estudiantes Registrados</span>
                            <span>{{ $studentCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bitácora Log -->
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Bitácora de Tactic Force</span>
                    </div>
                </div>
                <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
                    <ul class="activity-log">
                        <li class="activity-item">
                            <div class="activity-time">{{ now()->format('H:i:s') }}</div>
                            <div class="activity-icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
                            <div class="activity-text">Usuario <strong>{{ auth()->user()->name }}</strong> inició sesión con éxito.</div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-time">{{ now()->subMinutes(2)->format('H:i:s') }}</div>
                            <div class="activity-icon"><i class="fa-solid fa-circle-check"></i></div>
                            <div class="activity-text">Sistema <strong>Tactic Force</strong> operacional.</div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-time">{{ now()->subMinutes(15)->format('H:i:s') }}</div>
                            <div class="activity-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div class="activity-text"><strong>{{ $courseCount }}</strong> cursos y <strong>{{ $lessonCount }}</strong> lecciones disponibles en el LMS.</div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
@endsection
