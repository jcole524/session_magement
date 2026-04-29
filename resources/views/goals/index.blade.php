@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', auth()->user()->isAdmin() ? 'All Goals' : 'My Goals')
@section('content')

<div class="page-header">
    <h1 class="page-title">{{ auth()->user()->isAdmin() ? 'All Goals' : 'My Goals' }}</h1>
    @if(!auth()->user()->isAdmin())
        <a href="{{ route('goals.create') }}" class="btn btn-primary">+ New Goal</a>
    @endif
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                @if(auth()->user()->isAdmin())
                    <th>Member</th>
                @endif
                <th>Type</th>
                <th>Description</th>
                <th>Progress</th>
                <th>Target Date</th>
                <th>Status</th>
                @if(!auth()->user()->isAdmin())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($goals as $goal)
            @php $pct = $goal->progressPercent(); @endphp
            <tr>
                @if(auth()->user()->isAdmin())
                    <td>
                        <div style="font-size:.875rem;font-weight:600;color:var(--text)">
                            {{ $goal->user->name }}
                        </div>
                        <div style="font-size:.75rem;color:var(--muted)">
                            {{ $goal->user->email }}
                        </div>
                    </td>
                @endif

                <td><span class="tag">{{ $goal->typeLabel() }}</span></td>

                <td>
                    <div style="font-size:.875rem;color:var(--text)">
                        {{ Str::limit($goal->description, 60) ?: '—' }}
                    </div>
                    @if(in_array($goal->type, ['weight_loss', 'muscle_gain']))
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem">
                            Target: {{ $goal->target_value }} kg
                            @if($goal->starting_value) · Start: {{ $goal->starting_value }} kg @endif
                            @if($goal->current_value)  · Now: {{ $goal->current_value }} kg @endif
                        </div>
                    @elseif($goal->type === 'strength')
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem">
                            {{ $goal->exercise?->name }} · Target: {{ $goal->target_value }} kg
                            @if($goal->current_value) · Best: {{ $goal->current_value }} kg @endif
                        </div>
                    @elseif(in_array($goal->type, ['endurance','flexibility','consistency']))
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem">
                            Sessions: {{ $goal->current_sessions }} / {{ $goal->target_sessions }}
                        </div>
                    @endif
                    @if($goal->type === 'muscle_gain' && $goal->target_sessions)
                        <div style="font-size:.75rem;color:var(--muted)">
                            Sessions: {{ $goal->current_sessions }} / {{ $goal->target_sessions }}
                        </div>
                    @endif
                </td>

                <td style="min-width:130px">
                    @if($goal->status !== 'pending')
                        <div style="height:5px;background:rgba(0,170,255,0.1);border-radius:3px;overflow:hidden;margin-bottom:.3rem">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? 'var(--green)' : 'var(--blue)' }};border-radius:3px;transition:width .3s"></div>
                        </div>
                        <span style="font-size:.7rem;color:var(--muted)">{{ $pct }}%</span>
                    @else
                        <span style="font-size:.75rem;color:var(--muted);font-style:italic">Waiting to activate...</span>
                    @endif
                </td>

                <td style="font-size:.8rem;color:var(--muted)">
                    {{ $goal->target_date ? $goal->target_date->format('M d, Y') : '—' }}
                </td>

                <td>
                    <span class="badge {{ $goal->badgeClass() }}">{{ $goal->statusLabel() }}</span>
                </td>

                @if(!auth()->user()->isAdmin())
                <td class="actions">
                    @if(!in_array($goal->status, ['achieved', 'cancelled']))
                        <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="{{ route('goals.cancel', $goal) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Cancel this goal?')">Cancel</button>
                        </form>
                    @else
                        <span style="font-size:.75rem;color:var(--muted)">—</span>
                    @endif
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->isAdmin() ? 6 : 7 }}" class="empty-cell">
                    @if(auth()->user()->isAdmin())
                        No goals found.
                    @else
                        No goals yet. <a href="{{ route('goals.create') }}">Create your first goal →</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $goals->links() }}</div>
</div>

@endsection