@extends('layouts.admin')

@section('title', $course->title . ' - Sistema Militar UNEFA')

@section('styles')
    <style>
        .course-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .course-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Duolingo style path */
        .duolingo-path-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
            background: rgba(13, 17, 27, 0.6);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            position: relative;
            min-height: 400px;
        }

        .path-line {
            position: absolute;
            top: 60px;
            bottom: 60px;
            width: 4px;
            background: rgba(46, 74, 53, 0.3);
            border-radius: 2px;
            z-index: 1;
        }

        .path-node-wrapper {
            position: relative;
            z-index: 2;
            margin-bottom: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .path-node-wrapper:last-child {
            margin-bottom: 0;
        }

        /* Zig-zag offset styling */
        .node-offset-left {
            transform: translateX(-40px);
        }
        .node-offset-right {
            transform: translateX(40px);
        }

        @media (max-width: 576px) {
            .node-offset-left, .node-offset-right {
                transform: translateX(0);
            }
        }

        .path-node {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            border: 3px solid transparent;
        }

        /* Completed Node: Green */
        .node-completed {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            border-color: var(--success-green);
            color: #9fedc0;
            box-shadow: 0 0 15px rgba(46, 204, 113, 0.4), inset 0 0 10px rgba(255,255,255,0.1);
        }

        .node-completed:hover {
            transform: scale(1.1);
            box-shadow: 0 0 25px rgba(46, 204, 113, 0.6);
        }

        /* Active/Current Node: Gold with Pulse */
        .node-active {
            background: linear-gradient(135deg, #b7950b, #d4af37);
            border-color: #f3cd4a;
            color: var(--bg-dark);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5), inset 0 0 10px rgba(255,255,255,0.2);
            animation: node-glow 2s infinite alternate;
        }

        .node-active:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.8);
        }

        @keyframes node-glow {
            0% {
                box-shadow: 0 0 15px rgba(212, 175, 55, 0.4), inset 0 0 8px rgba(255,255,255,0.2);
            }
            100% {
                box-shadow: 0 0 30px rgba(212, 175, 55, 0.7), inset 0 0 15px rgba(255,255,255,0.4);
            }
        }

        /* Locked Node: Gray */
        .node-locked {
            background: #181d28;
            border-color: #2b3547;
            color: #48556e;
            cursor: not-allowed;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
        }

        .node-number-badge {
            position: absolute;
            bottom: -6px;
            right: -6px;
            background: var(--bg-dark);
            border: 1px solid var(--border-primary);
            color: var(--text-main);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-family: 'Share Tech Mono', monospace;
            font-weight: 700;
        }

        .node-completed .node-number-badge {
            border-color: var(--success-green);
            color: var(--success-green);
        }

        .node-active .node-number-badge {
            border-color: var(--accent-gold);
            color: var(--accent-gold-hover);
        }

        .node-title {
            margin-top: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-main);
            font-family: 'Share Tech Mono', monospace;
            text-transform: uppercase;
            text-align: center;
            max-width: 180px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .node-locked + .node-title {
            color: #48556e;
        }

        .node-active + .node-title {
            color: var(--accent-gold-hover);
            text-shadow: 0 0 8px rgba(212,175,55,0.2);
        }
    </style>
@endsection

