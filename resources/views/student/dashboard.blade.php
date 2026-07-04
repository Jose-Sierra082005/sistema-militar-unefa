@extends('layouts.admin')

@section('title', 'Portal del Estudiante - Sistema Militar UNEFA')

@section('styles')
    <style>
        .student-hero {
            background: linear-gradient(135deg, rgba(13, 20, 32, 0.95) 0%, rgba(8, 11, 19, 0.98) 100%);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            flex-wrap: wrap;
            gap: 25px;
        }

        .student-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .student-stat-card {
            background: rgba(7, 9, 14, 0.6);
            border: 1px solid rgba(42, 71, 51, 0.4);
            border-radius: 12px;
            padding: 15px 25px;
            text-align: center;
            min-width: 140px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .student-stat-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .student-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent-gold);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }

        .student-stat-value-xp {
            color: var(--success-green);
            text-shadow: 0 0 10px rgba(46, 204, 113, 0.2);
        }

        .courses-grid-student {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .course-card-student {
            background: var(--panel-bg);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .course-card-student:hover {
            border-color: var(--border-glow);
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.08);
        }

        .course-header-student {
            background: var(--panel-header);
            padding: 20px;
            border-bottom: 1px solid rgba(46, 74, 53, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .course-title-student {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .course-category-student {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent-gold);
            text-transform: uppercase;
        }

        .course-body-student {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex-grow: 1;
        }

        .course-description-student {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.45;
            min-height: 58px;
        }

        .progress-bar-container-student {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .progress-text-student {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            font-family: 'Share Tech Mono', monospace;
            color: var(--text-secondary);
        }

        .progress-bar-track-student {
            height: 8px;
            background: rgba(7, 9, 14, 0.8);
            border: 1px solid rgba(46, 74, 53, 0.2);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill-student {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-gold), #f3cd4a);
            border-radius: 4px;
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.4);
            transition: width 0.5s ease;
        }

        .course-footer-student {
            padding: 20px;
            border-top: 1px solid rgba(46, 74, 53, 0.15);
            background: rgba(10, 14, 23, 0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quick-modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }

        .quick-module-card {
            background: var(--panel-bg);
            border: 1px solid rgba(46, 74, 53, 0.25);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .quick-module-card:hover {
            border-color: var(--accent-gold);
            background: rgba(42, 71, 51, 0.12);
        }

        .quick-module-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(42, 71, 51, 0.3);
            border: 1px solid var(--border-primary);
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .quick-module-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .quick-module-desc {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }
    </style>
@endsection

@section('content')
    <!-- Banner de Bienvenida -->
    <div class="student-hero">
        <div style="flex-grow: 1;">
            <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.8rem; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px;">
                <i class="fa-solid fa-graduation-cap"></i> Academia Militar UNEFA Falcón
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; background: linear-gradient(135deg, var(--text-main), var(--accent-gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Portal del Estudiante
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.95rem; max-width: 550px; line-height: 1.5;">
                Bienvenido, cadete <strong>{{ $user->name }}</strong>. Acceda a sus materias tácticas, complete lecciones teóricas y apruebe los cuestionarios estilo Duolingo para ascender en rango y acumular XP.
            </p>
        </div>
        
        <!-- Estadísticas Rápidas HUD -->
        <div class="student-stats">
            <div class="student-stat-card">
                <span class="student-stat-label">Experiencia</span>
                <span class="student-stat-value student-stat-value-xp">{{ $user->points }} XP</span>
            </div>
            
            <div class="student-stat-card">
                <span class="student-stat-label">Rango Académico</span>
                @php
                    $rank = 'Cadete';
                    if ($user->points >= 500) $rank = 'General Académico';
                    elseif ($user->points >= 300) $rank = 'Teniente Académico';
                    elseif ($user->points >= 150) $rank = 'Sargento Académico';
                    elseif ($user->points >= 50) $rank = 'Distinguido';
                @endphp
                <span class="student-stat-value" style="font-size: 1rem; font-family: 'Share Tech Mono', monospace; margin-top: 6px;">
                    <i class="fa-solid fa-medal" style="margin-right: 4px;"></i>{{ $rank }}
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert" style="margin-top: 25px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-warning" style="margin-top: 25px;">
            <i class="fa-solid fa-circle-info" style="font-size: 1.2rem;"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Cursos Disponibles -->
    <div style="margin-top: 35px;">
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.3rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-map-location-dot"></i> Ruta de Adiestramiento Activa
        </h2>
        
        <div class="courses-grid-student">
            @forelse($courses as $course)
                <div class="course-card-student">
                    <div class="course-header-student">
                        <div>
                            <span class="course-category-student">{{ $course->category }}</span>
                            <h3 class="course-title-student">{{ $course->title }}</h3>
                        </div>
                        <span class="badge-status {{ strtolower($course->difficulty) == 'básico' ? 'badge-status-green' : (strtolower($course->difficulty) == 'intermedio' ? 'badge-status-orange' : 'badge-status-red') }}">
                            {{ $course->difficulty }}
                        </span>
                    </div>
                    
                    <div class="course-body-student">
                        <p class="course-description-student">{{ $course->description }}</p>
                        
                        <div class="progress-bar-container-student">
                            <div class="progress-text-student">
                                <span>Lecciones Completadas</span>
                                <span>{{ $course->completed_count }} / {{ count($course->lessons) }}</span>
                            </div>
                            <div class="progress-bar-track-student">
                                <div class="progress-bar-fill-student" style="width: {{ $course->progress_percent }}%;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="course-footer-student">
                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-family: 'Share Tech Mono', monospace;">
                            <i class="fa-solid fa-book-open"></i> {{ count($course->lessons) }} Temas
                        </span>
                        @if(count($course->lessons) > 0)
                            <a href="{{ route('student.courses.show', $course->id) }}" class="btn-tactical" style="padding: 8px 16px; font-size: 0.8rem;">
                                Iniciar Ruta <i class="fa-solid fa-circle-arrow-right"></i>
                            </a>
                        @else
                            <button class="btn-tactical btn-tactical-danger" style="padding: 8px 16px; font-size: 0.8rem; opacity: 0.5; cursor: not-allowed;" disabled>
                                Sin Contenido
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="panel" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--text-secondary);">
                    <i class="fa-solid fa-circle-info" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 10px;"></i>
                    <p>No se encuentran cursos de adiestramiento activos en el sistema actualmente.</p>
                </div>
            @endforelse
        </div>
    </div>


    <!-- Acceso Rápido: Configuración de Perfil -->
    <div style="margin-top: 40px;">
        <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.3rem; text-transform: uppercase; color: var(--accent-gold); letter-spacing: 1px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-sliders"></i> Ajustes del Sistema
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 20px;">
            Gestione su información de identificación, credenciales de acceso y parámetros de seguridad de su cuenta institucional.
        </p>

        <div class="quick-modules">
            <a href="{{ route('student.profile.show') }}" class="quick-module-card" style="max-width: 380px;">
                <div class="quick-module-icon" style="background: linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05)); border-color: rgba(212,175,55,0.3); color: var(--accent-gold);">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <div>
                    <div class="quick-module-title">Configuración de Perfil</div>
                    <div class="quick-module-desc">Actualice sus datos personales, correo y contraseña de acceso.</div>
                </div>
            </a>
        </div>
    </div>
@endsection

