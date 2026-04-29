<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ChallengeController;
use App\Models\Challenge;
use App\Models\Goal;
use App\Models\WorkoutSession;
use Closure;
use Illuminate\Http\Request;

class AutoUpdateSessions
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $user    = auth()->user();
            $now     = now();
            $today   = $now->toDateString();
            $timeNow = $now->format('H:i:s');

            // Auto-activate: scheduled → active when start_time reached
            $activated = WorkoutSession::forUser($user->id)
                ->where('status', 'scheduled')
                ->where(function ($q) use ($today, $timeNow) {
                    // Past days — activate all
                    $q->where('session_date', '<', $today)
                      ->orWhere(function ($q2) use ($today, $timeNow) {
                          // Today — only if start_time has passed
                          $q2->where('session_date', $today)
                             ->where('start_time', '<=', $timeNow);
                      });
                })
                ->update(['status' => 'active']);

            // Auto-complete: active → completed when end_time reached
            $completed = WorkoutSession::forUser($user->id)
                ->whereIn('status', ['scheduled', 'active'])
                ->whereNotNull('end_time')
                ->where(function ($q) use ($today, $timeNow) {
                    // Past days — complete all
                    $q->where('session_date', '<', $today)
                      ->orWhere(function ($q2) use ($today, $timeNow) {
                          // Today — only if end_time has passed
                          $q2->where('session_date', $today)
                             ->where('end_time', '<=', $timeNow);
                      });
                })
                ->update(['status' => 'completed']);

            // Trigger challenge and goal checks if anything was completed
            if ($completed > 0 || $activated > 0) {
                $challenges = Challenge::open()
                    ->whereHas('participants', fn($q) =>
                        $q->where('user_id', $user->id)
                    )
                    ->get();

                foreach ($challenges as $challenge) {
                    ChallengeController::recalculateProgress($challenge, $user);
                }

                Goal::forUser($user->id)
                    ->whereNotIn('status', ['achieved', 'cancelled'])
                    ->get()
                    ->each->checkAndUpdate();
            }
        }

        return $next($request);
    }
}