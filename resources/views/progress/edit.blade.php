@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Edit Progress')
@section('content')

<div class="page-header">
    <h1 class="page-title">Edit Progress</h1>
    <a href="{{ route('progress.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:540px">
    <form method="POST" action="{{ route('progress.update', $progress) }}" class="form">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Date</label>
            <input type="date" name="log_date"
                   value="{{ $progress->log_date->format('Y-m-d') }}"
                   class="form-input"
                   readonly
                   style="opacity:.5;cursor:not-allowed">
        </div>

        <div class="form-group">
            <label class="form-label">Body Weight (kg)</label>
            <input type="number" name="body_weight_kg" step="0.1" min="1" max="500"
                   value="{{ old('body_weight_kg', $progress->body_weight_kg) }}"
                   class="form-input @error('body_weight_kg') is-error @enderror"
                   placeholder="e.g. 72.5" required>
            <p class="form-hint">Your current body weight. Used to track Weight Loss and Muscle Gain goals automatically.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Notes <span class="form-hint-inline">optional</span></label>
            <textarea name="notes" class="form-input" rows="3"
                      placeholder="How are you feeling? Any PRs today?">{{ old('notes', $progress->notes) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Link to Session <span class="form-hint-inline">optional</span></label>
            <select name="session_id" class="form-input">
                <option value="">— No session —</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}"
                            @selected(old('session_id', $progress->session_id)==$session->id)>
                        {{ $session->title }} — {{ $session->session_date->format('M d, Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('progress.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

@endsection