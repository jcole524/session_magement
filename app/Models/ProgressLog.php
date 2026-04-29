<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'body_weight_kg', 'notes', 'log_date',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(WorkoutSession::class, 'session_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}