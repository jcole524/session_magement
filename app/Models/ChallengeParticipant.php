<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    protected $fillable = [
        'challenge_id', 'user_id', 'progress', 'joined_at', 'completed_at',
    ];

    protected $casts = [
        'joined_at'    => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function percentComplete(): int
    {
        if ($this->challenge->target <= 0) return 0;
        return min(100, (int) round(($this->progress / $this->challenge->target) * 100));
    }
}
