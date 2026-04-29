<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::orderBy('category')->orderBy('name')->paginate(20);
        return view('exercises.index', compact('exercises'));
    }

    public function show(Exercise $exercise)
    {
        return view('exercises.show', compact('exercise'));
    }

    public function create()
    {
        $this->requireAdmin();
        return view('exercises.create');
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'muscle_group' => 'nullable|string|max:100',
            'difficulty'   => 'required|in:beginner,intermediate,advanced',
            'equipment'    => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'description'  => 'nullable|string',
            'video_url'    => 'nullable|url|max:500',
        ]);

        Exercise::create($data);

        return redirect()->route('exercises.index')
                         ->with('success', 'Exercise added to library.');
    }

    public function edit(Exercise $exercise)
    {
        $this->requireAdmin();
        return view('exercises.edit', compact('exercise'));
    }

    public function update(Request $request, Exercise $exercise)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'muscle_group' => 'nullable|string|max:100',
            'difficulty'   => 'required|in:beginner,intermediate,advanced',
            'equipment'    => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'description'  => 'nullable|string',
            'video_url'    => 'nullable|url|max:500',
            'status'       => 'required|in:active,inactive',
        ]);

        $exercise->update($data);

        return redirect()->route('exercises.index')
                         ->with('success', 'Exercise updated.');
    }

    public function destroy(Exercise $exercise)
    {
        $this->requireAdmin();
        $exercise->update(['status' => 'inactive']);

        return redirect()->route('exercises.index')
                         ->with('success', 'Exercise deactivated.');
    }

    public function activate(Exercise $exercise)
    {
        $this->requireAdmin();
        $exercise->update(['status' => 'active']);

        return redirect()->route('exercises.index')
                         ->with('success', 'Exercise activated.');
    }

    private function requireAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Admin access required.');
    }
}