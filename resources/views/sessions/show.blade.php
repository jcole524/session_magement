@extends('layouts.app')
@section('title', $session->title)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $session->title }}</h1>
        <p class="page-subtitle">{{ $session->session_date->format('F d, Y') }} · {{ $session->start_time }} – {{ $session->end_time }}</p>
    </div>
    <div class="header-actions">
        <span class="badge badge-{{ $session->status }}">{{ ucfirst($session->status) }}</span>

        @if($session->status === 'scheduled')
            <form method="POST" action="{{ route('sessions.start', $session) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">▶ Start Session</button>
            </form>
            <a href="{{ route('sessions.edit', $session) }}" class="btn btn-outline">Edit</a>
            <form method="POST" action="{{ route('sessions.cancel', $session) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Cancel this session?')">Cancel</button>
            </form>
        @endif

        @if($session->status === 'in_progress')
            <form method="POST" action="{{ route('sessions.complete', $session) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Mark session as complete?')">✓ Complete</button>
            </form>
        @endif

        <a href="{{ route('sessions.index') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($session->notes)
<div class="card" style="margin-bottom: 1.5rem">
    <h2 class="card-title" style="margin-bottom: 0.5rem">Notes</h2>
    <p style="color: var(--color-text-secondary)">{{ $session->notes }}</p>
</div>
@endif

{{-- Exercises --}}
<div class="card" style="margin-bottom: 1.5rem">
    <div class="card-header">
        <h2 class="card-title">Exercises</h2>
    </div>

    @if($session->sessionExercises->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Exercise</th>
                <th>Sets</th>
                <th>Reps</th>
                <th>Weight (kg)</th>
                <th>Duration (mins)</th>
                @if($session->status !== 'completed' && $session->status !== 'cancelled')
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($session->sessionExercises as $se)
            <tr>
                <td>{{ $se->exercise->name }}</td>
                <td>{{ $se->sets ?? '—' }}</td>
                <td>{{ $se->reps ?? '—' }}</td>
                <td>{{ $se->weight_kg ?? '—' }}</td>
                <td>{{ $se->duration_mins ?? '—' }}</td>
                @if($session->status !== 'completed' && $session->status !== 'cancelled')
                <td>
                    <form method="POST" action="{{ route('sessions.exercises.remove', [$session, $se]) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Remove this exercise?')">Remove</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p class="empty-state">No exercises added yet.</p>
    @endif
</div>

{{-- Add Exercise --}}
@if($session->status !== 'completed' && $session->status !== 'cancelled')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Add Exercise</h2>
    </div>
    <form method="POST" action="{{ route('sessions.exercises.add', $session) }}" class="form">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Exercise</label>
                <select name="exercise_id" class="form-input" required>
                    <option value="">— Select Exercise —</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}">{{ $exercise->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sets</label>
                <input type="number" name="sets" class="form-input" min="1" placeholder="e.g. 3">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Reps</label>
                <input type="number" name="reps" class="form-input" min="1" placeholder="e.g. 10">
            </div>
            <div class="form-group">
                <label class="form-label">Weight (kg)</label>
                <input type="number" name="weight_kg" class="form-input" step="0.5" min="0" placeholder="e.g. 50">
            </div>
            <div class="form-group">
                <label class="form-label">Duration (mins)</label>
                <input type="number" name="duration_mins" class="form-input" min="1" placeholder="e.g. 30">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">+ Add Exercise</button>
    </form>
</div>
@endif

@endsection