@extends('layouts.admin')

@section('title', 'Progreso Estudiantil — Tactic Force')

@section('styles')
<style>
    /* ── KPI Cards ──────────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }
    @media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
        background: linear-gradient(135deg, rgba(10,18,14,0.9) 0%, rgba(20,36,25,0.85) 100%);
        border: 1px solid var(--border-primary);
        border-radius: 14px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(245,158,11,0.05) 0%, transparent 60%);
        pointer-events: none;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.4); }

    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .kpi-icon.gold    { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .kpi-icon.green   { background: rgba(34,197,94,0.15);  color: #22c55e; }
    .kpi-icon.blue    { background: rgba(96,165,250,0.15); color: #60a5fa; }
    .kpi-icon.purple  { background: rgba(167,139,250,0.15);color: #a78bfa; }

    .kpi-value {
        font-family: 'Share Tech Mono', monospace;
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-gold);
        line-height: 1;
    }
    .kpi-label {
        font-size: 0.78rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-top: 4px;
    }

    /* ── Student Row / Accordion ──────────────────────────── */
    .student-card {
        background: rgba(10,18,14,0.75);
        border: 1px solid var(--border-primary);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 14px;
        transition: border-color 0.2s ease;
    }
    .student-card:hover { border-color: rgba(245,158,11,0.35); }

    .student-header {
        display: grid;
        grid-template-columns: 44px 1fr 110px 110px 120px 80px 44px;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        cursor: pointer;
        user-select: none;
    }
    @media (max-width: 900px) {
        .student-header { grid-template-columns: 44px 1fr auto 44px; }
        .student-col-hide { display: none !important; }
    }

    .student-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2e4a35, #1a2e1e);
        border: 2px solid var(--border-primary);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Share Tech Mono', monospace;
        font-size: 1rem;
        font-weight: 700;
        color: var(--accent-gold);
        flex-shrink: 0;
    }

    .student-name { font-weight: 700; font-size: 0.95rem; }
    .student-meta { font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px; }

    .stat-chip {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-primary);
        border-radius: 8px;
        padding: 6px 12px;
        text-align: center;
    }
    .stat-chip-value {
        font-family: 'Share Tech Mono', monospace;
        font-size: 1.1rem;
        font-weight: 700;
        display: block;
        color: var(--accent-gold);
    }
    .stat-chip-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .accuracy-chip .stat-chip-value { color: #60a5fa; }
    .rank-chip { font-size: 0.72rem; color: var(--text-secondary); }

    .expand-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border-primary);
        background: rgba(255,255,255,0.04);
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.8rem;
    }
    .expand-btn:hover { border-color: var(--accent-gold); color: var(--accent-gold); }
    .expand-btn.open  { transform: rotate(180deg); color: var(--accent-gold); }

    /* ── Accordion Body ───────────────────────────────────── */
    .student-body {
        display: none;
        padding: 0 20px 20px;
        border-top: 1px solid var(--border-primary);
    }
    .student-body.open { display: block; animation: fadeSlideDown 0.25s ease; }

    @keyframes fadeSlideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Course Progress Bars ─────────────────────────────── */
    .course-progress-section { margin-top: 18px; }
    .course-progress-title {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .course-bar-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 10px;
    }
    .course-bar-name {
        min-width: 160px;
        max-width: 200px;
        font-size: 0.82rem;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .course-bar-track {
        flex: 1;
        height: 8px;
        background: rgba(255,255,255,0.07);
        border-radius: 50px;
        overflow: hidden;
    }
    .course-bar-fill {
        height: 100%;
        border-radius: 50px;
        background: linear-gradient(90deg, #d97706, #f59e0b);
        box-shadow: 0 0 8px rgba(245,158,11,0.35);
        transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
    }
    .course-bar-fill.complete {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        box-shadow: 0 0 8px rgba(34,197,94,0.35);
    }
    .course-bar-pct {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.78rem;
        color: var(--text-secondary);
        min-width: 40px;
        text-align: right;
    }

    /* ── Lesson History Table ─────────────────────────────── */
    .lesson-history-title {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--text-muted);
        margin: 18px 0 12px;
    }

    .lesson-history-table { width: 100%; border-collapse: collapse; }
    .lesson-history-table th {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid var(--border-primary);
    }
    .lesson-history-table td {
        padding: 10px 12px;
        font-size: 0.83rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        vertical-align: middle;
    }
    .lesson-history-table tr:last-child td { border-bottom: none; }
    .lesson-history-table tr:hover td { background: rgba(255,255,255,0.02); }

    .badge-xp {
        background: rgba(245,158,11,0.15);
        border: 1px solid rgba(245,158,11,0.3);
        color: #f59e0b;
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .badge-pct {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .badge-pct.high   { background: rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.35); color:#22c55e; }
    .badge-pct.mid    { background: rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.3); color:#f59e0b; }
    .badge-pct.low    { background: rgba(239,68,68,0.12);  border:1px solid rgba(239,68,68,0.3);  color:#ef4444; }

    /* ── Empty / Pagination ───────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 12px; opacity: 0.4; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div style="margin-bottom: 24px;">
    <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.8rem; text-transform:uppercase; color:var(--accent-gold); letter-spacing:1px;">
        <i class="fa-solid fa-chart-line" style="margin-right:10px;"></i>Progreso Estudiantil
    </h2>
    <p style="color:var(--text-secondary); font-size:0.9rem; margin-top:4px;">
        Historial de lecciones completadas, XP acumulado y precisión por quiz de cada estudiante registrado. Los datos son generados automáticamente por el sistema.
    </p>
</div>

{{-- KPI Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon gold"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="kpi-value">{{ $totalStudents }}</div>
            <div class="kpi-label">Estudiantes Activos</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="kpi-value">{{ $totalCompletions }}</div>
            <div class="kpi-label">Lecciones Completadas</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-star"></i></div>
        <div>
            <div class="kpi-value">{{ $avgXp }}</div>
            <div class="kpi-label">XP Promedio / Lección</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple"><i class="fa-solid fa-bullseye"></i></div>
        <div>
            <div class="kpi-value">{{ $avgAccuracy }}%</div>
            <div class="kpi-label">Precisión Promedio Global</div>
        </div>
    </div>
</div>

{{-- Filtro de búsqueda --}}
<div class="panel" style="margin-bottom:20px;">
    <div class="panel-body" style="padding:14px 20px;">
        <form action="{{ route('admin.evaluations.index') }}" method="GET"
              style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <div style="flex-grow:1; min-width:220px;">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-input" placeholder="Buscar por nombre, correo o cédula..."
                       style="padding:10px 15px;">
            </div>
            <button type="submit" class="btn-tactical btn-tactical-gold">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
            @if(request('search'))
                <a href="{{ route('admin.evaluations.index') }}" class="btn-tactical btn-tactical-danger">
                    <i class="fa-solid fa-xmark"></i> Limpiar
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Alertas --}}
@if(session('success'))
    <div class="alert" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Student Cards --}}
@forelse($students as $student)
    @php
        $initials = collect(explode(' ', $student->name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
        $completions = $student->lessonCompletions;
    @endphp

    <div class="student-card" id="card-{{ $student->id }}">

        {{-- Accordion Header --}}
        <div class="student-header" onclick="toggleCard({{ $student->id }})">

            {{-- Avatar --}}
            <div class="student-avatar">{{ $initials }}</div>

            {{-- Nombre y datos --}}
            <div>
                <div class="student-name">{{ $student->name }}</div>
                <div class="student-meta">
                    {{ $student->email }}
                    @if($student->cedula)
                        &nbsp;·&nbsp; C.I. {{ $student->cedula }}
                    @endif
                </div>
                <div class="rank-chip" style="margin-top:4px;">
                    <i class="fa-solid fa-shield-halved" style="color:var(--accent-gold); font-size:0.65rem;"></i>
                    {{ $student->rank }}
                </div>
            </div>

            {{-- XP Total --}}
            <div class="stat-chip student-col-hide">
                <span class="stat-chip-value">{{ number_format($student->points ?? 0) }}</span>
                <span class="stat-chip-label">XP Total</span>
            </div>

            {{-- Precisión Promedio --}}
            <div class="stat-chip accuracy-chip student-col-hide">
                <span class="stat-chip-value">{{ $student->avg_accuracy }}%</span>
                <span class="stat-chip-label">Precisión</span>
            </div>

            {{-- Lecciones completadas --}}
            <div class="stat-chip student-col-hide">
                <span class="stat-chip-value" style="color:#22c55e;">{{ $student->completions_count }}</span>
                <span class="stat-chip-label">Lecciones</span>
            </div>

            {{-- Badge de estado --}}
            <div class="student-col-hide">
                @if($student->completions_count === 0)
                    <span class="badge-status badge-status-orange" style="font-size:0.7rem;">Sin Actividad</span>
                @elseif($student->avg_accuracy >= 80)
                    <span class="badge-status badge-status-green" style="font-size:0.7rem;">Élite</span>
                @else
                    <span class="badge-status badge-status-orange" style="font-size:0.7rem;">En Progreso</span>
                @endif
            </div>

            {{-- Toggle chevron --}}
            <div class="expand-btn" id="chevron-{{ $student->id }}">
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>

        {{-- Accordion Body --}}
        <div class="student-body" id="body-{{ $student->id }}">

            @if($completions->isEmpty())
                <div class="empty-state" style="padding: 30px 0;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Este estudiante aún no ha completado ninguna lección.
                </div>
            @else

                {{-- Progreso por curso --}}
                <div class="course-progress-section">
                    <div class="course-progress-title">
                        <i class="fa-solid fa-map" style="margin-right:6px;"></i>Avance por Curso
                    </div>
                    @foreach($student->course_progress as $cp)
                        <div class="course-bar-row">
                            <div class="course-bar-name" title="{{ $cp['title'] }}">
                                {{ $cp['title'] }}
                                <span style="font-size:0.68rem; color:var(--text-muted);"> · {{ $cp['category'] }}</span>
                            </div>
                            <div class="course-bar-track">
                                <div class="course-bar-fill {{ $cp['pct'] >= 100 ? 'complete' : '' }}"
                                     style="width: {{ $cp['pct'] }}%"></div>
                            </div>
                            <div class="course-bar-pct">{{ $cp['pct'] }}%</div>
                            <div style="font-size:0.72rem; color:var(--text-muted); min-width:60px; text-align:right;">
                                {{ $cp['done'] }}/{{ $cp['total'] }} lecc.
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Historial de lecciones --}}
                <div class="lesson-history-title">
                    <i class="fa-solid fa-list-check" style="margin-right:6px;"></i>Historial de Lecciones Completadas
                </div>
                <div style="overflow-x: auto;">
                    <table class="lesson-history-table">
                        <thead>
                            <tr>
                                <th>Lección</th>
                                <th>Curso</th>
                                <th style="text-align:center;">XP Ganado</th>
                                <th style="text-align:center;">Precisión</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completions->sortByDesc('created_at') as $comp)
                                <tr>
                                    <td>
                                        <i class="fa-solid fa-book-open"
                                           style="color:var(--accent-gold); margin-right:7px; font-size:0.78rem;"></i>
                                        {{ $comp->lesson->title ?? '—' }}
                                    </td>
                                    <td style="color:var(--text-secondary); font-size:0.8rem;">
                                        {{ $comp->lesson->course->title ?? '—' }}
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge-xp">+{{ $comp->xp_earned }} XP</span>
                                    </td>
                                    <td style="text-align:center;">
                                        @php $pct = $comp->accuracy_percent; @endphp
                                        <span class="badge-pct {{ $pct >= 80 ? 'high' : ($pct >= 50 ? 'mid' : 'low') }}">
                                            {{ $pct }}%
                                        </span>
                                    </td>
                                    <td style="font-family:'Share Tech Mono',monospace; font-size:0.8rem; color:var(--text-secondary);">
                                        {{ \Carbon\Carbon::parse($comp->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
@empty
    <div class="empty-state">
        <i class="fa-solid fa-user-slash"></i>
        No se encontraron estudiantes registrados en el sistema.
    </div>
@endforelse

{{-- Paginación --}}
@if($students->hasPages())
    <div style="margin-top:20px; display:flex; justify-content:center; font-family:'Share Tech Mono',monospace;">
        {{ $students->appends(request()->query())->links() }}
    </div>
@endif

@endsection

@section('scripts')
<script>
    function toggleCard(id) {
        const body    = document.getElementById('body-' + id);
        const chevron = document.getElementById('chevron-' + id);
        const isOpen  = body.classList.contains('open');

        body.classList.toggle('open', !isOpen);
        chevron.classList.toggle('open', !isOpen);
    }
</script>
@endsection
