<?php

namespace App\Observers;

use App\Models\Goal;
use App\Models\ProgressLog;

class ProgressLogObserver
{
    public function created(ProgressLog $log): void
    {
        $this->triggerGoalCheck($log);
    }

    public function updated(ProgressLog $log): void
    {
        $this->triggerGoalCheck($log);
    }

    private function triggerGoalCheck(ProgressLog $log): void
    {
        if (!$log->body_weight_kg) return;

        Goal::where('user_id', $log->user_id)
            ->whereNotIn('status', ['achieved', 'cancelled'])
            ->whereIn('type', ['weight_loss', 'muscle_gain'])
            ->get()
            ->each->checkAndUpdate();
    }
}