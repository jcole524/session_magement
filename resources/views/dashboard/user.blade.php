@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<style>
.stat-link { text-decoration: none; display: block; }
.stat-link .stat-card { transition: border-color .2s, transform .15s; }
.stat-link:hover .stat-card { border-color: rgba(0,170,255,0.5); transform: translateY(-2px); }
</style>

<div class="page-header">
    <h1 class="page-title">Welcome, {{ auth()->user()->name }}    </h1>
    <a href="{{ route('sessions.create') }}" class="btn btn-primary">+ New Session</a>
</div>

<div class="stats-grid">
    <a href="{{ route('sessions.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_sessions'] }}</div>
            <div class="stat-label">Total Sessions</div>
        </div>
    </a>

    <a href="{{ route('sessions.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['completed_sessions'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </a>

    <a href="{{ route('goals.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['active_goals'] }}</div>
            <div class="stat-label">Active Goals</div>
        </div>
    </a>

    <a href="{{ route('goals.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['achieved_goals'] }}</div>
            <div class="stat-label">Goals Achieved</div>
        </div>
    </a>
</div>

<div class="dash-grid">

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Upcoming Sessions</h2>
            <a href="{{ route('sessions.index') }}" class="card-link">View all →</a>
        </div>
        @forelse($upcomingSessions as $s)
            <div class="list-item">
                <div>
                    <div class="list-item-title">{{ $s->title }}</div>
                    <div class="list-item-meta">
                        {{ $s->session_date->format('D, M d') }} · {{ $s->start_time }}
                    </div>
                </div>
                <span class="badge badge-scheduled">Scheduled</span>
            </div>
        @empty
            <p class="empty-state">No upcoming sessions. <a href="{{ route('sessions.create') }}">Schedule one →</a></p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Active Goals</h2>
            <a href="{{ route('goals.index') }}" class="card-link">View all →</a>
        </div>
        @forelse($activeGoals as $goal)
            <div class="list-item">
                <div>
                    <div class="list-item-title">{{ $goal->description }}</div>
                    <div class="list-item-meta">
                        {{ $goal->type }}
                        @if($goal->target_date) · Target: {{ $goal->target_date->format('M d, Y') }} @endif
                    </div>
                </div>
                <span class="badge badge-active">Active</span>
            </div>
        @empty
            <p class="empty-state">No goals yet. <a href="{{ route('goals.create') }}">Add one →</a></p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Completed</h2>
            <a href="{{ route('sessions.index') }}" class="card-link">View all →</a>
        </div>
        @forelse($recentSessions as $s)
            <div class="list-item">
                <div>
                    <div class="list-item-title">
                        <a href="{{ route('sessions.show', $s) }}">{{ $s->title }}</a>
                    </div>
                    <div class="list-item-meta">{{ $s->session_date->format('M d, Y') }}</div>
                </div>
                <span class="badge badge-completed">Done</span>
            </div>
        @empty
            <p class="empty-state">No completed sessions yet.</p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Progress</h2>
            <a href="{{ route('progress.index') }}" class="card-link">View all →</a>
        </div>
        @forelse($recentLogs as $log)
            <div class="list-item">
                <div>
                    <div class="list-item-title">{{ $log->log_date->format('M d, Y') }}</div>
                    @if($log->body_weight_kg)
                        <div class="list-item-meta">Weight: {{ $log->body_weight_kg }} kg</div>
                    @endif
                    @if($log->notes)
                        <div class="list-item-meta">{{ Str::limit($log->notes, 60) }}</div>
                    @endif
                </div>
            </div>
        @empty
            <p class="empty-state">No progress logs yet. <a href="{{ route('progress.create') }}">Log one →</a></p>
        @endforelse
    </div>

</div>
@endsection