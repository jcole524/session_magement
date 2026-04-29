<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — PT-SSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">

<div class="auth-container">
    <div class="auth-brand">
        <span class="brand-icon-lg">⚡</span>
        <h1 class="auth-title">PT<span class="brand-accent">SSMS</span></h1>
        <p class="auth-subtitle">Personal Workout &amp; Session Scheduling</p>
    </div>
    <div class="auth-card">
        @yield('content')
    </div>
</div>

</body>
</html>