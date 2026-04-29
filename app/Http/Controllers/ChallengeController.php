<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\Exercise;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function index()
    {
        Challenge::visible()->get()->each->syncStatus();

        $challenges = Challenge::with(['creator', 'participants'])
            ->visible()
            ->latest()
            ->paginate(12);

        return view('challenges.index', compact('challenges'));
    }

    public function show(Challenge $challenge)
    {
        $challenge->syncStatus();
        $challenge->load(['participants.user', 'exercise', 'creator']);

        $leaderboard = $challenge->participants()
            ->with('user')
            ->orderByDesc('progress')
            ->orderBy('completed_at')
            ->get();

        $myParticipant = $challenge->participants()
            ->where('user_id', Auth::id())
            ->first();

        return view('challenges.show', compact('challenge', 'leaderboard', 'myParticipant'));
    }

    public function join(Challenge $challenge)
    {
        abort_if(Auth::user()->isAdmin(), 403);
        abort_if($challenge->status !== 'open', 403, 'This challenge is not open.');

        if ($challenge->isJoinedBy(Auth::user())) {
            return redirect()->route('challenges.show', $challenge)
                             ->with('error', 'You have already joined this challenge.');
        }

        ChallengeParticipant::create([
            'challenge_id' => $challenge->id,
            'user_id'      => Auth::id(),
            'progress'     => 0,
            'joined_at'    => now(),
        ]);

        $this->recalculateProgress($challenge, Auth::user());

        return redirect()->route('challenges.show', $challenge)
                         ->with('success', 'You joined the challenge!');
    }

   public function leave(Challenge $challenge)
{
    ChallengeParticipant::where('challenge_id', $challenge->id)
        ->where('user_id', Auth::id())
        ->delete();

    return redirect()->route('challenges.index')
                     ->with('error', 'You left the challenge.');
}

    public function create()
    {
        $this->requireAdmin();
        $exercises = Exercise::active()->orderBy('name')->get();
        return view('challenges.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:session_count,total_weight,streak,specific_exercise',
            'target'      => 'required|integer|min:1',
            'exercise_id' => 'nullable|exists:exercises,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
        ]);

        $data['created_by'] = Auth::id();
        $data['status'] = now()->toDateString() >= $data['start_date'] ? 'open' : 'upcoming';

        Challenge::create($data);

        return redirect()->route('challenges.index')
                         ->with('success', 'Challenge created!');
    }

    public function edit(Challenge $challenge)
    {
        $this->requireAdmin();
        $exercises = Exercise::active()->orderBy('name')->get();
        return view('challenges.edit', compact('challenge', 'exercises'));
    }

    public function update(Request $request, Challenge $challenge)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:session_count,total_weight,streak,specific_exercise',
            'target'      => 'required|integer|min:1',
            'exercise_id' => 'nullable|exists:exercises,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'status'      => 'required|in:upcoming,open,closed',
        ]);

        $challenge->update($data);

        return redirect()->route('challenges.index')
                         ->with('success', 'Challenge updated.');
    }

    public function destroy(Challenge $challenge)
    {
        $this->requireAdmin();
        $challenge->delete();

        return redirect()->route('challenges.index')
                         ->with('success', 'Challenge deleted.');
    }

    public static function recalculateProgress(Challenge $challenge, $user): void
    {
        $participant = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $participant) return;

        $progress = match($challenge->type) {
            'session_count' => WorkoutSession::forUser($user->id)
                ->where('status', 'completed')
                ->whereBetween('session_date', [$challenge->start_date, $challenge->end_date])
                ->count(),

            'total_weight' => (int) \App\Models\SessionExercise::whereHas('workoutSession', fn($q) =>
                $q->forUser($user->id)
                  ->where('status', 'completed')
                  ->whereBetween('session_date', [$challenge->start_date, $challenge->end_date])
            )->sum('weight_kg'),

            'streak' => self::calculateStreak($user->id, $challenge),

            'specific_exercise' => (int) \App\Models\SessionExercise::where('exercise_id', $challenge->exercise_id)
                ->whereHas('workoutSession', fn($q) =>
                    $q->forUser($user->id)
                      ->where('status', 'completed')
                      ->whereBetween('session_date', [$challenge->start_date, $challenge->end_date])
                )->sum('reps'),

            default => 0,
        };

        $completed_at = $progress >= $challenge->target
            ? ($participant->completed_at ?? now())
            : null;

        $participant->update([
            'progress'     => $progress,
            'completed_at' => $completed_at,
        ]);
    }

    private static function calculateStreak(int $userId, Challenge $challenge): int
    {
        $dates = WorkoutSession::forUser($userId)
            ->where('status', 'completed')
            ->whereBetween('session_date', [$challenge->start_date, $challenge->end_date])
            ->pluck('session_date')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->sort()
            ->values();

        if ($dates->isEmpty()) return 0;

        $maxStreak = 1;
        $current   = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            $prev = \Carbon\Carbon::parse($dates[$i - 1]);
            $curr = \Carbon\Carbon::parse($dates[$i]);
            if ($curr->diffInDays($prev) === 1) {
                $current++;
                $maxStreak = max($maxStreak, $current);
            } else {
                $current = 1;
            }
        }

        return $maxStreak;
    }

    private function requireAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }
}