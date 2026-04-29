<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'name', 'category', 'muscle_group',
        'difficulty', 'equipment', 'instructions',
        'description', 'video_url', 'status',
    ];

    public function sessionExercises()
    {
        return $this->hasMany(SessionExercise::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function difficultyLabel(): string
    {
        return match($this->difficulty) {
            'beginner'     => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced'     => 'Advanced',
            default        => ucfirst($this->difficulty ?? 'beginner'),
        };
    }

    public function difficultyColor(): string
    {
        return match($this->difficulty) {
            'beginner'     => 'var(--green)',
            'intermediate' => 'var(--orange)',
            'advanced'     => 'var(--red)',
            default        => 'var(--green)',
        };
    }
}