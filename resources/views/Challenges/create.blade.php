@extends('layouts.admin')
@section('title', 'New Challenge')
@section('content')

<div class="page-header">
    <h1 class="page-title">New Challenge</h1>
    <a href="{{ route('challenges.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="{{ route('challenges.store') }}" class="form">
        @csrf

        <div class="form-group">
            <label class="form-label">Challenge Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="form-input @error('title') is-error @enderror"
                   placeholder="e.g. 30-Day Session Streak" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description <span class="form-hint-inline">optional</span></label>
            <textarea name="description" class="form-input" rows="3"
                      placeholder="Describe what members need to do...">{{ old('description') }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Challenge Type</label>
                <select name="type" id="type" class="form-input @error('type') is-error @enderror"
                        required onchange="toggleExercise(this.value)">
                    <option value="">— Select —</option>
                    <option value="session_count"     @selected(old('type')==='session_count')>Complete Sessions</option>
                    <option value="total_weight"      @selected(old('type')==='total_weight')>Total Weight Lifted (kg)</option>
                    <option value="streak"            @selected(old('type')==='streak')>Day Streak</option>
                    <option value="specific_exercise" @selected(old('type')==='specific_exercise')>Specific Exercise Reps</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Target</label>
                <input type="number" name="target" value="{{ old('target') }}"
                       class="form-input @error('target') is-error @enderror"
                       placeholder="e.g. 10" min="1" required>
            </div>
        </div>

        <div class="form-group" id="exercise-group" style="display:none">
            <label class="form-label">Exercise <span class="form-hint-inline">required for specific exercise type</span></label>
            <select name="exercise_id" class="form-input">
                <option value="">— Select exercise —</option>
                @foreach($exercises->groupBy('category') as $cat => $exs)
                    <optgroup label="{{ $cat }}">
                        @foreach($exs as $ex)
                            <option value="{{ $ex->id }}" @selected(old('exercise_id')==$ex->id)>{{ $ex->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="form-input @error('start_date') is-error @enderror" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="form-input @error('end_date') is-error @enderror" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Challenge</button>
            <a href="{{ route('challenges.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleExercise(val) {
    document.getElementById('exercise-group').style.display =
        val === 'specific_exercise' ? 'flex' : 'none';
}
toggleExercise('{{ old('type') }}');
</script>

@endsection
