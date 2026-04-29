<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'user_id', 'type', 'description',
        'target_value', 'starting_value', 'current_value',
        'target_sessions', 'current_sessions',
        'exercise_id', 'target_date', 'status',
    ];

    protected $casts = [
        'target_date'    => 'date',
        'target_value'   => 'decimal:2',
        'starting_value' => 'decimal:2',
        'current_value'  => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Progress ──────────────────────────────────────────────────────────────

    public function progressPercent(): int
    {
        return match($this->type) {
            'weight_loss'                              => $this->weightLossPercent(),
            'muscle_gain'                              => $this->muscleGainPercent(),
            'strength'                                 => $this->strengthPercent(),
            'endurance', 'flexibility', 'consistency' => $this->sessionPercent(),
            default                                    => 0,
        };
    }

    private function weightLossPercent(): int
    {
        if (!$this->starting_value || !$this->target_value || !$this->current_value) return 0;

        // Already at or below target
        if ((float)$this->current_value <= (float)$this->target_value) return 100;

        $total = (float)$this->starting_value - (float)$this->target_value;
        $done  = (float)$this->starting_value - (float)$this->current_value;

        // Start equals target — already achieved
        if ($total <= 0) return 100;

        return min(100, max(0, (int) round(($done / $total) * 100)));
    }

    private function muscleGainPercent(): int
    {
        $weightPct  = 0;
        $sessionPct = 0;

        if ($this->starting_value && $this->target_value && $this->current_value) {
            // Already at or above target
            if ((float)$this->current_value >= (float)$this->target_value) {
                $weightPct = 100;
            } else {
                $total = (float)$this->target_value - (float)$this->starting_value;
                $done  = (float)$this->current_value - (float)$this->starting_value;
                if ($total > 0) $weightPct = min(100, max(0, (int) round(($done / $total) * 100)));
            }
        }

        if ($this->target_sessions > 0) {
            $sessionPct = min(100, (int) round(($this->current_sessions / $this->target_sessions) * 100));
        }

        return (int) round(($weightPct + $sessionPct) / 2);
    }

    private function strengthPercent(): int
    {
        if (!$this->target_value || !$this->current_value) return 0;
        if ((float)$this->current_value >= (float)$this->target_value) return 100;
        return min(100, max(0, (int) round(((float)$this->current_value / (float)$this->target_value) * 100)));
    }

    private function sessionPercent(): int
    {
        if (!$this->target_sessions) return 0;
        return min(100, (int) round(($this->current_sessions / $this->target_sessions) * 100));
    }

    // ── Auto-check ────────────────────────────────────────────────────────────

    public function checkAndUpdate(): void
    {
        if (in_array($this->status, ['achieved', 'cancelled'])) return;

        match($this->type) {
            'weight_loss' => $this->checkWeightLoss(),
            'muscle_gain' => $this->checkMuscleGain(),
            'strength'    => $this->checkStrength(),
            'endurance'   => $this->checkEndurance(),
            'flexibility' => $this->checkFlexibility(),
            'consistency' => $this->checkConsistency(),
            default       => null,
        };
    }

    private function checkWeightLoss(): void
    {
        $latest = ProgressLog::forUser($this->user_id)
            ->whereNotNull('body_weight_kg')
            ->latest('log_date')
            ->first();

        if (!$latest) return;

        if ($this->status === 'pending') {
            $alreadyMet = (float)$latest->body_weight_kg <= (float)$this->target_value;

            $this->update([
                'status'         => $alreadyMet ? 'achieved' : 'active',
                'starting_value' => $latest->body_weight_kg,
                'current_value'  => $latest->body_weight_kg,
            ]);
            return;
        }

        $this->update(['current_value' => $latest->body_weight_kg]);

        if ((float)$latest->body_weight_kg <= (float)$this->target_value) {
            $this->update(['status' => 'achieved']);
        }
    }

    private function checkMuscleGain(): void
    {
        $latest = ProgressLog::forUser($this->user_id)
            ->whereNotNull('body_weight_kg')
            ->latest('log_date')
            ->first();

        $completedSessions = WorkoutSession::forUser($this->user_id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', $this->created_at)
            ->count();

        if (!$latest) return;

        if ($this->status === 'pending') {
            $alreadyMet = (float)$latest->body_weight_kg >= (float)$this->target_value
                && $completedSessions >= ($this->target_sessions ?? 0);

            $this->update([
                'status'           => $alreadyMet ? 'achieved' : 'active',
                'starting_value'   => $latest->body_weight_kg,
                'current_value'    => $latest->body_weight_kg,
                'current_sessions' => $completedSessions,
            ]);
            return;
        }

        $this->update([
            'current_value'    => $latest->body_weight_kg,
            'current_sessions' => $completedSessions,
        ]);

        $weightMet  = (float)$latest->body_weight_kg >= (float)$this->target_value;
        $sessionMet = $completedSessions >= ($this->target_sessions ?? 0);

        if ($weightMet && $sessionMet) {
            $this->update(['status' => 'achieved']);
        }
    }

    private function checkStrength(): void
    {
        if (!$this->exercise_id) return;

        $best = SessionExercise::where('exercise_id', $this->exercise_id)
            ->whereHas('workoutSession', fn($q) =>
                $q->forUser($this->user_id)
                  ->where('status', 'completed')
                  ->where('updated_at', '>=', $this->created_at)
            )
            ->orderByDesc('weight_kg')
            ->first();

        if (!$best) return;

        if ($this->status === 'pending') {
            $alreadyMet = (float)$best->weight_kg >= (float)$this->target_value;

            $this->update([
                'status'         => $alreadyMet ? 'achieved' : 'active',
                'starting_value' => $best->weight_kg,
                'current_value'  => $best->weight_kg,
            ]);
            return;
        }

        $this->update(['current_value' => $best->weight_kg]);

        if ((float)$best->weight_kg >= (float)$this->target_value) {
            $this->update(['status' => 'achieved']);
        }
    }

    private function checkEndurance(): void
    {
        $completed = WorkoutSession::forUser($this->user_id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', $this->created_at)
            ->count();

        if ($this->status === 'pending') {
            if ($completed === 0) return;
            $alreadyMet = $completed >= $this->target_sessions;
            $this->update([
                'status'           => $alreadyMet ? 'achieved' : 'active',
                'current_sessions' => $completed,
            ]);
            return;
        }

        $this->update(['current_sessions' => $completed]);

        if ($completed >= $this->target_sessions) {
            $this->update(['status' => 'achieved']);
        }
    }

    private function checkFlexibility(): void
    {
        $completed = WorkoutSession::forUser($this->user_id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', $this->created_at)
            ->whereHas('exercises', fn($q) =>
                $q->where('category', 'Flexibility')
            )
            ->count();

        if ($this->status === 'pending') {
            if ($completed === 0) return;
            $alreadyMet = $completed >= $this->target_sessions;
            $this->update([
                'status'           => $alreadyMet ? 'achieved' : 'active',
                'current_sessions' => $completed,
            ]);
            return;
        }

        $this->update(['current_sessions' => $completed]);

        if ($completed >= $this->target_sessions) {
            $this->update(['status' => 'achieved']);
        }
    }

    private function checkConsistency(): void
    {
        $completed = WorkoutSession::forUser($this->user_id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', $this->created_at)
            ->count();

        if ($this->status === 'pending') {
            if ($completed === 0) return;
            $alreadyMet = $completed >= $this->target_sessions;
            $this->update([
                'status'           => $alreadyMet ? 'achieved' : 'active',
                'current_sessions' => $completed,
            ]);
            return;
        }

        $this->update(['current_sessions' => $completed]);

        if ($completed >= $this->target_sessions) {
            $this->update(['status' => 'achieved']);
        }
    }

    // ── Label helpers ─────────────────────────────────────────────────────────

    public function typeLabel(): string
    {
        return match($this->type) {
            'weight_loss' => 'Weight Loss',
            'muscle_gain' => 'Muscle Gain',
            'strength'    => 'Strength',
            'endurance'   => 'Endurance',
            'flexibility' => 'Flexibility',
            'consistency' => 'Consistency',
            default       => ucfirst($this->type),
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'Pending',
            'active'    => 'Active',
            'achieved'  => 'Achieved',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }

    public function badgeClass(): string
    {
        return match($this->status) {
            'pending'   => 'badge-scheduled',
            'active'    => 'badge-active',
            'achieved'  => 'badge-achieved',
            'cancelled' => 'badge-cancelled',
            default     => 'badge-scheduled',
        };
    }
}