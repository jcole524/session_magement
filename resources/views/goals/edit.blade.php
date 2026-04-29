@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Edit Goal')
@section('content')

<div class="page-header">
    <h1 class="page-title">Edit Goal</h1>
    <a href="{{ route('goals.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:580px">

    <div style="padding:.75rem 1rem;background:rgba(255,136,0,0.06);border:1px solid rgba(255,136,0,0.2);
                border-radius:4px;font-size:.8rem;color:var(--orange);margin-bottom:1.5rem">
        ⚠ Goal type and targets cannot be changed after creation. Only description and target date are editable.
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-bottom:1.5rem;
                padding:1rem;background:rgba(0,20,40,0.4);border-radius:4px">
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Type</div>
            <div style="font-size:.9rem;color:var(--text);margin-top:.2rem">{{ $goal->typeLabel() }}</div>
        </div>
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Status</div>
            <div style="margin-top:.2rem">
                <span class="badge {{ $goal->badgeClass() }}">{{ $goal->statusLabel() }}</span>
            </div>
        </div>
        @if($goal->target_value)
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Target Value</div>
            <div style="font-size:.9rem;color:var(--text);margin-top:.2rem">{{ $goal->target_value }} kg</div>
        </div>
        @endif
        @if($goal->target_sessions)
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Target Sessions</div>
            <div style="font-size:.9rem;color:var(--text);margin-top:.2rem">{{ $goal->target_sessions }}</div>
        </div>
        @endif
        @if($goal->exercise)
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Exercise</div>
            <div style="font-size:.9rem;color:var(--text);margin-top:.2rem">{{ $goal->exercise->name }}</div>
        </div>
        @endif
        @if($goal->current_value)
        <div>
            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);font-weight:700">Current Progress</div>
            <div style="font-size:.9rem;color:var(--blue);margin-top:.2rem;font-weight:700">
                {{ $goal->progressPercent() }}%
            </div>
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('goals.update', $goal) }}" class="form">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-input" rows="3"
                      placeholder="Add notes about this goal...">{{ old('description', $goal->description) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Target Date <span class="form-hint-inline">optional</span></label>
            <input type="date" name="target_date"
                   value="{{ old('target_date', $goal->target_date?->format('Y-m-d')) }}"
                   class="form-input">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

@endsection