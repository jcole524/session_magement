<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Pain?</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:      #00aaff;
            --blue-dim:  rgba(0,170,255,0.1);
            --blue-glow: rgba(0,170,255,0.3);
            --dark:      #03080f;
            --dark-2:    #060d18;
            --dark-3:    #0a1525;
            --border:    rgba(0,170,255,0.12);
            --border-2:  rgba(0,170,255,0.25);
            --text:      #d0dde8;
            --muted:     rgba(150,180,200,0.5);
            --green:     #00ff88;
            --red:       #ff4455;
            --orange:    #ff8800;
            --font-head: 'Bebas Neue', sans-serif;
            --font-body: 'Rajdhani', sans-serif;
            --radius:    4px;
        }
        html { font-size: 15px; }
        body { background: var(--dark); color: var(--text); font-family: var(--font-body); line-height: 1.6; min-height: 100vh; }
        body::before { content: ''; position: fixed; inset: 0; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.025) 2px, rgba(0,0,0,0.025) 4px); pointer-events: none; z-index: 9999; }
        a { color: var(--blue); text-decoration: none; }
        a:hover { color: #33bbff; }

        .admin-wrapper { display: flex; min-height: 100vh; }

        .sidebar { width: 230px; min-width: 230px; background: rgba(3,8,15,0.98); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; overflow-y: auto; }

        .sidebar-brand { padding: 1.5rem 1.25rem 1.25rem; border-bottom: 1px solid var(--border); }
        .sidebar-brand a { font-family: var(--font-head); font-size: 1.8rem; letter-spacing: .1em; color: #fff; text-decoration: none; text-shadow: 0 0 20px var(--blue-glow); display: block; }
        .sidebar-brand a span { color: var(--blue); text-shadow: 0 0 15px var(--blue); }
        .sidebar-badge { display: inline-block; font-size: .6rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; background: rgba(255,136,0,.1); color: var(--orange); border: 1px solid rgba(255,136,0,.25); padding: .15rem .55rem; border-radius: 2px; margin-top: .35rem; }

        .sidebar-label { font-size: .6rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: rgba(0,170,255,.4); padding: 1rem 1.25rem .3rem; }

        .sidebar-nav { flex: 1; padding: .5rem 0; }

        .sidebar-link { display: flex; align-items: center; gap: .7rem; padding: .6rem 1.25rem; color: var(--muted); font-size: .8rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; text-decoration: none; transition: all .15s; border-left: 2px solid transparent; }
        .sidebar-link:hover { color: var(--text); background: var(--blue-dim); text-shadow: none; }
        .sidebar-link.active { color: var(--blue); border-left-color: var(--blue); background: var(--blue-dim); }
        .sidebar-link .icon { font-size: .9rem; width: 18px; text-align: center; flex-shrink: 0; }

        .sidebar-footer { border-top: 1px solid var(--border); padding: 1.25rem; }
        .sidebar-user { display: flex; align-items: center; gap: .75rem; margin-bottom: .9rem; }
        .sidebar-avatar { width: 34px; height: 34px; border-radius: 2px; background: var(--blue-dim); border: 1px solid var(--border-2); display: flex; align-items: center; justify-content: center; font-family: var(--font-head); font-size: .85rem; letter-spacing: .05em; color: var(--blue); flex-shrink: 0; }
        .sidebar-user-name { font-size: .85rem; font-weight: 700; color: var(--text); line-height: 1.2; letter-spacing: .03em; }
        .sidebar-user-role { font-size: .65rem; color: var(--orange); text-transform: uppercase; letter-spacing: .1em; font-weight: 700; }

        .btn-logout { background: none; border: 1px solid var(--border); color: var(--muted); font-family: var(--font-body); font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .4rem .85rem; border-radius: var(--radius); cursor: pointer; transition: all .15s; width: 100%; }
        .btn-logout:hover { border-color: var(--red); color: var(--red); }

        .admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

        .admin-topbar { height: 52px; background: rgba(3,8,15,0.95); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); }
        .admin-page-title { font-family: var(--font-head); font-size: 1.1rem; letter-spacing: .12em; color: var(--blue); text-transform: uppercase; }

        .admin-content { padding: 2rem; flex: 1; }

        .alert { padding: .75rem 1.25rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: .85rem; font-weight: 600; letter-spacing: .04em; border-left: 3px solid; }
        .alert-success { background: rgba(0,255,136,.06); border-color: var(--green); color: var(--green); }
        .alert-error   { background: rgba(255,68,85,.06); border-color: var(--red);   color: var(--red); }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.75rem; gap: 1rem; }
        .page-title  { font-family: var(--font-head); font-size: 2.2rem; letter-spacing: .08em; color: #fff; text-shadow: 0 0 30px var(--blue-glow); line-height: 1; }
        .page-subtitle { color: var(--muted); font-size: .85rem; margin-top: .35rem; }
        .header-actions { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }

        .card { background: rgba(6,13,24,0.85); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1.5rem; position: relative; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; width: 20px; height: 20px; border-top: 1px solid var(--blue); border-left: 1px solid var(--blue); }
        .card-sm { padding: 1rem 1.25rem; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .card-title { font-family: var(--font-head); font-size: 1.1rem; letter-spacing: .1em; color: #fff; }
        .card-link  { font-size: .75rem; color: var(--muted); }
        .card-link:hover { color: var(--blue); }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card { background: rgba(6,13,24,0.85); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem 1.5rem; text-align: center; position: relative; overflow: hidden; }
        .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: var(--blue); opacity: .25; }
        .stat-card.accent { border-color: var(--border-2); background: var(--blue-dim); }
        .stat-card.accent::after { opacity: 1; }
        .stat-value { font-family: var(--font-head); font-size: 2.5rem; letter-spacing: .05em; color: #fff; line-height: 1; text-shadow: 0 0 20px var(--blue-glow); }
        .stat-card.accent .stat-value { color: var(--blue); }
        .stat-label { font-size: .65rem; color: var(--muted); margin-top: .4rem; text-transform: uppercase; letter-spacing: .1em; font-weight: 600; }

        .dash-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }

        .list-item { display: flex; align-items: center; justify-content: space-between; padding: .75rem 0; border-bottom: 1px solid rgba(0,170,255,0.08); gap: 1rem; }
        .list-item:last-child { border-bottom: none; }
        .list-item-title { font-size: .9rem; font-weight: 600; color: var(--text); }
        .list-item-meta  { font-size: .75rem; color: var(--muted); margin-top: .1rem; }

        .empty-state { color: var(--muted); font-size: .85rem; padding: 1rem 0; }
        .empty-cell  { color: var(--muted); font-size: .85rem; text-align: center; padding: 2rem !important; }

        .badge { display: inline-block; font-size: .6rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .2rem .65rem; border-radius: 2px; white-space: nowrap; }
        .badge-lg { font-size: .75rem; padding: .3rem .9rem; }
        .badge-scheduled { background: rgba(0,170,255,.12); color: var(--blue); border: 1px solid rgba(0,170,255,.2); }
        .badge-active    { background: rgba(0,255,136,.1);  color: var(--green); border: 1px solid rgba(0,255,136,.2); }
        .badge-completed { background: rgba(0,255,136,.08); color: #00cc66; border: 1px solid rgba(0,255,136,.15); }
        .badge-cancelled { background: rgba(150,160,180,.08); color: var(--muted); border: 1px solid rgba(150,160,180,.15); }
        .badge-achieved  { background: rgba(0,170,255,.1); color: var(--blue); border: 1px solid var(--border); }
        .badge-inactive  { background: rgba(150,160,180,.08); color: var(--muted); border: 1px solid rgba(150,160,180,.1); }
        .tag { display: inline-block; font-size: .7rem; font-weight: 600; letter-spacing: .06em; background: var(--blue-dim); color: var(--blue); border: 1px solid var(--border); padding: .15rem .55rem; border-radius: 2px; text-transform: uppercase; }

        .table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .table th { text-align: left; font-family: var(--font-head); font-size: .7rem; letter-spacing: .12em; text-transform: uppercase; color: var(--blue); padding: .6rem 1rem; border-bottom: 1px solid var(--border-2); }
        .table td { padding: .85rem 1rem; border-bottom: 1px solid rgba(0,170,255,0.06); vertical-align: middle; color: var(--text); }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: rgba(0,170,255,0.03); }
        .actions { display: flex; gap: .4rem; align-items: center; }

        .btn { display: inline-flex; align-items: center; gap: .4rem; font-family: var(--font-body); font-size: .8rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: .5rem 1.1rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all .15s; text-decoration: none; white-space: nowrap; line-height: 1; }
        .btn-primary { background: transparent; color: var(--blue); border-color: var(--blue); position: relative; overflow: hidden; isolation: isolate; }
        .btn-primary::before { content: ''; position: absolute; inset: 0; background: var(--blue); transform: scaleX(0); transform-origin: left; transition: transform .2s ease; z-index: -1; }
        .btn-primary:hover::before { transform: scaleX(1); }
        .btn-primary:hover { color: #000; }
        .btn-outline { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-outline:hover { color: var(--text); border-color: rgba(0,170,255,.4); }
        .btn-danger { background: transparent; color: var(--red); border-color: rgba(255,68,85,.3); }
        .btn-danger:hover { background: rgba(255,68,85,.1); }
        .btn-sm { font-size: .7rem; padding: .3rem .75rem; }
        .btn-full { width: 100%; justify-content: center; }

        .form { display: flex; flex-direction: column; gap: 1.1rem; }
        .form-group { display: flex; flex-direction: column; gap: .4rem; }
        .form-row { display: flex; gap: 1rem; flex-wrap: wrap; }
        .form-row .form-group { flex: 1; min-width: 120px; }
        .form-label { font-size: .65rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--blue); }
        .form-hint { font-size: .75rem; color: var(--muted); margin-top: .2rem; }
        .form-hint-inline { font-weight: 400; text-transform: none; letter-spacing: 0; color: var(--muted); }
        .form-input { background: rgba(0,20,40,0.5); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text); font-family: var(--font-body); font-size: .95rem; font-weight: 500; padding: .6rem .9rem; width: 100%; transition: border-color .15s, box-shadow .15s; outline: none; }
        .form-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(0,170,255,0.08); }
        .form-input.is-error { border-color: var(--red); }
        .form-input:disabled { opacity: .4; cursor: not-allowed; }
        textarea.form-input { resize: vertical; }
        select.form-input option { background: #060d18; }
        .form-input::placeholder { color: rgba(150,180,200,0.25); }
        .form-actions { display: flex; gap: .75rem; margin-top: .5rem; }

        .info-list { display: grid; grid-template-columns: max-content 1fr; gap: .6rem 1.5rem; font-size: .875rem; }
        .info-list dt { color: var(--muted); font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
        .info-list dd { color: var(--text); }

        .pagination-wrap { display: flex; justify-content: center; margin-top: 1.5rem; }
        .pagination-wrap nav { display: flex; gap: .25rem; }
        .pagination-wrap a, .pagination-wrap span { padding: .35rem .75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: .75rem; font-weight: 600; letter-spacing: .06em; color: var(--muted); background: rgba(6,13,24,0.8); text-decoration: none; }
        .pagination-wrap a:hover { border-color: var(--blue); color: var(--blue); }
        .pagination-wrap [aria-current="page"] span { background: var(--blue-dim); border-color: var(--blue); color: var(--blue); }

        .notes-text { color: var(--muted); font-size: .875rem; font-style: italic; }
        .chart-wrap { padding: .5rem 0; }

        @media (max-width: 768px) { .sidebar { display: none; } }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .dash-grid { grid-template-columns: 1fr; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-wrapper">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">PAIN<span>?</span></a>
            <div class="sidebar-badge">Admin </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-label">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link @if(request()->routeIs('dashboard')) active @endif">
                <span class="icon">▣</span> Dashboard
            </a>

            <div class="sidebar-label">Management</div>
            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link @if(request()->routeIs('admin.users.*')) active @endif">
                <span class="icon">◈</span> Members
            </a>
            <a href="{{ route('sessions.index') }}"
               class="sidebar-link @if(request()->routeIs('sessions.*')) active @endif">
                <span class="icon">◷</span> All Sessions
            </a>
            <a href="{{ route('exercises.index') }}"
               class="sidebar-link @if(request()->routeIs('exercises.*')) active @endif">
                <span class="icon">◉</span> Exercise Library
            </a>
            <a href="{{ route('goals.index') }}"
               class="sidebar-link @if(request()->routeIs('goals.*')) active @endif">
                <span class="icon">◎</span> All Goals
            </a>
            <a href="{{ route('challenges.index') }}"
               class="sidebar-link @if(request()->routeIs('challenges.*')) active @endif">
                <span class="icon">◆</span> Challenges
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">▶ Sign Out</button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <span class="admin-page-title">@yield('title', 'Dashboard')</span>
            <a href="{{ route('profile.edit') }}"
               style="font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)">
                {{ auth()->user()->name }}
            </a>
        </div>

        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </div>

</div>
@stack('scripts')
</body>
</html>