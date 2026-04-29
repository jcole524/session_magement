<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserManagementController extends Controller
{
    private function requireAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function index()
    {
        $this->requireAdmin();
        $users = User::where('role', 'user')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->requireAdmin();
        $user->load([
            'workoutSessions' => fn ($q) => $q->latest('session_date')->take(10),
            'goals',
            'progressLogs.session',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $this->requireAdmin();
        abort_if($user->isAdmin(), 403, 'Cannot change an admin account status.');

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        $msg = $user->status === 'active' ? 'User activated.' : 'User deactivated.';

        return redirect()->route('admin.users.index')->with('success', $msg);
    }

    public function destroy(User $user)
    {
        $this->requireAdmin();
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');
        abort_if($user->isAdmin(), 403, 'Cannot delete an admin account.');

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', $user->name . ' has been deleted.');
    }

    public function makeAdmin(User $user)
    {
        $this->requireAdmin();
        abort_if($user->isAdmin(), 403, 'User is already an admin.');

        $user->update(['role' => 'admin']);

        return redirect()->route('admin.users.show', $user)
                         ->with('success', $user->name . ' has been upgraded to Admin.');
    }

    public function removeAdmin(User $user)
    {
        $this->requireAdmin();
        abort_if($user->id === auth()->id(), 403, 'You cannot remove your own admin role.');

        $user->update(['role' => 'user']);

        return redirect()->route('admin.users.show', $user)
                         ->with('success', $user->name . ' has been downgraded to Member.');
    }
}