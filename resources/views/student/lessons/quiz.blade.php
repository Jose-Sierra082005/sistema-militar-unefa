@extends('layouts.admin')

@section('title', 'Desafío Táctico: ' . $lesson->title . ' - Sistema Militar UNEFA')

@section('styles')
<style>
    :root {
        --quiz-correct: #22c55e;
        --quiz-wrong: #ef4444;
        --quiz-accent: #f59e0b;
        --quiz-panel: rgba(10, 18, 14, 0.85);
    }

    .quiz-arena {
        max-width: 820px;
        margin: 0 auto;
        min-height: calc(100vh - 180px);
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    /* ── Header HUD ───────────────────────────── */
    .quiz-hud {
        background: var(--quiz-panel);
        border: 1px solid var(--border-primary);
        border-radius: 16px 16px 0 0;
        padding: 18px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        backdrop-filter: blur(12px);
        border-bottom: none;
    }

    .quiz-title {
        font-family: 'Share Tech Mono', monospace;
        font-size: 1rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .quiz-title strong {
        color: var(--accent-gold);
        display: block;
        font-size: 1.2rem;
    }

    .quiz-hud-stats {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .stat-pill {
        background: rgba(46, 74, 53, 0.3);
        border: 1px solid var(--border-primary);
        border-radius: 50px;
        padding: 6px 16px;
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-pill i { color: var(--accent-gold); }

    /* ── Progress bar ─────────────────────────── */
    .quiz-progress-track {
        background: var(--quiz-panel);
        border-left: 1px solid var(--border-primary);
        border-right: 1px solid var(--border-primary);
        padding: 0 28px 16px;
        backdrop-filter: blur(12px);
    }

    .progress-bar-container {
        background: rgba(255,255,255,0.06);
        border-radius: 50px;
        height: 8px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-gold), #f97316);
        border-radius: 50px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.5);
    }

    .progress-label {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
    }

    /* ── Question Card ────────────────────────── */
    .quiz-card {
        background: var(--quiz-panel);
        border: 1px solid var(--border-primary);
        border-radius: 0 0 16px 16px;
        padding: 36px 36px 32px;
        backdrop-filter: blur(12px);
        flex: 1;
    }

    .question-number {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.78rem;
        color: var(--accent-gold);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 12px;
    }

    .question-text {
        font-size: 1.2rem;
        color: var(--text-main);
        line-height: 1.6;
        margin-bottom: 32px;
        font-weight: 500;
    }

    /* ── Options Grid ─────────────────────────── */
    .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 28px;
    }

    @media (max-width: 600px) {
        .options-grid { grid-template-columns: 1fr; }
        .quiz-hud { border-radius: 12px 12px 0 0; }
        .quiz-card { padding: 24px 20px; }
        .question-text { font-size: 1.05rem; }
    }

    .option-btn {
        background: rgba(255,255,255,0.04);
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 18px 20px;
        color: var(--text-main);
        font-size: 0.97rem;
        line-height: 1.4;
        cursor: pointer;
        text-align: left;
        transition: all 0.25s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .option-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(245,158,11,0.06) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.25s;
    }

    .option-btn:hover:not(:disabled)::before { opacity: 1; }

    .option-btn:hover:not(:disabled) {
        border-color: var(--accent-gold);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.35);
    }

    .option-btn:disabled { cursor: not-allowed; }

    .option-letter {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--accent-gold);
        min-width: 22px;
        margin-top: 1px;
    }

    /* States */
    .option-btn.correct {
        border-color: var(--quiz-correct) !important;
        background: rgba(34, 197, 94, 0.12) !important;
        color: var(--quiz-correct);
        animation: pulseGreen 0.5s ease;
    }

    .option-btn.wrong {
        border-color: var(--quiz-wrong) !important;
        background: rgba(239, 68, 68, 0.12) !important;
        color: var(--quiz-wrong);
        animation: shakeWrong 0.4s ease;
    }

    .option-btn.reveal-correct {
        border-color: var(--quiz-correct) !important;
        background: rgba(34, 197, 94, 0.08) !important;
    }

    @keyframes pulseGreen {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
        50% { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); }
        100% { box-shadow: none; }
    }

    @keyframes shakeWrong {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-5px); }
        80% { transform: translateX(5px); }
    }

    /* ── Feedback Banner ──────────────────────── */
    .feedback-banner {
        border-radius: 10px;
        padding: 14px 20px;
        margin-bottom: 24px;
        display: none;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        animation: fadeInDown 0.35s ease;
    }

    .feedback-banner.show { display: flex; }

    .feedback-banner.correct-fb {
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.4);
        color: var(--quiz-correct);
    }

    .feedback-banner.wrong-fb {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.4);
        color: var(--quiz-wrong);
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Action Button ────────────────────────── */
    .quiz-action-btn {
        width: 100%;
        padding: 16px 24px;
        border-radius: 12px;
        font-family: 'Share Tech Mono', monospace;
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.25s ease;
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #0a120e;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
    }

    .quiz-action-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.5);
        filter: brightness(1.1);
    }

    .quiz-action-btn:disabled {
        background: rgba(255,255,255,0.08);
        color: var(--text-muted);
        cursor: not-allowed;
        box-shadow: none;
    }

    /* ── Results Screen ───────────────────────── */
    #results-screen {
        display: none;
        text-align: center;
        padding: 24px 0;
        animation: fadeInDown 0.5s ease;
    }

    .results-badge {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid var(--accent-gold);
        background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        margin: 0 auto 24px;
        animation: badgePop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s both;
    }

    @keyframes badgePop {
        from { transform: scale(0.3); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .results-title {
        font-family: 'Share Tech Mono', monospace;
        font-size: 1.8rem;
        text-transform: uppercase;
        color: var(--accent-gold);
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .results-subtitle {
        color: var(--text-muted);
        font-size: 1rem;
        margin-bottom: 28px;
    }

    .results-stats {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-bottom: 32px;
        flex-wrap: wrap;
    }

    .result-stat-box {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-primary);
        border-radius: 14px;
        padding: 20px 28px;
        min-width: 130px;
    }

    .result-stat-value {
        font-family: 'Share Tech Mono', monospace;
        font-size: 2rem;
        color: var(--accent-gold);
        display: block;
        line-height: 1;
    }

    .result-stat-label {
        font-size: 0.78rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 6px;
        display: block;
    }

    /* XP floating particles */
    .xp-particle {
        position: fixed;
        font-family: 'Share Tech Mono', monospace;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--accent-gold);
        pointer-events: none;
        z-index: 9999;
        animation: floatUp 1.2s ease forwards;
    }

    @keyframes floatUp {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-80px) scale(1.3); }
    }
