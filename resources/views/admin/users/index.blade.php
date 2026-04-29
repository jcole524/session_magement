@extends('layouts.admin')
@section('title', 'Members')
@section('content')

<div class="page-header">
    <h1 class="page-title">Member Accounts</h1>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->isAdmin() ? 'badge-achieved' : 'badge-scheduled' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td class="actions">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm">View</a>
                    @if(!$user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm {{ $user->status === 'active' ? 'btn-danger' : 'btn-outline' }}"
                                    onclick="return confirm('Change status?')">
                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-cell">No members found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</div>
@endsection