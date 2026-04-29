@extends('layouts.admin')
@section('title','Add Exercise')
@section('content')

<div class="page-header">
    <h1 class="page-title">Add Exercise</h1>
    <a href="{{ route('exercises.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:640px">
    <form method="POST" action="{{ route('exercises.store') }}" class="form">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Exercise Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') is-error @enderror"
                       placeholder="e.g. Bench Press" required>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category"
                        class="form-input @error('category') is-error @enderror"
                        required>
                    <option value="">— Select —</option>
                    <option value="Strength"    @selected(old('category')==='Strength')>Strength</option>
                    <option value="Cardio"      @selected(old('category')==='Cardio')>Cardio</option>
                    <option value="Core"        @selected(old('category')==='Core')>Core</option>
                    <option value="Flexibility" @selected(old('category')==='Flexibility')>Flexibility</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Muscle Group <span class="form-hint-inline">optional</span></label>
                <input type="text" name="muscle_group" value="{{ old('muscle_group') }}"
                       class="form-input" placeholder="e.g. Chest, Legs, Core">
            </div>
            <div class="form-group">
                <label class="form-label">Difficulty</label>
                <select name="difficulty" class="form-input" required>
                    <option value="beginner"     @selected(old('difficulty')==='beginner')>Beginner</option>
                    <option value="intermediate" @selected(old('difficulty')==='intermediate')>Intermediate</option>
                    <option value="advanced"     @selected(old('difficulty')==='advanced')>Advanced</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Equipment <span class="form-hint-inline">optional</span></label>
            <input type="text" name="equipment" value="{{ old('equipment') }}"
                   class="form-input" placeholder="e.g. Barbell, Dumbbells, Bodyweight">
        </div>

        <div class="form-group">
            <label class="form-label">YouTube Video URL <span class="form-hint-inline">optional</span></label>
            <input type="url" name="video_url" value="{{ old('video_url') }}"
                   class="form-input"
                   placeholder="https://www.youtube.com/watch?v=...">
            <p class="form-hint">Paste a specific YouTube link. If empty, clicking the exercise name will search YouTube automatically.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Description <span class="form-hint-inline">optional</span></label>
            <textarea name="description" class="form-input" rows="2"
                      placeholder="Brief description of the exercise...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Step-by-Step Instructions <span class="form-hint-inline">optional</span></label>
            <textarea name="instructions" class="form-input" rows="8"
                      placeholder="Enter each step on a new line:&#10;1. Lie flat on the bench...&#10;2. Grip the bar...&#10;3. Lower the bar to your chest...">{{ old('instructions') }}</textarea>
            <p class="form-hint">Enter each step on a new line. They will be displayed as a numbered list.</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Exercise</button>
            <a href="{{ route('exercises.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

@endsection