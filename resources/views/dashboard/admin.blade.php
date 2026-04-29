@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<style>
.stat-link { text-decoration: none; display: block; }
.stat-link .stat-card { transition: border-color .2s, transform .15s; }
.stat-link:hover .stat-card { border-color: rgba(0,170,255,0.5); transform: translateY(-2px); }
</style>

<div class="stats-grid" style="grid-template-columns:repeat(5,1fr)">

    <a href="{{ route('admin.users.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Members</div>
        </div>
    </a>

    <a href="{{ route('admin.users.index') }}?status=active" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['active_users'] }}</div>
            <div class="stat-label">Active Members</div>
        </div>
    </a>

    <a href="{{ route('sessions.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_sessions'] }}</div>
            <div class="stat-label">All Sessions</div>
        </div>
    </a>

    <a href="{{ route('exercises.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_exercises'] }}</div>
            <div class="stat-label">Exercises </div>
        </div>
    </a>

    <a href="{{ route('challenges.index') }}" class="stat-link">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_challenges'] }}</div>
            <div class="stat-label">Active Challenges</div>
        </div>
    </a>

</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Sessions (All Members)</h2>
        <a href="{{ route('sessions.index') }}" class="card-link">View all →</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Session</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSessions as $s)
            <tr>
                <td>{{ $s->user->name }}</td>
                <td>{{ $s->title }}</td>
                <td>{{ $s->session_date->format('M d, Y') }}</td>
                <td>
                    <span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-cell">No sessions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection