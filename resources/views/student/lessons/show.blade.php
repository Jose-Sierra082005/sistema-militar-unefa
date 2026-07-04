@extends('layouts.admin')

@section('title', $lesson->title . ' - Tactic Force')

@section('styles')
    <style>
        .lesson-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .lesson-meta-bar {
            background: var(--panel-header);
            border: 1px solid var(--border-primary);
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.85rem;
        }

        .lesson-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lesson-meta-item i {
            color: var(--accent-gold);
        }

        .lesson-body {
            background: var(--panel-bg);
            border: 1px solid var(--border-primary);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            line-height: 1.7;
            font-size: 1.05rem;
            color: var(--text-main);
        }

        .lesson-body p {
            margin-bottom: 20px;
        }

        .lesson-body h3 {
            font-family: 'Share Tech Mono', monospace;
            color: var(--accent-gold);
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 1.3rem;
            border-bottom: 1px dashed rgba(46, 74, 53, 0.3);
            padding-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .lesson-body ul, .lesson-body ol {
            margin-left: 25px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lesson-body li::marker {
            color: var(--accent-gold);
        }

        .lesson-footer-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .lesson-body {
                padding: 24px;
                font-size: 0.95rem;
            }
            .lesson-footer-buttons {
                flex-direction: column;
                width: 100%;
            }
            .lesson-footer-buttons .btn-tactical {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="lesson-container">
        
        <!-- Cabecera de Navegación -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <a href="{{ route('student.courses.show', $lesson->course_id) }}" style="font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; color: var(--accent-gold); text-decoration: none;" class="lesson-meta-item">
                <i class="fa-solid fa-chevron-left"></i> Volver a la Ruta
            </a>
            @if($isCompleted)
                <span class="badge-status badge-status-green"><i class="fa-solid fa-circle-check"></i> Completada</span>
            @else
                <span class="badge-status badge-status-orange"><i class="fa-solid fa-clock"></i> Pendiente</span>
            @endif
        </div>

        <!-- Barra de Metadatos -->
        <div class="lesson-meta-bar">
            <div class="lesson-meta-item">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Curso: <strong>{{ $lesson->course->title }}</strong></span>
            </div>
            <div class="lesson-meta-item">
                <i class="fa-solid fa-folder"></i>
                <span>Materia: <strong>{{ $lesson->course->category }}</strong></span>
            </div>
            <div class="lesson-meta-item">
                <i class="fa-solid fa-layer-group"></i>
                <span>Nivel: <strong>{{ $lesson->course->difficulty }}</strong></span>
            </div>
        </div>

        <!-- Panel de Lectura Táctica -->
        <div class="lesson-body">
            <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 1.8rem; text-transform: uppercase; color: var(--accent-gold-hover); margin-bottom: 25px; line-height: 1.3; border-bottom: 2px solid var(--border-primary); padding-bottom: 15px; letter-spacing: 0.5px;">
                {{ $lesson->title }}
            </h1>

            <!-- Carga el contenido real interpretando saltos de línea y HTML básico -->
            {!! $lesson->content !!}
        </div>

        <!-- Botones de Acción de Pie de Página -->
        <div class="lesson-footer-buttons">
            <!-- Download manual -->
            <a href="{{ route('student.lessons.download', $lesson->id) }}" class="btn-tactical btn-tactical-gold">
                <i class="fa-solid fa-file-arrow-down"></i> Descargar Ficha Técnica (.TXT)
            </a>

            <!-- Start interactive Quiz -->
            <a href="{{ route('student.lessons.quiz', $lesson->id) }}" class="btn-tactical">
                @if($isCompleted)
                    Repasar Desafío <i class="fa-solid fa-rotate-right"></i>
                @else
                    Iniciar Desafío Táctico <i class="fa-solid fa-circle-arrow-right"></i>
                @endif
            </a>
        </div>

    </div>
@endsection
