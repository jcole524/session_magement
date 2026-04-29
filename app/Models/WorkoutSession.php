<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    protected $table = 'workout_sessions'; // ← add this line

    protected $fillable = [
        'user_id', 'title', 'session_date',
        'start_time', 'end_time', 'notes', 'status',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'session_exercises')
                    ->withPivot('sets', 'reps', 'weight_kg', 'duration_mins', 'notes')
                    ->withTimestamps();
    }

    public function sessionExercises()
    {
        return $this->hasMany(SessionExercise::class, 'session_id');
    }

    public function progressLog()
    {
        return $this->hasOne(ProgressLog::class, 'session_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNotCancelled($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}