@section('content')
    <!-- Cabecera del Curso -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.8rem; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px;">
                <a href="{{ route('student.dashboard') }}" style="color: var(--accent-gold); text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Volver al Portal</a>
            </div>
            <h2 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--text-main); letter-spacing: 1px;">
                {{ $course->title }}
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem; max-width: 650px; margin-top: 4px;">
                Siga la ruta de aprendizaje de arriba hacia abajo. Debe desbloquear y completar cada cuestionario para avanzar en la formación.
            </p>
        </div>
        <div class="header-status" style="background: rgba(13,17,27,0.85); border-color: var(--accent-gold);">
            <i class="fa-solid fa-star"></i> Puntos: {{ $user->points }} XP
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="course-layout">
        
        <!-- Left: Duolingo Path Map -->
        <div class="duolingo-path-container">
            <!-- Central connecting line -->
            <div class="path-line"></div>

            @forelse($lessons as $index => $lesson)
                @php
                    // Alternating zig-zag layout offsets for Duolingo feel
                    $offsetClass = '';
                    if ($index % 3 == 1) {
                        $offsetClass = 'node-offset-left';
                    } elseif ($index % 3 == 2) {
                        $offsetClass = 'node-offset-right';
                    }
                @endphp
                
                <div class="path-node-wrapper {{ $offsetClass }}">
                    @if($lesson->is_completed)
                        <!-- Completed Lesson Link -->
                        <a href="{{ route('student.lessons.show', $lesson->id) }}" class="path-node node-completed" title="Repasar lección '{{ $lesson->title }}'">
                            <i class="fa-solid fa-check"></i>
                            <span class="node-number-badge">{{ $index + 1 }}</span>
                        </a>
                    @elseif($lesson->is_unlocked)
                        <!-- Current/Active Lesson Link -->
                        <a href="{{ route('student.lessons.show', $lesson->id) }}" class="path-node node-active" title="Comenzar lección '{{ $lesson->title }}'">
                            <i class="fa-solid fa-play"></i>
                            <span class="node-number-badge">{{ $index + 1 }}</span>
                        </a>
                    @else
                        <!-- Locked Lesson Button -->
                        <div class="path-node node-locked" title="Lección bloqueada. Complete los cuestionarios previos.">
                            <i class="fa-solid fa-lock"></i>
                            <span class="node-number-badge">{{ $index + 1 }}</span>
                        </div>
                    @endif
                    
                    <div class="node-title">{{ $lesson->title }}</div>
                </div>
            @empty
                <div style="z-index: 10; color: var(--text-secondary); text-align: center; margin-top: 100px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                    <p>No hay lecciones planificadas para esta materia táctica aún.</p>
                </div>
            @endforelse
        </div>

        <!-- Right: Course Details Panel -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Información Académica</span>
                    </div>
                </div>
                <div class="panel-body" style="font-size: 0.9rem; line-height: 1.5; color: var(--text-secondary); display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <strong style="color: var(--text-main);">Sinopsis:</strong>
                        <p style="margin-top: 4px; font-size: 0.85rem;">{{ $course->description }}</p>
                    </div>
                    
                    <div style="border-top: 1px dashed rgba(46, 74, 53, 0.25); padding-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <span style="display: block; font-size: 0.75rem; font-family: 'Share Tech Mono', monospace; text-transform: uppercase;">Categoría:</span>
                            <strong style="color: var(--accent-gold-hover);">{{ $course->category }}</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 0.75rem; font-family: 'Share Tech Mono', monospace; text-transform: uppercase;">Dificultad:</span>
                            <strong style="color: var(--text-main);">{{ $course->difficulty }}</strong>
                        </div>
                    </div>

                    <div style="border-top: 1px dashed rgba(46, 74, 53, 0.25); padding-top: 15px;">
                        <strong style="color: var(--text-main);">Reglamento de Cursada:</strong>
                        <ul style="margin-left: 20px; font-size: 0.8rem; margin-top: 6px; display: flex; flex-direction: column; gap: 6px;">
                            <li>Debe cursar los temas en el orden secuencial establecido.</li>
                            <li>Completar el cuestionario otorga puntos de experiencia (XP) para subir de rango.</li>
                            <li>La descarga de materiales es libre para estudio sin conexión.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Gamification Ranking Widget -->
            <div class="panel">
                <div class="panel-header-bar">
                    <div class="panel-title">
                        <i class="fa-solid fa-award"></i>
                        <span>Medallero Académico</span>
                    </div>
                </div>
                <div class="panel-body" style="font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(7, 9, 14, 0.4); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(46, 74, 53, 0.15);">
                        <span><i class="fa-solid fa-medal" style="color: #ffd700; margin-right: 8px;"></i>General Académico</span>
                        <strong style="font-family: 'Share Tech Mono', monospace; color: var(--text-main);">500+ XP</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(7, 9, 14, 0.4); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(46, 74, 53, 0.15);">
                        <span><i class="fa-solid fa-medal" style="color: #c0c0c0; margin-right: 8px;"></i>Teniente Académico</span>
                        <strong style="font-family: 'Share Tech Mono', monospace; color: var(--text-main);">300 XP</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(7, 9, 14, 0.4); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(46, 74, 53, 0.15);">
                        <span><i class="fa-solid fa-medal" style="color: #cd7f32; margin-right: 8px;"></i>Sargento Académico</span>
                        <strong style="font-family: 'Share Tech Mono', monospace; color: var(--text-main);">150 XP</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(7, 9, 14, 0.4); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(46, 74, 53, 0.15);">
                        <span><i class="fa-solid fa-star" style="color: var(--accent-gold); margin-right: 8px;"></i>Distinguido</span>
                        <strong style="font-family: 'Share Tech Mono', monospace; color: var(--text-main);">50 XP</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
