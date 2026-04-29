@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Challenges')
@section('content')

<div class="page-header">
    <h1 class="page-title">Challenges</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('challenges.create') }}" class="btn btn-primary">+ New Challenge</a>
    @endif
</div>

@if($challenges->isEmpty())
    <div class="card" style="text-align:center;padding:3rem">
        <p style="font-family:var(--font-head);font-size:1.5rem;color:var(--muted);letter-spacing:.1em">
            NO CHALLENGES YET
        </p>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('challenges.create') }}" class="btn btn-primary" style="margin-top:1rem">
                Create First Challenge
            </a>
        @endif
    </div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem">
    @foreach($challenges as $challenge)
    @php
        $joined = !auth()->user()->isAdmin() && $challenge->isJoinedBy(auth()->user());
        $progress = $joined ? $challenge->progressFor(auth()->user()) : 0;
        $pct = $challenge->target > 0 ? min(100, round(($progress / $challenge->target) * 100)) : 0;
    @endphp
    <div class="card" style="margin-bottom:0;display:flex;flex-direction:column;gap:.75rem">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
            <div>
                <div style="font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--blue);margin-bottom:.3rem">
                    {{ $challenge->typeLabel() }}
                </div>
                <div style="font-family:var(--font-head);font-size:1.2rem;letter-spacing:.06em;color:#fff">
                    {{ $challenge->title }}
                </div>
            </div>
            <span class="badge @if($challenge->status === 'open') badge-active @elseif($challenge->status === 'upcoming') badge-scheduled @else badge-cancelled @endif">
                {{ ucfirst($challenge->status) }}
            </span>
        </div>

        @if($challenge->description)
            <p style="font-size:.8rem;color:var(--muted);line-height:1.5">{{ Str::limit($challenge->description, 80) }}</p>
        @endif

        <div style="display:flex;gap:1.5rem;font-size:.75rem">
            <div>
                <div style="color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-size:.6rem;font-weight:700">Target</div>
                <div style="color:var(--text);font-weight:600;margin-top:.15rem">{{ $challenge->targetLabel() }}</div>
            </div>
            <div>
                <div style="color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-size:.6rem;font-weight:700">Period</div>
                <div style="color:var(--text);font-weight:600;margin-top:.15rem">
                    {{ $challenge->start_date->format('M d') }} – {{ $challenge->end_date->format('M d, Y') }}
                </div>
            </div>
            <div>
                <div style="color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-size:.6rem;font-weight:700">Joined</div>
                <div style="color:var(--text);font-weight:600;margin-top:.15rem">{{ $challenge->participants->count() }}</div>
            </div>
        </div>

        @if($joined)
        <div>
            <div style="display:flex;justify-content:space-between;font-size:.7rem;color:var(--muted);margin-bottom:.35rem">
                <span>Your progress</span>
                <span style="color:var(--blue)">{{ $progress }} / {{ $challenge->target }} · {{ $pct }}%</span>
            </div>
            <div style="height:4px;background:rgba(0,170,255,0.1);border-radius:2px;overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;background:var(--blue);border-radius:2px;transition:width .3s"></div>
            </div>
        </div>
        @endif

        <div style="display:flex;gap:.5rem;margin-top:auto">
            <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-outline btn-sm">View</a>

            @if(!auth()->user()->isAdmin())
                @if($challenge->status === 'open' && !$joined)
                    <form method="POST" action="{{ route('challenges.join', $challenge) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Join</button>
                    </form>
                @elseif($joined && $challenge->status === 'open')
                    <form method="POST" action="{{ route('challenges.leave', $challenge) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Leave this challenge?')">Leave</button>
                    </form>
                @endif
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('challenges.edit', $challenge) }}" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="{{ route('challenges.destroy', $challenge) }}" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this challenge?')">Delete</button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="pagination-wrap" style="margin-top:1.5rem">{{ $challenges->links() }}</div>
@endif

@endsection
