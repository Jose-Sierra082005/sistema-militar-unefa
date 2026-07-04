@extends('layouts.admin')

@section('title', 'Mi Perfil | Tactic Force')

@section('styles')
<style>
/* ============================================================ LAYOUT */
.profile-grid { display:grid; grid-template-columns:310px 1fr; gap:26px; align-items:start; }
@media(max-width:900px){ .profile-grid{ grid-template-columns:1fr; } }

/* ============================================================ IDENTITY CARD */
.identity-card {
    background:linear-gradient(145deg,rgba(13,17,27,.97),rgba(18,24,38,.92));
    border:1px solid rgba(46,74,53,.5); border-radius:20px;
    padding:30px 22px; display:flex; flex-direction:column;
    align-items:center; gap:16px; text-align:center;
    position:relative; overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.5);
}
.identity-card::before {
    content:''; position:absolute; top:-50px; left:-50px;
    width:200px; height:200px;
    background:radial-gradient(circle,rgba(42,71,51,.3),transparent 70%);
    pointer-events:none;
}
.avatar-ring {
    width:96px; height:96px; border-radius:50%;
    background:linear-gradient(135deg,var(--tactical-green),#1b3a25);
    border:3px solid var(--accent-gold);
    display:flex; align-items:center; justify-content:center;
    font-size:2.4rem; color:var(--accent-gold);
    box-shadow:0 0 25px rgba(212,175,55,.2);
    position:relative; z-index:1;
}
.online-dot {
    position:absolute; bottom:4px; right:4px;
    width:14px; height:14px; background:var(--success-green);
    border-radius:50%; border:2px solid var(--bg-dark);
    box-shadow:0 0 8px rgba(46,204,113,.6);
    animation:pulse-dot 2s infinite;
}
@keyframes pulse-dot{ 0%,100%{ transform:scale(1); } 50%{ transform:scale(1.15); opacity:.8; } }

.id-name  { font-size:1.25rem; font-weight:700; color:var(--text-main); }
.id-email { font-size:.78rem; color:var(--text-secondary); font-family:'Share Tech Mono',monospace; word-break:break-all; margin-top:4px; }

.rank-badge {
    border-radius:30px; padding:5px 15px;
    font-size:.74rem; font-weight:700; font-family:'Share Tech Mono',monospace;
    letter-spacing:1.5px; text-transform:uppercase; border:1px solid;
}
.google-badge {
    display:flex; align-items:center; gap:7px;
    background:rgba(66,133,244,.1); border:1px solid rgba(66,133,244,.35);
    border-radius:30px; padding:5px 13px;
    font-size:.74rem; color:#7db0f5; font-weight:600;
}
.id-divider { width:100%; height:1px; background:linear-gradient(to right,transparent,rgba(46,74,53,.5),transparent); }

.stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:9px; width:100%; }
.stat-cell { background:rgba(7,9,14,.6); border:1px solid rgba(46,74,53,.3); border-radius:9px; padding:11px 7px; text-align:center; }
.stat-val  { font-family:'Share Tech Mono',monospace; font-size:1.35rem; font-weight:700; color:var(--accent-gold); }
.stat-lbl  { font-size:.64rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; margin-top:2px; }

.sec-row { display:flex; justify-content:space-between; align-items:center; }
.sec-lbl { font-size:.76rem; color:var(--text-secondary); }
.sec-lbl i { width:16px; color:var(--accent-gold); }

