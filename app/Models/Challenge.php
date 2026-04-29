<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'created_by', 'title', 'description', 'type',
        'target', 'exercise_id', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function participants()
    {
        return $this->hasMany(ChallengeParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'challenge_participants')
                    ->withPivot('progress', 'joined_at', 'completed_at')
                    ->withTimestamps();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isJoinedBy(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function progressFor(User $user): int
    {
        $p = $this->participants()->where('user_id', $user->id)->first();
        return $p ? $p->progress : 0;
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'session_count'     => 'Complete Sessions',
            'total_weight'      => 'Total Weight (kg)',
            'streak'            => 'Day Streak',
            'specific_exercise' => 'Exercise Reps',
            default             => $this->type,
        };
    }

    public function targetLabel(): string
    {
        return match($this->type) {
            'session_count'     => $this->target . ' sessions',
            'total_weight'      => $this->target . ' kg',
            'streak'            => $this->target . ' days',
            'specific_exercise' => $this->target . ' reps',
            default             => $this->target,
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['open', 'upcoming', 'closed']);
    }

    // ── Auto-update status based on dates ─────────────────────────────────────

    public function syncStatus(): void
    {
        $today = now()->toDateString();

        if ($this->end_date->lt(today())) {
            $this->update(['status' => 'closed']);
        } elseif ($this->start_date->lte(today())) {
            $this->update(['status' => 'open']);
        }
    }
}
