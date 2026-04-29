@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Sessions')

@section('content')
<div class="page-header">
    <h1 class="page-title">Workout Sessions</h1>
    @if(!auth()->user()->isAdmin())
        <a href="{{ route('sessions.create') }}" class="btn btn-primary">+ New Session</a>
    @endif
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                @if(auth()->user()->isAdmin())<th>Member</th>@endif
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                @if(auth()->user()->isAdmin())
                    <td>{{ $session->user->name }}</td>
                @endif
                <td><a href="{{ route('sessions.show', $session) }}">{{ $session->title }}</a></td>
                <td>{{ $session->session_date->format('M d, Y') }}</td>
                <td>{{ $session->start_time }}@if($session->end_time) – {{ $session->end_time }}@endif</td>
                <td><span class="badge badge-{{ $session->status }}">{{ ucfirst($session->status) }}</span></td>
                <td class="actions">
                    <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm">View</a>
                    @if(!auth()->user()->isAdmin() && $session->status !== 'cancelled')
                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-sm btn-outline">Edit</a>
                        @if($session->status !== 'cancelled')
                        <form method="POST" action="{{ route('sessions.cancel', $session) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Cancel this session?')">Cancel</button>
                        </form>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-cell">No sessions found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">{{ $sessions->links() }}</div>
</div>
@endsection
