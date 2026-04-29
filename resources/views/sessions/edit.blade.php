@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Edit Session')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Session</h1>
    <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ route('sessions.update', $session) }}" class="form">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Session Title</label>
            <input type="text" name="title" value="{{ old('title', $session->title) }}"
                   class="form-input @error('title') is-error @enderror" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="session_date"
                       value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}"
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time"
                       value="{{ old('start_time', $session->start_time) }}"
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time"
                       value="{{ old('end_time', $session->end_time) }}"
                       class="form-input">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="scheduled"  @selected(old('status',$session->status)=='scheduled')>Scheduled</option>
                <option value="completed"  @selected(old('status',$session->status)=='completed')>Completed</option>
                <option value="cancelled"  @selected(old('status',$session->status)=='cancelled')>Cancelled</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-input" rows="3">{{ old('notes', $session->notes) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
