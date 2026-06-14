<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Militar - UNEFA')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #07090e;
            --panel-bg: rgba(13, 17, 27, 0.85);
            --panel-header: rgba(18, 24, 38, 0.9);
            --border-primary: rgba(46, 74, 53, 0.4);
            --border-glow: rgba(201, 160, 84, 0.3);
            --tactical-green: #2a4733;
            --tactical-green-glow: rgba(42, 71, 51, 0.4);
            --accent-gold: #d4af37;
            --accent-gold-hover: #f3cd4a;
            --text-main: #f0f4f8;
            --text-secondary: #9aa5b1;
            --error-red: #ff4d4d;
            --success-green: #2ecc71;
            --sidebar-width: 280px;
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
                radial-gradient(circle at 10% 10%, rgba(42, 71, 51, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 90% 90%, rgba(212, 175, 55, 0.05) 0%, transparent 45%),
                linear-gradient(rgba(7, 9, 14, 0.9), rgba(7, 9, 14, 0.95));
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-main);
            overflow-x: hidden;
            display: flex;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--tactical-green);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-gold);
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.005) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.005) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 1;
        }

        /* Sidebar Styling */
        aside {
            width: var(--sidebar-width);
            background: rgba(10, 14, 23, 0.95);
            border-right: 1px solid var(--border-primary);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(46, 74, 53, 0.2);
            background: var(--panel-header);
        }

        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--accent-gold);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }

        .sidebar-brand-text {
            font-family: 'Share Tech Mono', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-main);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.25s ease;
        }

        .sidebar-item a:hover {
            color: var(--text-main);
            background: rgba(42, 71, 51, 0.2);
            border-color: rgba(42, 71, 51, 0.4);
            text-shadow: 0 0 5px rgba(240, 244, 248, 0.3);
        }

        .sidebar-item.active a {
            color: var(--accent-gold);
            background: rgba(42, 71, 51, 0.3);
            border-color: var(--accent-gold);
            font-weight: 600;
            box-shadow: inset 0 0 10px rgba(212, 175, 55, 0.05);
        }

        .sidebar-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-link-content i {
            width: 20px;
            font-size: 1.1rem;
        }

        .module-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Share Tech Mono', monospace;
        }

        .badge-active {
            background: rgba(46, 204, 113, 0.15);
            border: 1px solid var(--success-green);
            color: #9fedc0;
        }

        .badge-dev {
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid var(--accent-gold);
            color: var(--accent-gold-hover);
        }

        .badge-planned {
            background: rgba(154, 165, 177, 0.15);
            border: 1px solid var(--text-secondary);
            color: var(--text-secondary);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(46, 74, 53, 0.2);
            background: var(--panel-header);
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-align: center;
            font-family: 'Share Tech Mono', monospace;
        }

        /* Main Content Layout */
        .app-container {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /* Top Header */
        header {
            background: rgba(10, 14, 23, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-primary);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-title-area {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(42, 71, 51, 0.3);
            border: 1px solid var(--border-primary);
            padding: 4px 12px;
            border-radius: 12px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent-gold);
        }

        .header-status i {
            animation: pulse-light 1.8s infinite;
        }

        @keyframes pulse-light {
            0% { transform: scale(0.9); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.5; }
        }

        .user-menu {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 700;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--accent-gold);
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(42, 71, 51, 0.4);
            color: var(--accent-gold);
            font-size: 1.2rem;
            font-weight: 700;
            overflow: hidden;
        }

        .btn-logout {
            background: rgba(255, 77, 77, 0.15);
            border: 1px solid var(--error-red);
            border-radius: 8px;
            color: #ff9999;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: var(--error-red);
            color: white;
            box-shadow: 0 0 12px rgba(255, 77, 77, 0.4);
        }

        /* Main Workspace Content */
        main {
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 32px;
            flex-grow: 1;
        }

        /* Alert notifications */
        .alert {
            background: rgba(46, 204, 113, 0.12);
            border: 1px solid var(--success-green);
            color: #9fedc0;
            padding: 16px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: slide-in 0.4s ease;
        }

        @keyframes slide-in {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-warning {
            background: rgba(212, 175, 55, 0.12) !important;
            border: 1px solid var(--accent-gold) !important;
            color: var(--accent-gold-hover) !important;
        }

        .alert-danger {
            background: rgba(255, 77, 77, 0.12) !important;
            border: 1px solid var(--error-red) !important;
            color: #ff9999 !important;
        }

        /* Panel box styling */
        .panel {
            background: var(--panel-bg);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .panel-header-bar {
            background: var(--panel-header);
            padding: 16px 24px;
            border-bottom: 1px solid rgba(46, 74, 53, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-title {
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-body {
            padding: 24px;
        }

        /* Tactical Tables */
        .tactical-table-container {
            width: 100%;
            overflow-x: auto;
            border: 1px solid rgba(46, 74, 53, 0.25);
            border-radius: 8px;
            background: rgba(7, 9, 14, 0.4);
        }

        .tactical-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            text-align: left;
        }

        .tactical-table th {
            background: var(--panel-header);
            color: var(--accent-gold);
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 14px 18px;
            border-bottom: 2px solid var(--border-primary);
            font-size: 0.8rem;
        }

        .tactical-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(46, 74, 53, 0.15);
            color: var(--text-main);
        }

        .tactical-table tr:hover {
            background: rgba(42, 71, 51, 0.12);
        }

        .tactical-table tr:last-child td {
            border-bottom: none;
        }

        /* Forms Styling inside Panels */
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

        .form-input, .form-select {
            width: 100%;
            background: rgba(7, 9, 14, 0.95);
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            padding: 12px 15px;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.15);
            background: #090c13;
        }

        .form-select option {
            background: var(--bg-dark);
            color: var(--text-main);
        }

        /* Buttons styles */
        .btn-tactical {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--tactical-green), #1b2f21);
            border: 1px solid var(--tactical-green);
            border-radius: 8px;
            color: var(--text-main);
            padding: 10px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-tactical:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold-hover);
            box-shadow: 0 6px 15px rgba(42, 71, 51, 0.3);
        }

        .btn-tactical-gold {
            background: linear-gradient(135deg, rgba(201, 160, 84, 0.2), rgba(201, 160, 84, 0.1));
            border-color: var(--accent-gold);
            color: var(--accent-gold);
        }

        .btn-tactical-gold:hover {
            background: var(--accent-gold);
            color: var(--bg-dark);
        }

        .btn-tactical-danger {
            background: rgba(255, 77, 77, 0.12);
            border-color: var(--error-red);
            color: #ff9999;
        }

        .btn-tactical-danger:hover {
            background: var(--error-red);
            color: white;
            box-shadow: 0 6px 15px rgba(255, 77, 77, 0.25);
        }

        /* Badges status */
        .badge-status {
            display: inline-block;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Share Tech Mono', monospace;
            border: 1px solid transparent;
        }

        .badge-status-green {
            background: rgba(46, 204, 113, 0.12);
            border-color: var(--success-green);
            color: #9fedc0;
        }

        .badge-status-orange {
            background: rgba(212, 175, 55, 0.12);
            border-color: var(--accent-gold);
            color: var(--accent-gold-hover);
        }

        .badge-status-red {
            background: rgba(255, 77, 77, 0.12);
            border-color: var(--error-red);
            color: #ff9999;
        }

        .badge-status-blue {
            background: rgba(66, 133, 244, 0.12);
            border-color: #4285f4;
            color: #a1c4fd;
        }

        /* Mobile layout styling */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.4rem;
            cursor: pointer;
            margin-right: 15px;
        }

        .sidebar-close-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.4rem;
            cursor: pointer;
            margin-left: auto;
            transition: color 0.25s ease;
        }

        .sidebar-close-btn:hover {
            color: var(--accent-gold);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(4, 5, 8, 0.7);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 95;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 768px) {
            aside {
                left: -100%;
                z-index: 100;
                box-shadow: none;
            }
            aside.open {
                left: 0;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.6);
            }
            #sidebar.open ~ .sidebar-overlay {
                display: block;
            }
            .sidebar-close-btn {
                display: block;
            }
            .app-container {
                margin-left: 0;
            }
            .mobile-toggle {
                display: block;
            }
            header {
                padding: 12px 20px;
            }
            main {
                padding: 16px;
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .user-details {
                display: none;
            }
            .panel-header-bar {
                padding: 12px 16px;
            }
            .panel-body {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 10px 12px;
            }
            .header-status {
                display: none;
            }
            .btn-logout span {
                display: none;
            }
            .btn-logout {
                padding: 8px;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                justify-content: center;
                align-items: center;
                margin-left: 5px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Mobile Drawer Overlay -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-shield-halved"></i>
            <span class="sidebar-brand-text">UNEFA MILITAR</span>
            <button class="sidebar-close-btn" onclick="toggleSidebar()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Panel Principal</span>
                    </div>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.personnel.*') ? 'active' : '' }}">
                <a href="{{ route('admin.personnel.index') }}">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Personal Militar</span>
                    </div>
                    <span class="module-badge badge-active">Activo</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.armory.*') ? 'active' : '' }}">
                <a href="{{ route('admin.armory.index') }}">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-gun"></i>
                        <span>Parque de Armas</span>
                    </div>
                    <span class="module-badge badge-active">Activo</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.guards.*') ? 'active' : '' }}">
                <a href="{{ route('admin.guards.index') }}">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Guardias y Roles</span>
                    </div>
                    <span class="module-badge badge-active">Activo</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.evaluations.*') ? 'active' : '' }}">
                <a href="{{ route('admin.evaluations.index') }}">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Evaluaciones</span>
                    </div>
                    <span class="module-badge badge-active">Activo</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#">
                    <div class="sidebar-link-content">
                        <i class="fa-solid fa-key"></i>
                        <span>Comunicaciones</span>
                    </div>
                    <span class="module-badge badge-planned">Plan</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div>SISTEMA MILITAR v2.1</div>
            <div style="font-size: 0.7rem; color: var(--accent-gold); margin-top: 4px;">CONEXIÓN SEGURA</div>
        </div>
    </aside>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="app-container">
        <!-- Top Nav Header -->
        <header>
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="header-title-area">
                <div class="header-status">
                    <i class="fa-solid fa-circle-dot"></i> SISTEMA OPERACIONAL - DEFCON 5
                </div>
            </div>

            <div class="user-menu">
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">
                        @if(auth()->user()->google_id)
                            <i class="fa-brands fa-google" style="margin-right: 4px; font-size: 0.75rem;"></i>Google Auth
                        @else
                            Oficial de Turno
                        @endif
                    </div>
                </div>
                <div class="user-avatar" title="{{ auth()->user()->email }}">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" style="margin-left: 10px;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-power-off"></i> <span>Salir</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Workspace content -->
        <main>
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
    @yield('scripts')
</body>
</html>
