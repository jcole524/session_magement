@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', $exercise->name . ' Guide')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $exercise->name }}</h1>
        <p class="page-subtitle">
            {{ $exercise->category }}
            @if($exercise->muscle_group) · {{ $exercise->muscle_group }} @endif
        </p>
    </div>
    <div class="header-actions">
        @if($exercise->video_url)
            <a href="{{ $exercise->video_url }}" target="_blank"
               class="btn btn-primary"
               style="border-color:var(--red);color:var(--red)">
                ▶ Watch on YouTube
            </a>
        @else
            <a href="https://www.youtube.com/results?search_query={{ urlencode($exercise->name . ' exercise tutorial') }}"
               target="_blank"
               class="btn btn-outline">
                ▶ Search YouTube
            </a>
        @endif
        @if(auth()->user()->isAdmin())
            <a href="{{ route('exercises.edit', $exercise) }}" class="btn btn-outline">Edit</a>
        @endif
        <a href="{{ route('exercises.index') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="dash-grid">

    {{-- Info Card --}}
    <div class="card">
        <div class="card-header"><h2 class="card-title">Exercise Info</h2></div>
        <dl class="info-list">
            <dt>Category</dt>
            <dd><span class="tag">{{ $exercise->category }}</span></dd>

            <dt>Muscle Group</dt>
            <dd>{{ $exercise->muscle_group ?? '—' }}</dd>

            <dt>Difficulty</dt>
            <dd>
                <span class="badge"
                      style="background:{{ $exercise->difficultyColor() }}22;
                             color:{{ $exercise->difficultyColor() }};
                             border:1px solid {{ $exercise->difficultyColor() }}44">
                    {{ $exercise->difficultyLabel() }}
                </span>
            </dd>

            <dt>Equipment</dt>
            <dd>{{ $exercise->equipment ?? 'Bodyweight / None' }}</dd>

            <dt>Status</dt>
            <dd>
                <span class="badge {{ $exercise->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                    {{ ucfirst($exercise->status) }}
                </span>
            </dd>

            @if($exercise->video_url)
            <dt>Video</dt>
            <dd>
                <a href="{{ $exercise->video_url }}" target="_blank"
                   style="color:var(--red);font-weight:700">
                    ▶ Watch Tutorial
                </a>
            </dd>
            @endif
        </dl>

        @if($exercise->description)
            <div style="margin-top:1rem;padding-top:.75rem;border-top:1px solid var(--border)">
                <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;
                            color:var(--blue);font-weight:700;margin-bottom:.5rem">
                    About
                </div>
                <p style="font-size:.875rem;color:var(--muted);line-height:1.7">
                    {{ $exercise->description }}
                </p>
            </div>
        @endif
    </div>

    {{-- Tips Card --}}
    <div class="card">
        <div class="card-header"><h2 class="card-title">Quick Tips</h2></div>

        {{-- Equipment tip --}}
        @if($exercise->equipment)
        <div class="list-item">
            <div>
                <div class="list-item-title">Equipment Needed</div>
                <div class="list-item-meta">{{ $exercise->equipment }}</div>
            </div>
        </div>
        @else
        <div class="list-item">
            <div>
                <div class="list-item-title">Equipment Needed</div>
                <div class="list-item-meta">Bodyweight — no equipment required.</div>
            </div>
        </div>
        @endif

        {{-- Difficulty tip --}}
        <div class="list-item">
            <div>
                <div class="list-item-title">Difficulty Level</div>
                <div class="list-item-meta">
                    @if($exercise->difficulty === 'beginner')
                        Suitable for beginners. Focus on form before adding intensity.
                    @elseif($exercise->difficulty === 'intermediate')
                        Intermediate level. Assumes a basic fitness foundation.
                    @else
                        Advanced exercise. Ensure proper warm up and technique before attempting.
                    @endif
                </div>
            </div>
        </div>

        {{-- Category-specific tips --}}
        @if($exercise->category === 'Cardio')
            <div class="list-item">
                <div>
                    <div class="list-item-title">Track Duration</div>
                    <div class="list-item-meta">Log how many minutes you performed this exercise. No weight logging needed.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Warm Up First</div>
                    <div class="list-item-meta">Start at low intensity for the first 2-3 minutes before reaching full speed.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Stay Hydrated</div>
                    <div class="list-item-meta">Drink water before, during, and after cardio sessions.</div>
                </div>
            </div>

        @elseif($exercise->category === 'Flexibility')
            <div class="list-item">
                <div>
                    <div class="list-item-title">Hold Each Stretch</div>
                    <div class="list-item-meta">Hold each position for 20-30 seconds. Log sets and reps, no weight needed.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Never Bounce</div>
                    <div class="list-item-meta">Use slow, controlled movements. Bouncing can cause injury.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Breathe Steadily</div>
                    <div class="list-item-meta">Exhale as you deepen the stretch. Never hold your breath.</div>
                </div>
            </div>

        @elseif($exercise->category === 'Core')
            <div class="list-item">
                <div>
                    <div class="list-item-title">Engage Your Core</div>
                    <div class="list-item-meta">Pull your belly button toward your spine throughout the movement.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Control the Movement</div>
                    <div class="list-item-meta">Slow, controlled reps are more effective than fast, sloppy ones.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Don't Pull Your Neck</div>
                    <div class="list-item-meta">Keep your hands loose behind your head to avoid neck strain.</div>
                </div>
            </div>

        @else
            {{-- Strength --}}
            <div class="list-item">
                <div>
                    <div class="list-item-title">Progressive Overload</div>
                    <div class="list-item-meta">Gradually increase weight or reps each week to keep making gains.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Control the Eccentric</div>
                    <div class="list-item-meta">Lower the weight slowly (2-3 seconds) for maximum muscle growth.</div>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <div class="list-item-title">Rest Between Sets</div>
                    <div class="list-item-meta">Rest 60-90 seconds for hypertrophy, 2-3 mins for strength.</div>
                </div>
            </div>
        @endif

        {{-- Muscle group tip --}}
        @if($exercise->muscle_group)
        <div class="list-item">
            <div>
                <div class="list-item-title">Primary Muscle</div>
                <div class="list-item-meta">
                    This exercise primarily targets the {{ $exercise->muscle_group }}.
                </div>
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Instructions --}}
@if($exercise->instructions)
<div class="card">
    <div class="card-header"><h2 class="card-title">Step-by-Step Instructions</h2></div>
    @php
        $steps = array_values(array_filter(explode("\n", trim($exercise->instructions))));
    @endphp
    @if(count($steps) > 1)
        <ol style="padding-left:1.5rem;display:flex;flex-direction:column;gap:.75rem">
            @foreach($steps as $step)
                @if(trim($step))
                <li style="color:var(--text);padding-left:.5rem;font-size:.9rem;line-height:1.7">
                    {{ trim($step) }}
                </li>
                @endif
            @endforeach
        </ol>
    @else
        <p style="color:var(--text);font-size:.9rem;line-height:1.7">
            {{ $exercise->instructions }}
        </p>
    @endif
</div>
@else
    @if(auth()->user()->isAdmin())
    <div class="card" style="text-align:center;padding:2rem">
        <p style="color:var(--muted);margin-bottom:1rem">No instructions added yet.</p>
        <a href="{{ route('exercises.edit', $exercise) }}" class="btn btn-primary">
            + Add Instructions
        </a>
    </div>
    @endif
@endif

@endsection