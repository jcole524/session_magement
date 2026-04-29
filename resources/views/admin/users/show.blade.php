@extends('layouts.app')
@section('title', $user->name)
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $user->name }}</h1>
        <p class="page-subtitle">{{ $user->email }}</p>
    </div>
    <div class="header-actions">

        <span class="badge {{ $user->isAdmin() ? 'badge-achieved' : 'badge-scheduled' }} badge-lg">
            {{ $user->isAdmin() ? 'Admin' : 'Member' }}
        </span>

        <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }} badge-lg">
            {{ ucfirst($user->status) }}
        </span>

        {{-- Upgrade / Downgrade role --}}
        @if(!$user->isAdmin())
            <form method="POST" action="{{ route('admin.users.makeadmin', $user) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Upgrade {{ $user->name }} to Admin?')">
                    ↑ Make Admin
                </button>
            </form>
        @elseif($user->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.removeadmin', $user) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline"
                        onclick="return confirm('Downgrade {{ $user->name }} to Member?')">
                    ↓ Remove Admin
                </button>
            </form>
        @endif

        {{-- Activate / Deactivate --}}
        @if(!$user->isAdmin())
            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit"
                        class="btn {{ $user->status === 'active' ? 'btn-danger' : 'btn-outline' }}"
                        onclick="return confirm('Change account status?')">
                    {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        @endif

        {{-- Delete Account --}}
        @if(!$user->isAdmin() && $user->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Permanently delete {{ $user->name }}? This cannot be undone.')">
                    Delete Account
                </button>
            </form>
        @endif

        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="dash-grid">

    {{-- Profile Info --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem">Profile Info</h2>
        <dl class="info-list">
            <dt>Phone</dt><dd>{{ $user->phone ?? '—' }}</dd>
            <dt>Gender</dt><dd>{{ $user->gender ? ucfirst($user->gender) : '—' }}</dd>
            <dt>Date of Birth</dt><dd>{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : '—' }}</dd>
            <dt>Role</dt><dd>{{ ucfirst($user->role) }}</dd>
            <dt>Joined</dt><dd>{{ $user->created_at->format('F d, Y') }}</dd>
        </dl>
    </div>

    {{-- Goals --}}
    <div class="card">
        <div class="card-header"><h2 class="card-title">Goals</h2></div>
        @forelse($user->goals->take(5) as $goal)
            <div class="list-item">
                <div>
                    <div class="list-item-title">{{ $goal->description }}</div>
                    <div class="list-item-meta">{{ $goal->type }}</div>
                </div>
                <span class="badge badge-{{ $goal->status }}">{{ ucfirst($goal->status) }}</span>
            </div>
        @empty
            <p class="empty-state">No goals.</p>
        @endforelse
    </div>

</div>

{{-- Recent Sessions --}}
<div class="card" style="margin-bottom: 1.5rem">
    <div class="card-header"><h2 class="card-title">Recent Sessions</h2></div>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->workoutSessions as $session)
            <tr>
                <td><a href="{{ route('sessions.show', $session) }}">{{ $session->title }}</a></td>
                <td>{{ $session->session_date->format('M d, Y') }}</td>
                <td>{{ $session->start_time }}</td>
                <td><span class="badge badge-{{ $session->status }}">{{ ucfirst($session->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-cell">No sessions.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Progress & Weight History --}}
<div class="card">
    <div class="card-header"><h2 class="card-title">Progress & Weight History</h2></div>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Weight (kg)</th>
                <th>Notes</th>
                <th>Linked Session</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->progressLogs as $log)
            <tr>
                <td>{{ $log->log_date->format('M d, Y') }}</td>
                <td>{{ $log->body_weight_kg ? number_format($log->body_weight_kg, 2) . ' kg' : '—' }}</td>
                <td>{{ $log->notes ? \Str::limit($log->notes, 60) : '—' }}</td>
                <td>{{ $log->session ? $log->session->title : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-cell">No progress logs recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection