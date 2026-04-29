<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — Pain?</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #00aaff; --blue-dim: rgba(0,170,255,0.1); --blue-glow: rgba(0,170,255,0.3);
            --dark: #03080f; --border: rgba(0,170,255,0.15); --border-2: rgba(0,170,255,0.25);
            --text: #d0dde8; --muted: rgba(150,180,200,0.5); --red: #ff4455; --green: #00ff88;
            --font-head: 'Bebas Neue', sans-serif; --font-body: 'Rajdhani', sans-serif;
        }
        html, body { min-height: 100%; font-family: var(--font-body); background: var(--dark); color: var(--text); }
        body::before { content: ''; position: fixed; inset: 0; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.025) 2px, rgba(0,0,0,0.025) 4px); pointer-events: none; z-index: 9999; }

        .bg {
            position: fixed; inset: 0; z-index: 0;
            background: url('{{ asset('gym.webp') }}') center center / cover no-repeat;
        }
        .bg::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(3,8,15,0.98) 0%, rgba(3,8,15,0.85) 60%, rgba(3,8,15,0.7) 100%);
        }

        .layout {
            position: relative; z-index: 10;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 3rem 1.5rem;
        }

        .register-card {
            width: 100%; max-width: 520px;
            background: rgba(3,12,22,0.88);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
            position: relative;
        }
        .register-card::before { content: ''; position: absolute; top: 0; left: 0; width: 40px; height: 40px; border-top: 2px solid var(--blue); border-left: 2px solid var(--blue); }
        .register-card::after  { content: ''; position: absolute; bottom: 0; right: 0; width: 40px; height: 40px; border-bottom: 2px solid var(--blue); border-right: 2px solid var(--blue); }

        .card-eyebrow { font-size: .65rem; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--blue); margin-bottom: .4rem; }
        .card-title { font-family: var(--font-head); font-size: 2rem; letter-spacing: .1em; color: #fff; margin-bottom: .2rem; }
        .card-sub { font-size: .75rem; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: 2rem; }

        .alert-error { background: rgba(255,68,85,0.08); border: 1px solid rgba(255,68,85,0.25); border-radius: 3px; padding: .6rem 1rem; font-size: .8rem; color: #ff6b6b; margin-bottom: 1.25rem; }

        .field { margin-bottom: 1.1rem; }
        .field-row { display: flex; gap: 1rem; }
        .field-row .field { flex: 1; }
        .field-label { display: block; font-size: .65rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: var(--blue); margin-bottom: .4rem; }
        .field-label .opt { color: var(--muted); font-weight: 400; text-transform: none; letter-spacing: 0; }

        .field-input {
            width: 100%; background: rgba(0,20,40,0.55); border: 1px solid var(--border);
            border-radius: 3px; color: var(--text); font-family: var(--font-body); font-size: .95rem;
            font-weight: 500; padding: .6rem 1rem; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .field-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(0,170,255,0.08); }
        .field-input::placeholder { color: rgba(150,180,200,0.25); }
        .field-input.err { border-color: var(--red); }

        .btn-register {
            width: 100%; background: transparent; border: 1px solid var(--blue); color: var(--blue);
            font-family: var(--font-head); font-size: 1.1rem; letter-spacing: .2em;
            padding: .8rem; border-radius: 3px; cursor: pointer; position: relative;
            overflow: hidden; transition: color .3s; margin-top: .5rem;
        }
        .btn-register::before { content: ''; position: absolute; inset: 0; background: var(--blue); transform: scaleX(0); transform-origin: left; transition: transform .3s ease; z-index: 0; }
        .btn-register:hover::before { transform: scaleX(1); }
        .btn-register:hover { color: #000; }
        .btn-register span { position: relative; z-index: 1; }

        .card-footer { text-align: center; margin-top: 1.5rem; font-size: .8rem; letter-spacing: .05em; color: var(--muted); }
        .card-footer a { color: var(--blue); font-weight: 700; }
        .card-footer a:hover { text-shadow: 0 0 10px var(--blue); }

        @media (max-width: 540px) { .field-row { flex-direction: column; } }
    </style>
</head>
<body>

<div class="bg"></div>

<div class="layout">
    <div class="register-card">
        <div class="card-eyebrow">Personal Training System</div>
        <div class="card-title">Create Account</div>
        <div class="card-sub">Join the system · Begin your grind</div>

        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="field">
                <label class="field-label" for="name">Full Name</label>
                <input class="field-input @error('name') err @enderror" type="text" id="name"
                       name="name" value="{{ old('name') }}" placeholder="Juan dela Cruz" required autofocus>
            </div>

            <div class="field">
                <label class="field-label" for="email">Email Address</label>
                <input class="field-input @error('email') err @enderror" type="email" id="email"
                       name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
            </div>
 
            <div class="field-row">
                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <input class="field-input @error('password') err @enderror" type="password"
                           id="password" name="password" placeholder="Min. 8 characters" required>
                </div>
                <div class="field">
                    <label class="field-label" for="password_confirmation">Confirm</label>
                    <input class="field-input" type="password" id="password_confirmation"
                           name="password_confirmation" placeholder="Repeat password" required>
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="field-label" for="phone">Phone <span class="opt">(optional)</span></label>
                    <input class="field-input" type="text" id="phone" name="phone"
                           value="{{ old('phone') }}" placeholder="09XXXXXXXXX">
                </div>
                <div class="field">
                    <label class="field-label" for="gender">Gender <span class="opt">(optional)</span></label>
                    <select class="field-input" id="gender" name="gender">
                        <option value="">— Select —</option>
                        <option value="male"   @selected(old('gender')==='male')>Male</option>
                        <option value="female" @selected(old('gender')==='female')>Female</option>
                        <option value="other"  @selected(old('gender')==='other')>Other</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label class="field-label" for="date_of_birth">Date of Birth <span class="opt">(optional)</span></label>
                <input class="field-input" type="date" id="date_of_birth" name="date_of_birth"
                       value="{{ old('date_of_birth') }}">
            </div>

            <button type="submit" class="btn-register">
                <span>▶ &nbsp; Create Account</span>
            </button>
        </form>

        <div class="card-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</div>

</body>
</html>
