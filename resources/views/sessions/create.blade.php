@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Schedule Session')

@section('content')
<div class="page-header">
    <h1 class="page-title">Schedule New Session</h1>
    <a href="{{ route('sessions.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ route('sessions.store') }}" class="form">
        @csrf

        <div class="form-group">
            <label class="form-label">Session Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="form-input @error('title') is-error @enderror"
                   placeholder="e.g. Morning Push Day" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="session_date" value="{{ old('session_date') }}"
                       class="form-input @error('session_date') is-error @enderror" required>
            </div>
            <div class="form-group">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="form-input @error('start_time') is-error @enderror" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Time (optional)</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                       class="form-input">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes (optional)</label>
            <textarea name="notes" class="form-input" rows="3"
                      placeholder="Any notes about this session...">{{ old('notes') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Schedule Session</button>
            <a href="{{ route('sessions.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
