@extends('layouts.admin')
@section('title', 'Edit Challenge')
@section('content')

<div class="page-header">
    <h1 class="page-title">Edit Challenge</h1>
    <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="{{ route('challenges.update', $challenge) }}" class="form">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Challenge Title</label>
            <input type="text" name="title" value="{{ old('title', $challenge->title) }}"
                   class="form-input @error('title') is-error @enderror" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-input" rows="3">{{ old('description', $challenge->description) }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Challenge Type</label>
                <select name="type" id="type" class="form-input" required onchange="toggleExercise(this.value)">
                    <option value="session_count"     @selected(old('type',$challenge->type)==='session_count')>Complete Sessions</option>
                    <option value="total_weight"      @selected(old('type',$challenge->type)==='total_weight')>Total Weight Lifted (kg)</option>
                    <option value="streak"            @selected(old('type',$challenge->type)==='streak')>Day Streak</option>
                    <option value="specific_exercise" @selected(old('type',$challenge->type)==='specific_exercise')>Specific Exercise Reps</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Target</label>
                <input type="number" name="target" value="{{ old('target', $challenge->target) }}"
                       class="form-input" min="1" required>
            </div>
        </div>

        <div class="form-group" id="exercise-group">
            <label class="form-label">Exercise</label>
            <select name="exercise_id" class="form-input">
                <option value="">— Select exercise —</option>
                @foreach($exercises->groupBy('category') as $cat => $exs)
                    <optgroup label="{{ $cat }}">
                        @foreach($exs as $ex)
                            <option value="{{ $ex->id }}" @selected(old('exercise_id',$challenge->exercise_id)==$ex->id)>{{ $ex->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date"
                       value="{{ old('start_date', $challenge->start_date->format('Y-m-d')) }}"
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date"
                       value="{{ old('end_date', $challenge->end_date->format('Y-m-d')) }}"
                       class="form-input" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="upcoming" @selected(old('status',$challenge->status)==='upcoming')>Upcoming</option>
                <option value="open"     @selected(old('status',$challenge->status)==='open')>Open</option>
                <option value="closed"   @selected(old('status',$challenge->status)==='closed')>Closed</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleExercise(val) {
    document.getElementById('exercise-group').style.display =
        val === 'specific_exercise' ? 'flex' : 'none';
}
toggleExercise('{{ old('type', $challenge->type) }}');
</script>

@endsection