</style>
@endsection

@section('content')
<div class="quiz-arena">

    {{-- HUD Header --}}
    <div class="quiz-hud">
        <div class="quiz-title">
            Desafío Táctico
            <strong>{{ $lesson->title }}</strong>
        </div>
        <div class="quiz-hud-stats">
            <div class="stat-pill">
                <i class="fa-solid fa-star"></i>
                <span id="hud-xp">0</span> XP
            </div>
            <div class="stat-pill">
                <i class="fa-solid fa-circle-check"></i>
                <span id="hud-correct">0</span> / <span id="hud-total">{{ count($formattedQuestions) }}</span>
            </div>
            <div class="stat-pill">
                <i class="fa-solid fa-bolt"></i>
                Racha: <span id="hud-streak">0</span>
            </div>
        </div>
    </div>

    {{-- Progress Track --}}
    <div class="quiz-progress-track">
        <div class="progress-label">
            <span id="progress-text">Pregunta 1 de {{ count($formattedQuestions) }}</span>
            <span id="progress-pct">0%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progress-bar" style="width: 0%"></div>
        </div>
    </div>

    {{-- Main Quiz Card --}}
    <div class="quiz-card" id="quiz-container">

        {{-- Question Panel --}}
        <div id="question-panel">
            <div class="question-number" id="q-number">Pregunta 1</div>
            <div class="question-text" id="q-text">Cargando...</div>

            <div class="options-grid" id="options-grid">
                {{-- Generated by JS --}}
            </div>

            {{-- Feedback Banner --}}
            <div class="feedback-banner" id="feedback-banner">
                <i class="fa-solid fa-circle-check" id="feedback-icon"></i>
                <span id="feedback-text"></span>
            </div>

            {{-- Next button --}}
            <button class="quiz-action-btn" id="next-btn" disabled onclick="nextQuestion()">
                Selecciona una respuesta
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>

        {{-- Results Screen --}}
        <div id="results-screen">
            <div class="results-badge" id="results-emoji">🏆</div>
            <div class="results-title" id="results-title">¡Misión Cumplida!</div>
            <div class="results-subtitle" id="results-subtitle">Has completado el desafío táctico</div>

            <div class="results-stats">
                <div class="result-stat-box">
                    <span class="result-stat-value" id="res-xp">0</span>
                    <span class="result-stat-label">XP Ganados</span>
                </div>
                <div class="result-stat-box">
                    <span class="result-stat-value" id="res-correct">0</span>
                    <span class="result-stat-label">Correctas</span>
                </div>
                <div class="result-stat-box">
                    <span class="result-stat-value" id="res-accuracy">0%</span>
                    <span class="result-stat-label">Precisión</span>
                </div>
            </div>

            {{-- Hidden form to POST completion --}}
            <form id="complete-form" method="POST"
                action="{{ route('student.lessons.complete_quiz', $lesson->id) }}">
                @csrf
                <input type="hidden" name="points_earned" id="points-earned-input" value="0">
                <button type="submit" class="quiz-action-btn" style="max-width: 420px; margin: 0 auto;">
                    <i class="fa-solid fa-flag-checkered"></i>
                    Registrar Progreso y Volver al Mapa
                </button>
            </form>

            <div style="margin-top: 16px;">
                <a href="{{ route('student.lessons.show', $lesson->id) }}"
                   style="font-family:'Share Tech Mono',monospace; font-size:0.85rem; color:var(--text-muted); text-decoration:none;">
                    <i class="fa-solid fa-rotate-left"></i> Repasar Lección
                </a>
            </div>
        </div>

    </div>{{-- /quiz-card --}}
