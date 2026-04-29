<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Goal;
use App\Models\ProgressLog;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $user->isAdmin()
            ? $this->adminDashboard()
            : $this->userDashboard($user);
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users'      => User::where('role', 'user')->count(),
            'active_users'     => User::where('role', 'user')->where('status', 'active')->count(),
            'total_sessions'   => WorkoutSession::count(),
            'total_exercises'  => Exercise::count(),
            'total_challenges' => \App\Models\Challenge::where('status', 'open')->count(),
        ];

        $recentSessions = WorkoutSession::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentSessions'));
    }

    private function userDashboard(User $user)
    {
        $upcomingSessions = WorkoutSession::forUser($user->id)
            ->whereIn('status', ['scheduled', 'active'])
            ->where('session_date', '>=', today())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $recentSessions = WorkoutSession::forUser($user->id)
            ->where('status', 'completed')
            ->orderByDesc('session_date')
            ->take(5)
            ->get();

        $activeGoals = Goal::forUser($user->id)
            ->active()
            ->get();

        $recentLogs = ProgressLog::forUser($user->id)
            ->orderByDesc('log_date')
            ->take(7)
            ->get();

        $stats = [
            'total_sessions'     => WorkoutSession::forUser($user->id)->count(),
            'completed_sessions' => WorkoutSession::forUser($user->id)->where('status', 'completed')->count(),
            'active_goals'       => $activeGoals->count(),
            'achieved_goals'     => Goal::forUser($user->id)->where('status', 'achieved')->count(),
        ];

        return view('dashboard.user', compact(
            'upcomingSessions', 'recentSessions', 'activeGoals', 'recentLogs', 'stats'
        ));
    }
}