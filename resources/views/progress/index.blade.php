@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Progress Tracker')
@section('content')

<div class="page-header">
    <h1 class="page-title">My Progress</h1>
    <a href="{{ route('progress.create') }}" class="btn btn-primary">+ Log Progress</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Body Weight</th>
                <th>Session</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->log_date->format('M d, Y') }}</td>
                <td>{{ $log->body_weight_kg ? $log->body_weight_kg.' kg' : '—' }}</td>
                <td>
                    @if($log->workoutSession)
                        <a href="{{ route('sessions.show', $log->workoutSession) }}">
                            {{ $log->workoutSession->title }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $log->notes ? Str::limit($log->notes, 60) : '—' }}</td>
                <td class="actions">
                    <a href="{{ route('progress.edit', $log) }}" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" action="{{ route('progress.destroy', $log) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this log?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-cell">
                    No progress logs yet. <a href="{{ route('progress.create') }}">Log your first entry →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</div>

@endsection 