.pill      { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.68rem; font-weight:700; letter-spacing:.7px; font-family:'Share Tech Mono',monospace; }
.pill-ok   { background:rgba(46,204,113,.12); border:1px solid rgba(46,204,113,.4); color:#9fedc0; }
.pill-warn { background:rgba(212,175,55,.12);  border:1px solid rgba(212,175,55,.4);  color:var(--accent-gold); }
.pill-off  { background:rgba(255,77,77,.1);    border:1px solid rgba(255,77,77,.35);  color:#ff9999; }

.id-meta     { width:100%; font-size:.75rem; color:var(--text-secondary); display:flex; flex-direction:column; gap:7px; }
.id-meta-row { display:flex; justify-content:space-between; }
.id-meta-row span:last-child { color:var(--text-main); }

/* ============================================================ FORM PANELS */
.form-panel { background:var(--panel-bg); border:1px solid var(--border-primary); border-radius:15px; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,.4); }
.form-panel + .form-panel { margin-top:20px; }

.fp-header { background:var(--panel-header); padding:15px 22px; border-bottom:1px solid rgba(46,74,53,.3); display:flex; align-items:center; gap:13px; }
.fp-icon   { width:35px; height:35px; background:linear-gradient(135deg,rgba(212,175,55,.15),rgba(212,175,55,.05)); border:1px solid rgba(212,175,55,.3); border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.92rem; color:var(--accent-gold); flex-shrink:0; }
.fp-title  { font-family:'Share Tech Mono',monospace; font-size:.88rem; font-weight:700; color:var(--accent-gold); text-transform:uppercase; letter-spacing:1px; }
.fp-sub    { font-size:.74rem; color:var(--text-secondary); margin-top:2px; line-height:1.4; }
.fp-body   { padding:24px; }

.form-row  { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:600px){ .form-row{ grid-template-columns:1fr; } }

.fg { display:flex; flex-direction:column; gap:7px; }
.fg label  { font-size:.69rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:1px; font-family:'Share Tech Mono',monospace; }
.fg input  { background:rgba(7,9,14,.8); border:1px solid rgba(46,74,53,.4); border-radius:9px; padding:11px 14px; color:var(--text-main); font-size:.9rem; outline:none; transition:border-color .25s,box-shadow .25s; width:100%; }
.fg input:focus    { border-color:var(--accent-gold); box-shadow:0 0 0 3px rgba(212,175,55,.1); }
.fg input:disabled { opacity:.4; cursor:not-allowed; }
.fhint { font-size:.7rem; color:var(--text-secondary); }
.ferr  { font-size:.71rem; color:#ff9999; }

.pw-wrap { position:relative; }
.pw-wrap input { padding-right:42px; }
.eye-btn { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size:.88rem; transition:color .2s; padding:0; }
.eye-btn:hover { color:var(--accent-gold); }

.str-bar  { height:3px; border-radius:3px; background:rgba(46,74,53,.3); margin-top:7px; overflow:hidden; }
.str-fill { height:100%; border-radius:3px; width:0; transition:width .4s,background-color .4s; }

.form-actions { display:flex; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid rgba(46,74,53,.2); }

.btn-save    { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,var(--tactical-green),#1b3a25); border:1px solid rgba(42,71,51,.8); border-radius:9px; color:var(--text-main); padding:10px 24px; font-size:.84rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; cursor:pointer; transition:all .25s; box-shadow:0 4px 12px rgba(0,0,0,.3); }
.btn-save:hover  { border-color:var(--accent-gold); color:var(--accent-gold); transform:translateY(-1px); }
.btn-danger  { background:linear-gradient(135deg,rgba(180,40,40,.4),rgba(130,20,20,.3)); border-color:rgba(255,77,77,.5); color:#ff9999; }
.btn-danger:hover  { border-color:var(--error-red); color:#ffbcbc; }
.btn-green   { background:linear-gradient(135deg,rgba(46,204,113,.25),rgba(30,160,80,.15)); border-color:rgba(46,204,113,.5); color:#9fedc0; }
.btn-green:hover   { border-color:#2ecc71; color:#cbf7e0; }
.btn-orange  { background:linear-gradient(135deg,rgba(249,115,22,.25),rgba(200,80,10,.15)); border-color:rgba(249,115,22,.5); color:#fdb07c; }
.btn-orange:hover  { border-color:#f97316; color:#ffcba0; }

/* alerts */
.al-ok   { display:flex; align-items:center; gap:11px; background:rgba(46,204,113,.1); border:1px solid rgba(46,204,113,.4); border-radius:9px; padding:13px 16px; font-size:.84rem; color:#9fedc0; margin-bottom:20px; animation:fadein .3s; }
.al-err  { display:flex; align-items:flex-start; gap:11px; background:rgba(255,77,77,.1); border:1px solid rgba(255,77,77,.4); border-radius:9px; padding:13px 16px; font-size:.84rem; color:#ff9999; margin-bottom:20px; animation:fadein .3s; }
.al-info { display:flex; align-items:center; gap:11px; background:rgba(66,133,244,.08); border:1px solid rgba(66,133,244,.3); border-radius:9px; padding:13px 16px; font-size:.83rem; color:#9ab8ef; margin-bottom:18px; }
.al-warn { display:flex; align-items:center; gap:11px; background:rgba(249,115,22,.08); border:1px solid rgba(249,115,22,.3); border-radius:9px; padding:13px 16px; font-size:.83rem; color:#fdb07c; margin-bottom:18px; }
@keyframes fadein { from{ opacity:0; transform:translateY(-5px); } to{ opacity:1; transform:translateY(0); } }

/* 2FA */
.tfa-grid     { display:grid; grid-template-columns:auto 1fr; gap:26px; align-items:start; }
@media(max-width:640px){ .tfa-grid{ grid-template-columns:1fr; } }
.qr-wrap      { display:flex; flex-direction:column; align-items:center; gap:14px; background:rgba(7,9,14,.7); border:1px solid rgba(46,74,53,.35); border-radius:13px; padding:20px; min-width:190px; }
#qr-canvas    { border-radius:8px; background:white; padding:8px; }
.secret-box   { background:rgba(7,9,14,.9); border:1px solid rgba(212,175,55,.3); border-radius:7px; padding:9px 14px; font-family:'Share Tech Mono',monospace; font-size:1rem; color:var(--accent-gold); letter-spacing:2px; text-align:center; cursor:pointer; transition:border-color .2s; width:100%; word-break:break-all; }
.secret-box:hover { border-color:var(--accent-gold); }
.steps        { list-style:none; display:flex; flex-direction:column; gap:11px; }
.steps li     { display:flex; align-items:flex-start; gap:11px; font-size:.83rem; color:var(--text-secondary); }
.step-num     { min-width:24px; height:24px; background:rgba(212,175,55,.12); border:1px solid rgba(212,175,55,.35); border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Share Tech Mono',monospace; font-size:.72rem; color:var(--accent-gold); font-weight:700; }
.tfa-ok-card  { display:flex; align-items:center; gap:15px; background:rgba(46,204,113,.06); border:1px solid rgba(46,204,113,.3); border-radius:11px; padding:18px 22px; }

/* OTP disable form */
.otp-disable-box {
    background:rgba(7,9,14,.7); border:1px solid rgba(249,115,22,.35);
    border-radius:12px; padding:22px 24px; margin-top:20px;
    animation:fadein .35s ease;
}
.otp-disable-box .otp-title {
    font-family:'Share Tech Mono',monospace; font-size:.8rem; color:#fdb07c;
    text-transform:uppercase; letter-spacing:1px; margin-bottom:14px;
    display:flex; align-items:center; gap:8px;
}

/* page header */
.page-title  { font-family:'Share Tech Mono',monospace; font-size:1.35rem; font-weight:700; color:var(--text-main); text-transform:uppercase; letter-spacing:2px; display:flex; align-items:center; gap:11px; margin-bottom:24px; }
.breadcrumb  { font-size:.74rem; color:var(--text-secondary); margin-bottom:5px; display:flex; align-items:center; gap:6px; }
.breadcrumb a{ color:var(--text-secondary); text-decoration:none; transition:color .2s; }
.breadcrumb a:hover { color:var(--accent-gold); }
</style>
@endsection

@section('content')
@php
    $xp        = $totalXp;
    $rank      = 'Cadete';
    $rankColor = '#9aa5b1';
    if ($xp >=  500){ $rank='Distinguido';     $rankColor='#5b9cf6'; }
    if ($xp >= 1000){ $rank='Sarg. Acad&eacute;mico'; $rankColor='#2ecc71'; }
    if ($xp >= 2000){ $rank='Tte. Acad&eacute;mico';  $rankColor='#d4af37'; }
    if ($xp >= 3500){ $rank='Cap. Acad&eacute;mico';  $rankColor='#f97316'; }

    $hasPassword = !empty($user->password);
    $hasCedula   = !empty($user->cedula);
    $has2FA      = $user->two_factor_enabled && !empty($user->two_factor_secret);
    $isGoogle    = !empty($user->google_id);

    // Mostrar formulario OTP de desactivacion si fue enviado
    $otpSent = session('2fa_otp_sent');
@endphp

<div style="padding:28px;max-width:1160px;margin:0 auto;">

    <div class="breadcrumb">
        <a href="{{ route('student.dashboard') }}"><i class="fa-solid fa-house-chimney" style="font-size:.66rem;"></i> Panel de Control</a>
        <i class="fa-solid fa-chevron-right" style="font-size:.56rem;"></i>
        <span style="color:var(--accent-gold);">Mi Perfil</span>
    </div>

    <div class="page-title">
        <i class="fa-solid fa-id-card-clip" style="color:var(--accent-gold);"></i>
        Configuraci&oacute;n de Perfil
    </div>

    @if(session('success'))
        <div class="al-ok"><i class="fa-solid fa-circle-check fa-lg"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="al-err"><i class="fa-solid fa-triangle-exclamation fa-lg"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any() && !session('success'))
        <div class="al-err">
            <i class="fa-solid fa-triangle-exclamation fa-lg" style="margin-top:2px;"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
    @endif

    <div class="profile-grid">

        {{-- ============================== TARJETA IDENTIDAD ============================== --}}
        <div class="identity-card">
            <div class="avatar-ring">
                <i class="fa-solid fa-user-shield"></i>
                <span class="online-dot" title="En l&iacute;nea"></span>
            </div>

            <div>
                <div class="id-name">{{ $user->name }}</div>
                <div class="id-email">{{ $user->email }}</div>
            </div>

            <span class="rank-badge" style="border-color:{{ $rankColor }}44;color:{{ $rankColor }};background:{{ $rankColor }}11;">
                <i class="fa-solid fa-star" style="font-size:.6rem;"></i> {!! $rank !!}
            </span>

            @if($isGoogle)
                <div class="google-badge"><i class="fa-brands fa-google"></i> Vinculado con Google</div>
            @endif

            <div class="id-divider"></div>

            <div class="stat-grid">
                <div class="stat-cell"><div class="stat-val">{{ number_format($totalXp) }}</div><div class="stat-lbl">XP Total</div></div>
                <div class="stat-cell"><div class="stat-val">{{ $completedLessons }}</div><div class="stat-lbl">Lecciones</div></div>
                <div class="stat-cell"><div class="stat-val">{{ $totalCourses }}</div><div class="stat-lbl">M&oacute;dulos</div></div>
                <div class="stat-cell">
                    <div class="stat-val" style="font-size:.95rem;">
                        @if($has2FA)<i class="fa-solid fa-shield-halved" style="color:var(--success-green);"></i>
                        @else<i class="fa-solid fa-shield-slash" style="color:#ff6b6b;"></i>@endif
                    </div>
                    <div class="stat-lbl">2FA {{ $has2FA ? 'Activo' : 'Inactivo' }}</div>
                </div>
            </div>

            <div class="id-divider"></div>

            <div style="width:100%;display:flex;flex-direction:column;gap:8px;">
                <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-family:'Share Tech Mono',monospace;text-align:left;">Estado de Seguridad</div>
                <div class="sec-row">
                    <span class="sec-lbl"><i class="fa-solid fa-key"></i> Contrase&ntilde;a</span>
                    @if($hasPassword)<span class="pill pill-ok"><i class="fa-solid fa-check"></i> OK</span>
                    @else<span class="pill pill-off"><i class="fa-solid fa-xmark"></i> Sin contrase&ntilde;a</span>@endif
                </div>
                <div class="sec-row">
                    <span class="sec-lbl"><i class="fa-solid fa-id-badge"></i> C&eacute;dula</span>
                    @if($hasCedula)<span class="pill pill-ok"><i class="fa-solid fa-check"></i> {{ $user->cedula }}</span>
                    @else<span class="pill pill-warn"><i class="fa-solid fa-clock"></i> Pendiente</span>@endif
                </div>
                <div class="sec-row">
                    <span class="sec-lbl"><i class="fa-solid fa-mobile-screen"></i> 2FA</span>
                    @if($has2FA)<span class="pill pill-ok"><i class="fa-solid fa-shield-halved"></i> Activo</span>
                    @else<span class="pill pill-off"><i class="fa-solid fa-shield-slash"></i> Inactivo</span>@endif
                </div>
            </div>

            <div class="id-divider"></div>

            <div class="id-meta">
                <div class="id-meta-row"><span><i class="fa-solid fa-calendar-plus" style="width:16px;color:var(--accent-gold);"></i> Registro</span><span>{{ $user->created_at->format('d/m/Y') }}</span></div>
                <div class="id-meta-row"><span><i class="fa-solid fa-user-tag" style="width:16px;color:var(--accent-gold);"></i> Rol</span><span>{{ ucfirst($user->role) }}</span></div>
                <div class="id-meta-row"><span><i class="fa-solid fa-hashtag" style="width:16px;color:var(--accent-gold);"></i> ID</span><span style="font-family:'Share Tech Mono',monospace;">#{{ str_pad($user->id,5,'0',STR_PAD_LEFT) }}</span></div>
            </div>
        </div>

        {{-- ============================== FORMULARIOS ============================== --}}
        <div>

            {{-- === PANEL 1: Datos de Identificaci&oacute;n === --}}
            <div class="form-panel">
                <div class="fp-header">
                    <div class="fp-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div>
                        <div class="fp-title">Datos de Identificaci&oacute;n</div>
                        <div class="fp-sub">Actualice su nombre de oficial y correo institucional</div>
                    </div>
                </div>
                <div class="fp-body">
                    <form method="POST" action="{{ route('student.profile.update') }}">
                        @csrf
                        <div class="form-row">
                            <div class="fg">
                                <label for="name">Nombre Completo</label>
                                <input type="text" id="name" name="name" value="{{ old('name',$user->name) }}" placeholder="Ej. Juan Rodr&iacute;guez" required>
                                @error('name')<span class="ferr">{{ $message }}</span>@enderror
                            </div>
                            <div class="fg">
                                <label for="email">Correo Electr&oacute;nico</label>
                                <input type="email" id="email" name="email" value="{{ old('email',$user->email) }}" placeholder="correo@unefa.edu.ve" required {{ $isGoogle ? 'disabled' : '' }}>
                                @if($isGoogle)<span class="fhint"><i class="fa-brands fa-google"></i> Gestionado por Google &mdash; no editable.</span>@endif
                                @error('email')<span class="ferr">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- === PANEL 2: C&eacute;dula de Identidad === --}}
            <div class="form-panel">
                <div class="fp-header">
                    <div class="fp-icon"><i class="fa-solid fa-id-badge"></i></div>
                    <div>
                        <div class="fp-title">C&eacute;dula de Identidad</div>
                        <div class="fp-sub">
                            @if($hasCedula) C&eacute;dula registrada. Puede iniciar sesi&oacute;n con c&eacute;dula + contrase&ntilde;a.
                            @else Registre su c&eacute;dula para poder iniciar sesi&oacute;n como alternativa al correo.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="fp-body">
                    @if($hasCedula)
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:rgba(7,9,14,.5);border:1px solid rgba(46,74,53,.3);border-radius:9px;padding:14px 18px;">
                            <div>
                                <div style="font-size:.7rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-family:'Share Tech Mono',monospace;">C&eacute;dula registrada</div>
                                <div style="font-size:1.25rem;font-weight:700;color:var(--accent-gold);font-family:'Share Tech Mono',monospace;margin-top:3px;">V-{{ $user->cedula }}</div>
                            </div>
                            <span class="pill pill-ok"><i class="fa-solid fa-check"></i> Activa para login</span>
                        </div>
                        <p style="font-size:.79rem;color:var(--text-secondary);margin-top:12px;line-height:1.5;">
                            <i class="fa-solid fa-circle-info" style="color:var(--accent-gold);"></i>
                            Puede escribir su c&eacute;dula en el campo de usuario del formulario de acceso junto con su contrase&ntilde;a.
                            Para cambiarla contacte al administrador.
                        </p>
                    @else
                        <form method="POST" action="{{ route('security.update') }}">
                            @csrf
                            <p style="font-size:.81rem;color:var(--text-secondary);margin-bottom:16px;line-height:1.5;">
                                <i class="fa-solid fa-triangle-exclamation" style="color:var(--accent-gold);"></i>
                                No tiene c&eacute;dula registrada. Al agregarla podr&aacute; iniciar sesi&oacute;n con ella en lugar del correo.
                            </p>
                            <div class="fg" style="max-width:300px;">
                                <label for="cedula">N&uacute;mero de C&eacute;dula (sin puntos ni guiones)</label>
                                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" placeholder="Ej. 31149881">
                                <span class="fhint">Formatos: <strong>31149881</strong> o <strong>V31149881</strong></span>
                                @error('cedula')<span class="ferr">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save"><i class="fa-solid fa-id-card"></i> Registrar C&eacute;dula</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- === PANEL 3: Contrase&ntilde;a ===
                 LOGICA:
                 - google_id: no pide contrase&ntilde;a actual (sesi&oacute;n OAuth prueba identidad)
                 - sin google_id: debe confirmar la contrase&ntilde;a actual
            --}}
            <div class="form-panel">
                <div class="fp-header">
                    <div class="fp-icon"><i class="fa-solid fa-lock"></i></div>
                    <div>
                        <div class="fp-title">
                            @if($isGoogle && !$hasPassword) Establecer Contrase&ntilde;a
                            @else Cambiar Contrase&ntilde;a @endif
                        </div>
                        <div class="fp-sub">
                            @if($isGoogle) Su sesi&oacute;n Google confirma su identidad &mdash; no necesita la contrase&ntilde;a anterior.
                            @else Debe verificar su contrase&ntilde;a actual antes de cambiarla. @endif
                        </div>
                    </div>
                </div>
                <div class="fp-body">

                    @if($isGoogle)
                        <div class="al-info">
                            <i class="fa-brands fa-google fa-lg"></i>
                            <span>
                                @if(!$hasPassword) No tiene contrase&ntilde;a configurada. Al establecerla podr&aacute; acceder tambi&eacute;n con correo + contrase&ntilde;a.
                                @else Puede cambiar su contrase&ntilde;a directamente sin necesitar la anterior. @endif
                            </span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.profile.password') }}">
                        @csrf

                        {{-- Contrase&ntilde;a actual: SOLO para usuarios sin Google --}}
                        @if(!$isGoogle)
                            <div class="fg pw-wrap" style="margin-bottom:16px;">
                                <label for="current_password">Contrase&ntilde;a Actual</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Ingrese su contrase&ntilde;a actual">
                                <button type="button" class="eye-btn" onclick="toggleEye('current_password',this)"><i class="fa-solid fa-eye"></i></button>
                                @error('current_password')<span class="ferr"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>@enderror
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="fg pw-wrap">
                                <label for="new_password">Nueva Contrase&ntilde;a</label>
                                <input type="password" id="new_password" name="new_password" placeholder="M&iacute;n. 8 caracteres" oninput="checkStr(this.value)">
                                <button type="button" class="eye-btn" onclick="toggleEye('new_password',this)"><i class="fa-solid fa-eye"></i></button>
                                <div class="str-bar"><div class="str-fill" id="str-fill"></div></div>
                                <span id="str-label" class="fhint"></span>
                                @error('new_password')<span class="ferr"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>@enderror
                            </div>
                            <div class="fg pw-wrap">
                                <label for="new_password_confirmation">Confirmar Contrase&ntilde;a</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Repita la contrase&ntilde;a">
                                <button type="button" class="eye-btn" onclick="toggleEye('new_password_confirmation',this)"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>

                        <p class="fhint" style="margin-top:9px;">
                            <i class="fa-solid fa-shield-halved" style="color:var(--accent-gold);"></i>
                            M&iacute;n. 8 caracteres &middot; may&uacute;scula &middot; n&uacute;mero &middot; car&aacute;cter especial (@$!%*?&amp;)
                        </p>

                        <div class="form-actions">
                            @if($isGoogle && !$hasPassword)
                                <button type="submit" class="btn-save btn-green"><i class="fa-solid fa-key"></i> Establecer Contrase&ntilde;a</button>
                            @else
                                <button type="submit" class="btn-save btn-danger"><i class="fa-solid fa-rotate"></i> Actualizar Contrase&ntilde;a</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- === PANEL 4: Google Authenticator 2FA === --}}
            <div class="form-panel">
                <div class="fp-header">
                    <div class="fp-icon" style="background:rgba(46,204,113,.1);border-color:rgba(46,204,113,.3);">
                        <i class="fa-solid fa-mobile-screen" style="color:#2ecc71;"></i>
                    </div>
                    <div style="flex:1;">
                        <div class="fp-title" style="color:#2ecc71;">Doble Factor (2FA)</div>
                        <div class="fp-sub">Google Authenticator &mdash; c&oacute;digo TOTP de 6 d&iacute;gitos</div>
                    </div>
                    @if($has2FA)
                        <span class="pill pill-ok"><i class="fa-solid fa-shield-halved"></i> Activo</span>
                    @else
                        <span class="pill pill-off"><i class="fa-solid fa-shield-slash"></i> Inactivo</span>
                    @endif
                </div>
                <div class="fp-body">

                    @if($has2FA)
                        {{-- ---- 2FA ACTIVO: mostrar estado + opcion de desactivar ---- --}}
                        <div class="tfa-ok-card">
                            <i class="fa-solid fa-shield-halved fa-2x" style="color:var(--success-green);"></i>
                            <div>
                                <div style="font-weight:700;color:var(--success-green);margin-bottom:4px;">Doble Factor Activo y Protegido</div>
                                <div style="font-size:.81rem;color:var(--text-secondary);line-height:1.5;">
                                    Su cuenta requiere el c&oacute;digo de Google Authenticator en cada inicio de sesi&oacute;n.
                                    Abra la app, busque <strong style="color:var(--accent-gold);">Tactic Force</strong> e ingrese el c&oacute;digo de 6 d&iacute;gitos cuando se le solicite.
                                </div>
                            </div>
                        </div>

                        {{-- Bloque de desactivaci&oacute;n --}}
                        @if(!$otpSent)
                            {{-- Paso 1: bot&oacute;n para solicitar OTP --}}
                            <div style="margin-top:20px;padding:16px 20px;background:rgba(249,115,22,.06);border:1px solid rgba(249,115,22,.25);border-radius:11px;">
                                <div style="font-size:.82rem;color:var(--text-secondary);margin-bottom:14px;line-height:1.5;">
                                    <i class="fa-solid fa-triangle-exclamation" style="color:#f97316;"></i>
                                    Para desactivar el 2FA le enviaremos un c&oacute;digo de verificaci&oacute;n OTP a su correo <strong style="color:var(--text-main);">{{ $user->email }}</strong>.
                                    Deber&aacute; ingresarlo para confirmar la acci&oacute;n.
                                </div>
                                <form method="POST" action="{{ route('student.profile.2fa.disable.send') }}">
                                    @csrf
                                    <button type="submit" class="btn-save btn-orange">
                                        <i class="fa-solid fa-envelope-open-text"></i> Enviar c&oacute;digo OTP para desactivar
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Paso 2: formulario para ingresar el OTP recibido --}}
                            @if(session('info_2fa'))
                                <div class="al-warn" style="margin-top:16px;">
                                    <i class="fa-solid fa-envelope fa-lg"></i> {{ session('info_2fa') }}
                                </div>
                            @endif

                            <div class="otp-disable-box">
                                <div class="otp-title">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                    Ingrese el c&oacute;digo OTP recibido en su correo
                                </div>

                                <form method="POST" action="{{ route('student.profile.2fa.disable.confirm') }}">
                                    @csrf
                                    <div class="fg" style="max-width:220px;">
                                        <label for="disable_otp">C&oacute;digo OTP (6 d&iacute;gitos)</label>
                                        <input type="text" id="disable_otp" name="disable_otp"
                                            placeholder="000000" maxlength="6"
                                            inputmode="numeric" pattern="[0-9]{6}"
                                            autocomplete="one-time-code"
                                            style="font-size:1.4rem;font-family:'Share Tech Mono',monospace;letter-spacing:6px;text-align:center;">
                                        @error('disable_otp')<span class="ferr"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>@enderror
                                    </div>

                                    <div style="display:flex;gap:12px;margin-top:16px;flex-wrap:wrap;">
                                        <button type="submit" class="btn-save btn-danger">
                                            <i class="fa-solid fa-shield-slash"></i> Confirmar Desactivaci&oacute;n
                                        </button>
                                    </div>
                                </form>

                                {{-- Re-enviar OTP --}}
                                <form method="POST" action="{{ route('student.profile.2fa.disable.send') }}" style="margin-top:14px;">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;color:var(--text-secondary);font-size:.78rem;cursor:pointer;text-decoration:underline;padding:0;">
                                        <i class="fa-solid fa-rotate-right"></i> Reenviar c&oacute;digo OTP
                                    </button>
                                </form>
                            </div>
                        @endif

                    @elseif(!$hasPassword)
                        {{-- Sin contrase&ntilde;a configurada: no se puede activar 2FA --}}
                        <div class="al-err">
                            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                            <span>Debe <strong>establecer una contrase&ntilde;a</strong> antes de activar el 2FA. El Doble Factor solo funciona con inicio de sesi&oacute;n por correo/c&eacute;dula + contrase&ntilde;a.</span>
                        </div>

                    @else
                        {{-- ---- 2FA INACTIVO: mostrar QR y formulario de activaci&oacute;n ---- --}}
                        @php
                            $twoFaSecret = \App\Services\Google2FAService::generateSecretKey();
                            $twoFaQrUri  = \App\Services\Google2FAService::getQRCodeUrl($user->name, $user->email, $twoFaSecret);
                        @endphp

                        <div class="tfa-grid">
                            <div class="qr-wrap">
                                <canvas id="qr-canvas" width="164" height="164"></canvas>
                                <div style="font-size:.7rem;color:var(--text-secondary);text-align:center;line-height:1.4;">
                                    Escanee con<br><strong style="color:var(--text-main);">Google Authenticator</strong>
                                </div>
                                <div class="secret-box" id="secret-display" title="Clic para copiar" onclick="copySecret()">{{ $twoFaSecret }}</div>
                                <span style="font-size:.66rem;color:var(--text-secondary);"><i class="fa-solid fa-copy"></i> Clave manual &mdash; clic para copiar</span>
                            </div>

                            <div>
                                <ul class="steps" style="margin-bottom:18px;">
                                    <li><div class="step-num">1</div><span>Instale <strong style="color:var(--text-main);">Google Authenticator</strong> en su tel&eacute;fono (Android o iOS).</span></li>
                                    <li><div class="step-num">2</div><span>Abra la app, toque <strong style="color:var(--text-main);">+</strong> y seleccione <em>Escanear c&oacute;digo QR</em>.</span></li>
                                    <li><div class="step-num">3</div><span>Apunte la c&aacute;mara al c&oacute;digo de la izquierda. Si no puede escanear, use la clave manual.</span></li>
                                    <li><div class="step-num">4</div><span>Ingrese el c&oacute;digo de <strong style="color:var(--text-main);">6 d&iacute;gitos</strong> que muestra la app y pulse Activar.</span></li>
                                </ul>

                                <form method="POST" action="{{ route('security.2fa-activate') }}">
                                    @csrf
                                    <input type="hidden" name="secret" value="{{ $twoFaSecret }}">
                                    <div class="fg">
                                        <label for="code_2fa">C&oacute;digo de Google Authenticator</label>
                                        <input type="text" id="code_2fa" name="code"
                                            placeholder="000000" maxlength="6"
                                            inputmode="numeric" pattern="[0-9]{6}"
                                            autocomplete="one-time-code"
                                            style="font-size:1.25rem;font-family:'Share Tech Mono',monospace;letter-spacing:5px;text-align:center;max-width:170px;">
                                        @error('code')<span class="ferr">{{ $message }}</span>@enderror
                                    </div>
                                    <div style="margin-top:14px;">
                                        <button type="submit" class="btn-save btn-green">
                                            <i class="fa-solid fa-shield-halved"></i> Activar Doble Factor
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>{{-- /panel 4 --}}

        </div>{{-- /right column --}}
    </div>{{-- /profile-grid --}}
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// === QR Code ===
@if(!$has2FA && $hasPassword)
(function(){
    var uri    = @json($twoFaQrUri ?? '');
    var canvas = document.getElementById('qr-canvas');
    if (!uri || !canvas) return;
    var tmp = document.createElement('div');
    tmp.style.display = 'none';
    document.body.appendChild(tmp);
    new QRCode(tmp, { text:uri, width:164, height:164,
        colorDark:'#000000', colorLight:'#FFFFFF',
        correctLevel: QRCode.CorrectLevel.M });
    setTimeout(function(){
        var src = tmp.querySelector('canvas') || tmp.querySelector('img');
        if (!src){ document.body.removeChild(tmp); return; }
        var ctx = canvas.getContext('2d');
        if (src.tagName === 'CANVAS'){ ctx.drawImage(src,0,0,164,164); }
        else { src.onload = function(){ ctx.drawImage(src,0,0,164,164); }; }
        document.body.removeChild(tmp);
    }, 250);
})();
@endif

// === Copiar clave secreta ===
function copySecret(){
    var el = document.getElementById('secret-display');
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim()).then(function(){
        var orig = el.textContent;
        el.textContent = '&#10003; Copiado';
        el.style.color = '#2ecc71';
        setTimeout(function(){ el.textContent = orig; el.style.color = ''; }, 1800);
    });
}

// === Medidor de fortaleza ===
function checkStr(val){
    var fill  = document.getElementById('str-fill');
    var label = document.getElementById('str-label');
    if (!fill || !label) return;
    var s = 0;
    if (val.length >= 8)           s++;
    if (/[A-Z]/.test(val))         s++;
    if (/[0-9]/.test(val))         s++;
    if (/[^A-Za-z0-9]/.test(val))  s++;
    var m = [
        {w:'0%',  c:'transparent', t:''},
        {w:'25%', c:'#ff4d4d',     t:'D\u00e9bil'},
        {w:'50%', c:'#f97316',     t:'Moderada'},
        {w:'75%', c:'#eab308',     t:'Buena'},
        {w:'100%',c:'#2ecc71',     t:'Fuerte'},
    ];
    fill.style.width = m[s].w;
    fill.style.backgroundColor = m[s].c;
    label.textContent = val.length > 0 ? 'Fortaleza: ' + m[s].t : '';
    label.style.color = m[s].c;
}

// === Toggle ojo contrase&ntilde;a ===
function toggleEye(id, btn){
    var inp = document.getElementById(id);
    var ico = btn.querySelector('i');
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.classList.toggle('fa-eye');
    ico.classList.toggle('fa-eye-slash');
}

// === Solo n&uacute;meros en inputs OTP ===
['code_2fa','disable_otp'].forEach(function(id){
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', function(){ this.value = this.value.replace(/\D/g,'').slice(0,6); });
});
</script>
@endsection
