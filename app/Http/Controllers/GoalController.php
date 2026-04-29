<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $goals = Goal::with(['user', 'exercise'])
                ->latest()
                ->paginate(15);

            return view('goals.index', compact('goals'));
        }

        // Auto-check all non-terminal goals first
        Goal::forUser(Auth::id())
            ->whereNotIn('status', ['achieved', 'cancelled'])
            ->get()
            ->each->checkAndUpdate();

        $goals = Goal::forUser(Auth::id())
            ->with('exercise')
            ->latest()
            ->paginate(15);

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        $exercises = Exercise::active()->orderBy('name')->get();
        return view('goals.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        // Merge strength_target_value into target_value
        if ($type === 'strength' && $request->filled('strength_target_value')) {
            $request->merge(['target_value' => $request->input('strength_target_value')]);
        }

        $rules = [
            'type'        => 'required|in:weight_loss,muscle_gain,strength,endurance,flexibility,consistency',
            'description' => 'nullable|string|max:500',
            'target_date' => 'nullable|date|after:today',
        ];

        if (in_array($type, ['weight_loss', 'muscle_gain'])) {
            $rules['target_value'] = 'required|numeric|min:1';
        }
        if ($type === 'muscle_gain') {
            $rules['target_sessions'] = 'required|integer|min:1';
        }
        if ($type === 'strength') {
            $rules['exercise_id']  = 'required|exists:exercises,id';
            $rules['target_value'] = 'required|numeric|min:1';
        }
        if (in_array($type, ['endurance', 'flexibility', 'consistency'])) {
            $rules['target_sessions'] = 'required|integer|min:1';
        }

        $data = $request->validate($rules);
        $data['user_id'] = Auth::id();
        $data['status']  = 'pending';

        $goal = Goal::create($data);
        $goal->checkAndUpdate();

        return redirect()->route('goals.index')
                         ->with('success', 'Goal created! The system will activate it automatically once conditions are met.');
    }

    public function edit(Goal $goal)
    {
        abort_if($goal->user_id !== Auth::id(), 403);
        abort_if(in_array($goal->status, ['achieved', 'cancelled']), 403, 'Cannot edit a completed goal.');

        $exercises = Exercise::active()->orderBy('name')->get();
        return view('goals.edit', compact('goal', 'exercises'));
    }

    public function update(Request $request, Goal $goal)
    {
        abort_if($goal->user_id !== Auth::id(), 403);
        abort_if(in_array($goal->status, ['achieved', 'cancelled']), 403);

        $data = $request->validate([
            'description' => 'nullable|string|max:500',
            'target_date' => 'nullable|date',
        ]);

        $goal->update($data);

        return redirect()->route('goals.index')
                         ->with('success', 'Goal updated.');
    }

    public function cancel(Goal $goal)
    {
        abort_if($goal->user_id !== Auth::id(), 403);
        abort_if($goal->status === 'achieved', 403, 'Cannot cancel an achieved goal.');

        $goal->update(['status' => 'cancelled']);

        return redirect()->route('goals.index')
                         ->with('success', 'Goal cancelled.');
    }
}