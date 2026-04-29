<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionExercise extends Model
{
    protected $table = 'session_exercises';

    protected $fillable = [
        'session_id', 'exercise_id',
        'sets', 'reps', 'weight_kg', 'duration_mins', 'notes',
    ];

    public function workoutSession()
    {
        return $this->belongsTo(WorkoutSession::class, 'session_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}