<?php

namespace App\Observers;

use App\Http\Controllers\ChallengeController;
use App\Models\Challenge;
use App\Models\Goal;
use App\Models\WorkoutSession;

class SessionObserver
{
    public function updated(WorkoutSession $session): void
    {
        if ($session->isDirty('status') && $session->status === 'completed') {
            $user = $session->user;

            // Update challenge progress
            $challenges = Challenge::open()
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->get();

            foreach ($challenges as $challenge) {
                ChallengeController::recalculateProgress($challenge, $user);
            }

            // Update goal progress
            Goal::forUser($user->id)
                ->whereNotIn('status', ['achieved', 'cancelled'])
                ->get()
                ->each->checkAndUpdate();
        }
    }
}