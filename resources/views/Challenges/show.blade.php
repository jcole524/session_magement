@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', $challenge->title)
@section('content')

@php $joined = !auth()->user()->isAdmin() && $challenge->isJoinedBy(auth()->user()); @endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $challenge->title }}</h1>
        <p class="page-subtitle">
            {{ $challenge->typeLabel() }} ·
            {{ $challenge->start_date->format('M d') }} – {{ $challenge->end_date->format('M d, Y') }}
        </p>
    </div>
    <div class="header-actions">
        <span class="badge badge-lg @if($challenge->status === 'open') badge-active @elseif($challenge->status === 'upcoming') badge-scheduled @else badge-cancelled @endif">
            {{ ucfirst($challenge->status) }}
        </span>
        @if(!auth()->user()->isAdmin())
            @if($challenge->status === 'open' && !$joined)
                <form method="POST" action="{{ route('challenges.join', $challenge) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Join Challenge</button>
                </form>
            @elseif($joined && $challenge->status === 'open')
                <form method="POST" action="{{ route('challenges.leave', $challenge) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Leave this challenge?')">Leave</button>
                </form>
            @endif
        @endif
        @if(auth()->user()->isAdmin())
            <a href="{{ route('challenges.edit', $challenge) }}" class="btn btn-outline">Edit</a>
        @endif
        <a href="{{ route('challenges.index') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="dash-grid">

    {{-- Challenge Info --}}
    <div class="card">
        <div class="card-header"><h2 class="card-title">Challenge Details</h2></div>
        <dl class="info-list">
            <dt>Type</dt><dd>{{ $challenge->typeLabel() }}</dd>
            <dt>Target</dt><dd style="color:var(--blue);font-weight:600">{{ $challenge->targetLabel() }}</dd>
            <dt>Start</dt><dd>{{ $challenge->start_date->format('F d, Y') }}</dd>
            <dt>End</dt><dd>{{ $challenge->end_date->format('F d, Y') }}</dd>
            @if($challenge->exercise)
                <dt>Exercise</dt><dd>{{ $challenge->exercise->name }}</dd>
            @endif
            <dt>Participants</dt><dd>{{ $challenge->participants->count() }}</dd>
            <dt>Created by</dt><dd>{{ $challenge->creator->name }}</dd>
        </dl>
        @if($challenge->description)
            <p style="margin-top:1rem;font-size:.875rem;color:var(--muted);line-height:1.6;border-top:1px solid var(--border);padding-top:.75rem">
                {{ $challenge->description }}
            </p>
        @endif
    </div>

    {{-- My Progress --}}
    @if($joined && $myParticipant)
    <div class="card">
        <div class="card-header"><h2 class="card-title">My Progress</h2></div>
        @php $pct = $myParticipant->percentComplete(); @endphp

        <div style="text-align:center;padding:1rem 0">
            <div style="font-family:var(--font-head);font-size:3.5rem;color:var(--blue);letter-spacing:.05em;line-height:1">
                {{ $myParticipant->progress }}
            </div>
            <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin:.25rem 0 1.5rem">
                of {{ $challenge->target }} {{ $challenge->typeLabel() }}
            </div>

            <div style="height:8px;background:rgba(0,170,255,0.1);border-radius:4px;overflow:hidden;margin-bottom:.5rem">
                <div style="height:100%;width:{{ $pct }}%;background:var(--blue);border-radius:4px;transition:width .4s"></div>
            </div>
            <div style="font-size:.75rem;color:var(--blue);font-weight:700;letter-spacing:.08em">{{ $pct }}% Complete</div>

            @if($myParticipant->isCompleted())
                <div style="margin-top:1rem;padding:.6rem 1rem;background:rgba(0,255,136,.08);border:1px solid rgba(0,255,136,.2);border-radius:4px;color:var(--green);font-size:.8rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase">
                    Challenge Completed!
                </div>
            @elseif($challenge->status === 'open')
                <div style="margin-top:1rem;font-size:.75rem;color:var(--muted)">
                    {{ $challenge->target - $myParticipant->progress }} more to go
                </div>
            @endif
        </div>
    </div>
    @elseif(!auth()->user()->isAdmin() && !$joined)
    <div class="card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2.5rem">
        <div style="font-family:var(--font-head);font-size:1.2rem;color:var(--muted);letter-spacing:.1em;margin-bottom:1rem">
            JOIN TO TRACK PROGRESS
        </div>
        @if($challenge->status === 'open')
        <form method="POST" action="{{ route('challenges.join', $challenge) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Join Challenge</button>
        </form>
        @endif
    </div>
    @endif

</div>

{{-- Leaderboard --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Leaderboard</h2>
        <span style="font-size:.75rem;color:var(--muted)">{{ $leaderboard->count() }} participants</span>
    </div>

    @if($leaderboard->isEmpty())
        <p class="empty-state">No participants yet.</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Progress</th>
                <th>Status</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $i => $p)
            @php $pct = $p->challenge->target > 0 ? min(100, round(($p->progress / $p->challenge->target) * 100)) : 0; @endphp
            <tr @if($p->user_id === auth()->id()) style="background:rgba(0,170,255,0.04)" @endif>
                <td>
                    @if($i === 0)
                        <span style="color:#ffd700;font-weight:700;font-family:var(--font-head);font-size:1rem">#1</span>
                    @elseif($i === 1)
                        <span style="color:#c0c0c0;font-weight:700;font-family:var(--font-head)">#2</span>
                    @elseif($i === 2)
                        <span style="color:#cd7f32;font-weight:700;font-family:var(--font-head)">#3</span>
                    @else
                        <span style="color:var(--muted)">#{{ $i + 1 }}</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight:600;color:var(--text)">{{ $p->user->name }}</span>
                    @if($p->user_id === auth()->id())
                        <span style="font-size:.65rem;color:var(--blue);margin-left:.35rem;font-weight:700">(You)</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <div style="width:100px;height:4px;background:rgba(0,170,255,0.1);border-radius:2px;overflow:hidden">
                            <div style="height:100%;width:{{ $pct }}%;background:var(--blue);border-radius:2px"></div>
                        </div>
                        <span style="font-size:.8rem;color:var(--text);font-weight:600">
                            {{ $p->progress }} / {{ $p->challenge->target }}
                        </span>
                    </div>
                </td>
                <td>
                    @if($p->isCompleted())
                        <span class="badge badge-active">Completed</span>
                    @else
                        <span class="badge badge-scheduled">In Progress</span>
                    @endif
                </td>
                <td style="font-size:.8rem;color:var(--muted)">{{ $p->joined_at?->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
