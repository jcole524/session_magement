<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pain? — Personal Training System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:    #1f86ba;
            --blue-2:  #0066cc;
            --blue-glow: rgba(0,170,255,0.35);
            --dark:    #03080f;
            --card-bg: rgba(3,12,22,0.82);
        }

        html, body {
            height: 100%;
            font-family: 'Rajdhani', sans-serif;
            background: var(--dark);
            color: #e8eaf0;
            overflow: hidden;
        }

        /* ── Full-screen background ─────────────────────────────────── */
        .bg {
            position: fixed;
            inset: 0;
            background: url('{{ asset('gym.webp') }}') center center / cover no-repeat;
            z-index: 0;
        }
        .bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right, rgba(3,8,15,0.97) 0%, rgba(3,8,15,0.6) 50%, rgba(3,8,15,0.15) 100%),
                linear-gradient(to top, rgba(3,8,15,0.9) 0%, transparent 50%);
        }

        /* ── Scanline texture overlay ───────────────────────────────── */
        .scanlines {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.03) 2px,
                rgba(0,0,0,0.03) 4px
            );
            pointer-events: none;
        }

        /* ── Floating particles ─────────────────────────────────────── */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--blue);
            border-radius: 50%;
            animation: float linear infinite;
            opacity: 0;
        }
        @keyframes float {
            0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.6; }
            100% { transform: translateY(-100px) translateX(40px); opacity: 0; }
        }

        /* ── Layout ─────────────────────────────────────────────────── */
        .layout {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 5vw;
        }

        /* ── Left — branding ────────────────────────────────────────── */
        .branding {
            flex: 1;
            padding-right: 4rem;
            animation: fadeInLeft .8s ease both;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .brand-eyebrow {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .25em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .brand-eyebrow::before {
            content: '';
            display: block;
            width: 28px;
            height: 1px;
            background: var(--blue);
        }

        .brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(4rem, 10vw, 8rem);
            line-height: .9;
            letter-spacing: .04em;
            color: #fff;
            text-shadow: 0 0 60px var(--blue-glow), 0 0 120px rgba(0,100,200,0.2);
            margin-bottom: 1.5rem;
        }
        .brand-name span {
            color: var(--blue);
            text-shadow: 0 0 30px var(--blue), 0 0 80px var(--blue-glow);
        }

        .brand-tagline {
            font-size: 1.1rem;
            font-weight: 300;
            color: rgba(200,220,240,0.7);
            letter-spacing: .08em;
            line-height: 1.6;
            max-width: 380px;
            margin-bottom: 2.5rem;
        }

        .brand-stats {
            display: flex;
            gap: 2.5rem;
        }
        .stat { display: flex; flex-direction: column; }
        .stat-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: var(--blue);
            line-height: 1;
        }
        .stat-label {
            font-size: .7rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(180,200,220,0.5);
            margin-top: .2rem;
        }

        /* ── Right — login card ─────────────────────────────────────── */
        .login-side {
            width: 380px;
            flex-shrink: 0;
            animation: fadeInRight .8s ease .2s both;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid rgba(0,170,255,0.15);
            border-radius: 4px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        /* Corner accent lines */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 40px; height: 40px;
            border-top: 2px solid var(--blue);
            border-left: 2px solid var(--blue);
        }
        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0; right: 0;
            width: 40px; height: 40px;
            border-bottom: 2px solid var(--blue);
            border-right: 2px solid var(--blue);
        }

        .card-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            letter-spacing: .1em;
            color: #fff;
            margin-bottom: .25rem;
        }
        .card-sub {
            font-size: .8rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(150,180,200,0.5);
            margin-bottom: 2rem;
        }

        /* Error alert */
        .alert-error {
            background: rgba(232,84,84,0.1);
            border: 1px solid rgba(232,84,84,0.3);
            border-radius: 3px;
            padding: .6rem 1rem;
            font-size: .8rem;
            color: #ff6b6b;
            margin-bottom: 1.25rem;
        }

        /* Form fields */
        .field { margin-bottom: 1.25rem; }

        .field-label {
            display: block;
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: .4rem;
        }

        .field-input {
            width: 100%;
            background: rgba(0,20,40,0.6);
            border: 1px solid rgba(0,170,255,0.2);
            border-radius: 3px;
            color: #e8eaf0;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            padding: .65rem 1rem;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .field-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0,170,255,0.1), inset 0 0 20px rgba(0,170,255,0.03);
        }
        .field-input::placeholder { color: rgba(150,180,200,0.3); }

        /* Remember me */
        .remember {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.75rem;
        }
        .remember input[type="checkbox"] {
            width: 14px; height: 14px;
            accent-color: var(--blue);
            cursor: pointer;
        }
        .remember label {
            font-size: .8rem;
            letter-spacing: .05em;
            color: rgba(180,200,220,0.5);
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: transparent;
            border: 1px solid var(--blue);
            color: var(--blue);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.1rem;
            letter-spacing: .2em;
            padding: .8rem;
            border-radius: 3px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: color .3s;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--blue);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease;
            z-index: 0;
        }
        .btn-login:hover::before { transform: scaleX(1); }
        .btn-login:hover { color: #000; }
        .btn-login span { position: relative; z-index: 1; }

        .card-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .8rem;
            letter-spacing: .05em;
            color: rgba(150,180,200,0.4);
        }
        .card-footer a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }
        .card-footer a:hover { text-shadow: 0 0 10px var(--blue); }

        /* ── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 900px) {
            .layout { flex-direction: column; justify-content: center; gap: 2.5rem; overflow-y: auto; padding: 3rem 1.5rem; }
            .branding { padding-right: 0; text-align: center; }
            .brand-eyebrow { justify-content: center; }
            .brand-stats { justify-content: center; }
            .brand-tagline { margin: 0 auto 2rem; }
            .login-side { width: 100%; max-width: 380px; }
            html, body { overflow: auto; }
        }
    </style>
</head>
<body>

<div class="bg"></div>
<div class="scanlines"></div>

<!-- Floating particles -->
<div class="particles" id="particles"></div>

<div class="layout">

    <!-- Branding -->
    <div class="branding">
        <div class="brand-eyebrow">Personal Training System</div>
        <div class="brand-name">PAIN<span>?</span></div>
        <p class="brand-tagline">
            Track every rep. Schedule every session.<br>
            Dominate every goal.
        </p>
        <div class="brand-stats">
            <div class="stat">
                <span class="stat-num">100+</span>
                <span class="stat-label">Challenges</span>
            </div>
            <div class="stat">
                <span class="stat-num">Countless</span>
                <span class="stat-label">Exercises</span>
            </div>
            <div class="stat">
                <span class="stat-num">∞</span>
                <span class="stat-label">Progress</span>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-side">
        <div class="login-card">
            <div class="card-title">Access System</div>
            <div class="card-sub">Enter your credentials to continue</div>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('status'))
                <div class="alert-error" style="color:#3ecf8e;border-color:rgba(62,207,142,0.3);background:rgba(62,207,142,0.05)">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Email Address</label>
                    <input class="field-input" type="email" id="email" name="email"
                           value="{{ old('email') }}" placeholder="you@example.com"
                           required autofocus autocomplete="username">
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <input class="field-input" type="password" id="password" name="password"
                           placeholder="••••••••" required autocomplete="current-password">
                </div>

                <div class="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me logged in</label>
                </div>

                <button type="submit" class="btn-login">
                    <span>▶ &nbsp; Enter System</span>
                </button>
            </form>

            <div class="card-footer">
                No account? <a href="{{ route('register') }}">Register here</a>
            </div>
        </div>
    </div>

</div>

<script>
    // Generate floating particles
    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left = Math.random() * 100 + '%';
        p.style.width = p.style.height = (Math.random() * 3 + 1) + 'px';
        p.style.animationDuration = (Math.random() * 10 + 8) + 's';
        p.style.animationDelay = (Math.random() * 10) + 's';
        p.style.opacity = Math.random() * 0.6 + 0.2;
        container.appendChild(p);
    }
</script>

</body>
</html>
