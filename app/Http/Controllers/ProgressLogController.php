<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\ProgressLog;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressLogController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->isAdmin(), 403);

        $logs = ProgressLog::forUser(Auth::id())
            ->with('workoutSession')
            ->orderByDesc('log_date')
            ->paginate(15);

        return view('progress.index', compact('logs'));
    }

    public function create()
    {
        abort_if(auth()->user()->isAdmin(), 403);

        // Check if already logged today
        $alreadyLogged = ProgressLog::forUser(Auth::id())
            ->where('log_date', today()->toDateString())
            ->exists();

        if ($alreadyLogged) {
            return redirect()->route('progress.index')
                             ->with('error', 'You have already logged your progress today. Edit your existing entry instead.');
        }

        $sessions = WorkoutSession::forUser(Auth::id())
            ->where('status', 'completed')
            ->orderByDesc('session_date')
            ->get();

        return view('progress.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->isAdmin(), 403);

        // Prevent duplicate logs for today
        $alreadyLogged = ProgressLog::forUser(Auth::id())
            ->where('log_date', today()->toDateString())
            ->exists();

        if ($alreadyLogged) {
            return redirect()->route('progress.index')
                             ->with('error', 'You have already logged your progress today. Edit your existing entry instead.');
        }

        $data = $request->validate([
            'body_weight_kg' => 'required|numeric|min:1|max:500',
            'notes'          => 'nullable|string|max:1000',
            'session_id'     => 'nullable|exists:workout_sessions,id',
        ]);

        $data['user_id']  = Auth::id();
        $data['log_date'] = today()->toDateString();

        $log = ProgressLog::create($data);

        // Trigger goal check
        Goal::where('user_id', Auth::id())
            ->whereNotIn('status', ['achieved', 'cancelled'])
            ->whereIn('type', ['weight_loss', 'muscle_gain'])
            ->get()
            ->each->checkAndUpdate();

        return redirect()->route('progress.index')
                         ->with('success', 'Progress logged.');
    }

    public function edit(ProgressLog $progress)
    {
        abort_if(auth()->user()->isAdmin(), 403);
        abort_if($progress->user_id !== Auth::id(), 403);

        $sessions = WorkoutSession::forUser(Auth::id())
            ->where('status', 'completed')
            ->orderByDesc('session_date')
            ->get();

        return view('progress.edit', compact('progress', 'sessions'));
    }

    public function update(Request $request, ProgressLog $progress)
    {
        abort_if(auth()->user()->isAdmin(), 403);
        abort_if($progress->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'body_weight_kg' => 'required|numeric|min:1|max:500',
            'notes'          => 'nullable|string|max:1000',
            'session_id'     => 'nullable|exists:workout_sessions,id',
        ]);

        $progress->update($data);

        // Trigger goal check
        Goal::where('user_id', Auth::id())
            ->whereNotIn('status', ['achieved', 'cancelled'])
            ->whereIn('type', ['weight_loss', 'muscle_gain'])
            ->get()
            ->each->checkAndUpdate();

        return redirect()->route('progress.index')
                         ->with('success', 'Progress updated.');
    }

    public function destroy(ProgressLog $progress)
    {
        abort_if(auth()->user()->isAdmin(), 403);
        abort_if($progress->user_id !== Auth::id(), 403);

        $progress->delete();

        return redirect()->route('progress.index')
                         ->with('success', 'Progress log deleted.');
    }
}