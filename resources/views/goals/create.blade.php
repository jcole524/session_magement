@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'New Goal')
@section('content')

<div class="page-header">
    <h1 class="page-title">New Goal</h1>
    <a href="{{ route('goals.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:580px">
    <form method="POST" action="{{ route('goals.store') }}" class="form" id="goal-form">
        @csrf

        <div class="form-group">
            <label class="form-label">Goal Type</label>
            <select name="type" id="goal-type"
                    class="form-input @error('type') is-error @enderror"
                    required onchange="showFields(this.value)">
                <option value="">— Select a goal type —</option>
                <option value="weight_loss" @selected(old('type')==='weight_loss')>Weight Loss</option>
                <option value="muscle_gain" @selected(old('type')==='muscle_gain')>Muscle Gain</option>
                <option value="strength"    @selected(old('type')==='strength')>Strength</option>
                <option value="endurance"   @selected(old('type')==='endurance')>Endurance</option>
                <option value="flexibility" @selected(old('type')==='flexibility')>Flexibility</option>
                <option value="consistency" @selected(old('type')==='consistency')>Consistency</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description <span class="form-hint-inline">optional</span></label>
            <textarea name="description" class="form-input" rows="2"
                      placeholder="e.g. Lose weight before summer...">{{ old('description') }}</textarea>
        </div>

        {{-- TARGET BODY WEIGHT: weight_loss, muscle_gain --}}
        <div class="form-group dyn-field" id="field-target-weight" style="display:none">
            <label class="form-label" id="label-target-weight">Target Body Weight (kg)</label>
            <input type="number" name="target_value" step="0.1" min="1"
                   value="{{ old('target_value') }}"
                   class="form-input @error('target_value') is-error @enderror"
                   placeholder="e.g. 70">
            <p class="form-hint" id="hint-target-weight"></p>
        </div>

        {{-- TARGET SESSIONS: muscle_gain only --}}
        <div class="form-group dyn-field" id="field-muscle-sessions" style="display:none">
            <label class="form-label">Sessions to Complete (alongside weight goal)</label>
            <input type="number" name="target_sessions" min="1"
                   value="{{ old('target_sessions') }}"
                   class="form-input @error('target_sessions') is-error @enderror"
                   placeholder="e.g. 20">
            <p class="form-hint">You must complete this many sessions AND hit your target weight to achieve this goal.</p>
        </div>

        {{-- EXERCISE: strength only --}}
        <div class="form-group dyn-field" id="field-exercise" style="display:none">
            <label class="form-label">Exercise to Track</label>
            <select name="exercise_id"
                    class="form-input @error('exercise_id') is-error @enderror">
                <option value="">— Select exercise —</option>
                @foreach($exercises->groupBy('category') as $cat => $exs)
                    <optgroup label="{{ $cat }}">
                        @foreach($exs as $ex)
                            <option value="{{ $ex->id }}" @selected(old('exercise_id')==$ex->id)>
                                {{ $ex->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <p class="form-hint">The system tracks your best lift for this exercise across all completed sessions.</p>
        </div>

        {{-- TARGET LIFT WEIGHT: strength only --}}
        <div class="form-group dyn-field" id="field-strength-weight" style="display:none">
            <label class="form-label">Target Weight to Lift (kg)</label>
            <input type="number" name="strength_target_value" step="0.5" min="1"
                   value="{{ old('strength_target_value') }}"
                   class="form-input @error('target_value') is-error @enderror"
                   placeholder="e.g. 100">
            <p class="form-hint">Goal activates when you first log this exercise. Achieved when your best lift hits this weight.</p>
        </div>

        {{-- TARGET SESSIONS: endurance, flexibility, consistency --}}
        <div class="form-group dyn-field" id="field-sessions" style="display:none">
            <label class="form-label" id="label-sessions">Sessions to Complete</label>
            <input type="number" name="target_sessions" min="1"
                   value="{{ old('target_sessions') }}"
                   class="form-input @error('target_sessions') is-error @enderror"
                   placeholder="e.g. 20">
            <p class="form-hint" id="hint-sessions"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Target Date <span class="form-hint-inline">optional</span></label>
            <input type="date" name="target_date"
                   value="{{ old('target_date') }}"
                   class="form-input">
        </div>

        <div id="goal-info" style="display:none;padding:.75rem 1rem;
             background:rgba(0,170,255,0.06);border:1px solid rgba(0,170,255,0.2);
             border-radius:4px;font-size:.82rem;color:var(--muted);line-height:1.7"></div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Goal</button>
            <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
const CONFIG = {
    weight_loss: {
        fields: ['field-target-weight'],
        weightLabel: 'Target Body Weight (kg)',
        weightHint:  'Enter the body weight you want to reach. Activates automatically when you first log your weight in Progress.',
        info: '⚡ Activates when you log your first body weight. Achieved when your logged weight reaches or goes below this target.',
    },
    muscle_gain: {
        fields: ['field-target-weight', 'field-muscle-sessions'],
        weightLabel: 'Target Body Weight (kg)',
        weightHint:  'Enter the body weight you want to reach through muscle building.',
        info: '⚡ Activates when you log your first body weight. Achieved when BOTH your weight reaches the target AND you complete the required sessions.',
    },
    strength: {
        fields: ['field-exercise', 'field-strength-weight'],
        info: '⚡ Activates when you first log the selected exercise in a completed session. Achieved when your best logged weight for that exercise hits your target.',
    },
    endurance: {
        fields: ['field-sessions'],
        sessionsLabel: 'Total Sessions to Complete',
        sessionsHint:  'The system counts all your completed sessions from the date this goal is created.',
        info: '⚡ Activates when you complete your first session. Achieved when your total completed sessions reaches the target.',
    },
    flexibility: {
        fields: ['field-sessions'],
        sessionsLabel: 'Flexibility Sessions to Complete',
        sessionsHint:  'Only sessions that include a Flexibility-category exercise count toward this goal.',
        info: '⚡ Activates when you complete your first Flexibility session. Achieved when your total Flexibility sessions reaches the target.',
    },
    consistency: {
        fields: ['field-sessions'],
        sessionsLabel: 'Total Sessions to Complete',
        sessionsHint:  'Complete this many sessions consistently from the date this goal is created.',
        info: '⚡ Activates when you complete your first session. Achieved when your total sessions reaches the target.',
    },
};

function showFields(type) {
    // Hide all dynamic fields and disable their inputs
    document.querySelectorAll('.dyn-field').forEach(el => {
        el.style.display = 'none';
        el.querySelectorAll('input, select').forEach(input => input.disabled = true);
    });

    const infoBox = document.getElementById('goal-info');
    infoBox.style.display = 'none';

    if (!type || !CONFIG[type]) return;

    const c = CONFIG[type];

    // Show relevant fields and enable their inputs
    c.fields.forEach(id => {
        const el = document.getElementById(id);
        el.style.display = 'flex';
        el.querySelectorAll('input, select').forEach(input => input.disabled = false);
    });

    if (c.weightLabel) {
        document.getElementById('label-target-weight').textContent = c.weightLabel;
        document.getElementById('hint-target-weight').textContent  = c.weightHint;
    }

    if (c.sessionsLabel) {
        document.getElementById('label-sessions').textContent = c.sessionsLabel;
        document.getElementById('hint-sessions').textContent  = c.sessionsHint;
    }

    infoBox.innerHTML = c.info;
    infoBox.style.display = 'block';
}

showFields('{{ old('type') }}');
</script>

@endsection