</div>{{-- /quiz-arena --}}

{{-- Embedded question data --}}
<script>
    const QUESTIONS = @json($formattedQuestions);
    const LETTERS   = ['A', 'B', 'C', 'D', 'E', 'F'];

    let currentIndex  = 0;
    let totalXP       = 0;
    let totalCorrect  = 0;
    let streak        = 0;
    let answered      = false;

    /* ─── Build & Render Question ─────────────────────── */
    function renderQuestion(index) {
        answered = false;
        const q   = QUESTIONS[index];
        const pct = Math.round((index / QUESTIONS.length) * 100);

        document.getElementById('q-number').textContent  = `Pregunta ${index + 1} de ${QUESTIONS.length}`;
        document.getElementById('q-text').textContent    = q.question_text;
        document.getElementById('progress-text').textContent = `Pregunta ${index + 1} de ${QUESTIONS.length}`;
        document.getElementById('progress-pct').textContent  = pct + '%';
        document.getElementById('progress-bar').style.width  = pct + '%';

        // Options
        const grid = document.getElementById('options-grid');
        grid.innerHTML = '';
        q.options.forEach((opt, i) => {
            const btn = document.createElement('button');
            btn.className   = 'option-btn';
            btn.dataset.correct = opt.is_correct ? '1' : '0';
            btn.dataset.id      = opt.id;
            btn.innerHTML = `<span class="option-letter">${LETTERS[i]}</span> ${escapeHtml(opt.option_text)}`;
            btn.addEventListener('click', () => selectAnswer(btn, q.points));
            grid.appendChild(btn);
        });

        // Reset feedback & next button
        hideFeedback();
        const nextBtn = document.getElementById('next-btn');
        nextBtn.disabled   = true;
        nextBtn.innerHTML  = 'Selecciona una respuesta <i class="fa-solid fa-arrow-right"></i>';
    }

    /* ─── Answer Selection ────────────────────────────── */
    function selectAnswer(selectedBtn, questionPoints) {
        if (answered) return;
        answered = true;

        const isCorrect = selectedBtn.dataset.correct === '1';

        // Disable all options
        document.querySelectorAll('.option-btn').forEach(btn => {
            btn.disabled = true;
            if (btn.dataset.correct === '1') btn.classList.add('reveal-correct');
        });

        if (isCorrect) {
            selectedBtn.classList.add('correct');
            selectedBtn.classList.remove('reveal-correct');
            const xpEarned = questionPoints + (streak >= 2 ? 5 : 0); // streak bonus
            totalXP      += xpEarned;
            totalCorrect += 1;
            streak       += 1;
            showFeedback(true, xpEarned);
            spawnXpParticle(selectedBtn, xpEarned);
        } else {
            selectedBtn.classList.add('wrong');
            streak = 0;
            showFeedback(false, 0);
        }

        updateHUD();

        // Enable next button
        const nextBtn = document.getElementById('next-btn');
        nextBtn.disabled  = false;
        const isLast      = currentIndex >= QUESTIONS.length - 1;
        nextBtn.innerHTML = isLast
            ? '<i class="fa-solid fa-flag-checkered"></i> Ver Resultados'
            : 'Siguiente Pregunta <i class="fa-solid fa-arrow-right"></i>';
    }

    /* ─── Next Question / Show Results ───────────────── */
    function nextQuestion() {
        if (!answered) return;

        currentIndex++;

        if (currentIndex >= QUESTIONS.length) {
            showResults();
        } else {
            renderQuestion(currentIndex);
        }
    }

    /* ─── Feedback Banner ────────────────────────────── */
    function showFeedback(correct, xp) {
        const banner   = document.getElementById('feedback-banner');
        const icon     = document.getElementById('feedback-icon');
        const text     = document.getElementById('feedback-text');

        banner.className = 'feedback-banner show ' + (correct ? 'correct-fb' : 'wrong-fb');
        icon.className   = correct ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark';

        if (correct) {
            const streakMsg = streak >= 3 ? ` ¡Racha x${streak}! +5 XP bonus!` : '';
            text.textContent = `¡Correcto! +${xp} XP ganados.${streakMsg}`;
        } else {
            const correctOpt = [...document.querySelectorAll('.option-btn')].find(b => b.dataset.correct === '1');
            const correctText = correctOpt ? correctOpt.textContent.trim() : '—';
            text.textContent = `Incorrecto. La respuesta era: ${correctText}`;
        }
    }

    function hideFeedback() {
        document.getElementById('feedback-banner').className = 'feedback-banner';
    }

    /* ─── HUD Update ─────────────────────────────────── */
    function updateHUD() {
        document.getElementById('hud-xp').textContent      = totalXP;
        document.getElementById('hud-correct').textContent = totalCorrect;
        document.getElementById('hud-streak').textContent  = streak;
    }

    /* ─── Results Screen ─────────────────────────────── */
    function showResults() {
        const total    = QUESTIONS.length;
        const accuracy = total > 0 ? Math.round((totalCorrect / total) * 100) : 0;

        document.getElementById('question-panel').style.display = 'none';
        document.getElementById('results-screen').style.display = 'block';

        // Update progress bar to 100%
        document.getElementById('progress-bar').style.width  = '100%';
        document.getElementById('progress-pct').textContent  = '100%';
        document.getElementById('progress-text').textContent = `Completado — ${total} de ${total}`;

        // Badge & title
        let emoji = '🏆', title = '¡Misión Cumplida!', subtitle;
        if (accuracy >= 90) {
            emoji = '🎖️'; title = '¡Élite Táctica!'; subtitle = 'Rendimiento excepcional. Honor al mérito.';
        } else if (accuracy >= 70) {
            emoji = '🏆'; subtitle = 'Buen desempeño. ¡Sigue adiestrándote!';
        } else if (accuracy >= 50) {
            emoji = '📋'; title = 'Misión Parcial'; subtitle = 'Repasa la lección e inténtalo de nuevo.';
        } else {
            emoji = '🔁'; title = 'Necesita Refuerzo'; subtitle = 'Estudio adicional requerido. ¡Tú puedes!';
        }

        document.getElementById('results-emoji').textContent   = emoji;
        document.getElementById('results-title').textContent   = title;
        document.getElementById('results-subtitle').textContent = subtitle;
        document.getElementById('res-xp').textContent          = totalXP;
        document.getElementById('res-correct').textContent     = `${totalCorrect}/${total}`;
        document.getElementById('res-accuracy').textContent    = accuracy + '%';

        // Set XP for form submission
        document.getElementById('points-earned-input').value = totalXP;

        updateHUD();
    }

    /* ─── XP Particle ────────────────────────────────── */
    function spawnXpParticle(btn, xp) {
        const rect    = btn.getBoundingClientRect();
        const el      = document.createElement('div');
        el.className  = 'xp-particle';
        el.textContent = `+${xp} XP`;
        el.style.left = (rect.left + rect.width / 2 - 30) + 'px';
        el.style.top  = (rect.top + window.scrollY - 10) + 'px';
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 1300);
    }

    /* ─── HTML escape ────────────────────────────────── */
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ─── Init ───────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('hud-total').textContent = QUESTIONS.length;
        renderQuestion(0);
    });
</script>
@endsection
