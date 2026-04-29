@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Exercise Library')
@section('content')

<div class="page-header">
    <h1 class="page-title">Exercise Library</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('exercises.create') }}" class="btn btn-primary">+ Add Exercise</a>
    @endif
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Muscle Group</th>
                <th>Difficulty</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exercises as $ex)
            <tr>
                <td>
                    @if($ex->video_url)
                        <a href="{{ $ex->video_url }}" target="_blank"
                           style="font-weight:700;color:var(--text);text-decoration:none;transition:color .15s"
                           onmouseover="this.style.color='var(--blue)'"
                           onmouseout="this.style.color='var(--text)'">
                            {{ $ex->name }}
                            <span style="font-size:.65rem;color:var(--red);margin-left:.35rem">▶ YouTube</span>
                        </a>
                    @else
                        <a href="https://www.youtube.com/results?search_query={{ urlencode($ex->name . ' exercise tutorial') }}"
                           target="_blank"
                           style="font-weight:700;color:var(--text);text-decoration:none;transition:color .15s"
                           onmouseover="this.style.color='var(--blue)'"
                           onmouseout="this.style.color='var(--text)'">
                            {{ $ex->name }}
                            <span style="font-size:.65rem;color:var(--muted);margin-left:.35rem">▶ Search</span>
                        </a>
                    @endif
                </td>
                <td>{{ $ex->category }}</td>
                <td>{{ $ex->muscle_group ?? '—' }}</td>
                <td>
                    <span class="badge"
                          style="background:{{ $ex->difficultyColor() }}22;
                                 color:{{ $ex->difficultyColor() }};
                                 border:1px solid {{ $ex->difficultyColor() }}44">
                        {{ $ex->difficultyLabel() }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $ex->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($ex->status) }}
                    </span>
                </td>
                <td class="actions">
                    <a href="{{ route('exercises.show', $ex) }}" class="btn btn-sm btn-outline">Guide</a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('exercises.edit', $ex) }}" class="btn btn-sm btn-outline">Edit</a>

                        @if($ex->status === 'active')
                            <form method="POST" action="{{ route('exercises.destroy', $ex) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Deactivate this exercise?')">
                                    Deactivate
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('exercises.activate', $ex) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm"
                                        style="background:var(--green);color:#000;border-color:var(--green)">
                                    Activate
                                </button>
                            </form>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-cell">No exercises found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $exercises->links() }}</div>
</div>

@endsection