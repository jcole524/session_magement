<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\SessionExercise;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $sessions = $user->isAdmin()
            ? WorkoutSession::with('user')->latest('session_date')->paginate(15)
            : WorkoutSession::forUser($user->id)->latest('session_date')->paginate(15);

        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'nullable',
            'notes'        => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        $data['status']  = 'scheduled';

        $session = WorkoutSession::create($data);

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Session scheduled successfully.');
    }

    public function show(WorkoutSession $session)
    {
        $this->authorizeSession($session);
        $session->load('sessionExercises.exercise');
        $exercises = Exercise::active()->orderBy('name')->get();

        return view('sessions.show', compact('session', 'exercises'));
    }

    public function edit(WorkoutSession $session)
    {
        $this->authorizeSession($session);
        abort_if($session->status === 'cancelled', 403, 'Cannot edit a cancelled session.');

        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, WorkoutSession $session)
    {
        $this->authorizeSession($session);
        abort_if($session->status === 'cancelled', 403);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'nullable',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:scheduled,active,completed,cancelled',
        ]);

        $session->update($data);

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Session updated.');
    }

    public function cancel(WorkoutSession $session)
    {
        $this->authorizeSession($session);
        $session->update(['status' => 'cancelled']);

        return redirect()->route('sessions.index')
                         ->with('success', 'Session cancelled.');
    }

    public function start(WorkoutSession $session)
    {
        $this->authorizeSession($session);
        abort_if($session->status !== 'scheduled', 403, 'Only scheduled sessions can be started.');

        $session->update(['status' => 'active']);

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Session started!');
    }

    public function complete(WorkoutSession $session)
    {
        $this->authorizeSession($session);
        abort_if($session->status !== 'active', 403, 'Only active sessions can be completed.');

        $session->update(['status' => 'completed']);

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Session marked as completed!');
    }

    // ── Session Exercises ──────────────────────────────────────────────────────

    public function addExercise(Request $request, WorkoutSession $session)
    {
        $this->authorizeSession($session);

        $data = $request->validate([
            'exercise_id'   => 'required|exists:exercises,id',
            'sets'          => 'nullable|integer|min:1',
            'reps'          => 'nullable|integer|min:1',
            'weight_kg'     => 'nullable|numeric|min:0',
            'duration_mins' => 'nullable|integer|min:1',
            'notes'         => 'nullable|string',
        ]);

        $data['session_id'] = $session->id;
        SessionExercise::create($data);

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Exercise logged.');
    }

    public function removeExercise(WorkoutSession $session, SessionExercise $sessionExercise)
    {
        $this->authorizeSession($session);
        $sessionExercise->delete();

        return redirect()->route('sessions.show', $session)
                         ->with('success', 'Exercise removed.');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function authorizeSession(WorkoutSession $session): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $session->user_id !== $user->id) {
            abort(403);
        }
    }
}