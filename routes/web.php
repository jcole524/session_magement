<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProgressLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sessions', SessionController::class)->except(['destroy']);
    Route::patch('/sessions/{session}/cancel',   [SessionController::class, 'cancel'])->name('sessions.cancel');
    Route::patch('/sessions/{session}/start',    [SessionController::class, 'start'])->name('sessions.start');
    Route::patch('/sessions/{session}/complete', [SessionController::class, 'complete'])->name('sessions.complete');
    Route::post('/sessions/{session}/exercises', [SessionController::class, 'addExercise'])->name('sessions.exercises.add');
    Route::delete('/sessions/{session}/exercises/{sessionExercise}', [SessionController::class, 'removeExercise'])->name('sessions.exercises.remove');

    Route::resource('exercises', ExerciseController::class)->except(['show']);
    Route::patch('/exercises/{exercise}/activate', [ExerciseController::class, 'activate'])->name('exercises.activate');
    Route::get('/exercises/{exercise}/guide', [ExerciseController::class, 'show'])->name('exercises.show');

    Route::resource('goals', GoalController::class)->except(['show', 'destroy']);
    Route::patch('/goals/{goal}/cancel', [GoalController::class, 'cancel'])->name('goals.cancel');

    Route::resource('progress', ProgressLogController::class)->except(['show']);

    Route::resource('challenges', ChallengeController::class)->except(['show']);
    Route::get('/challenges/{challenge}',          [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{challenge}/join',    [ChallengeController::class, 'join'])->name('challenges.join');
    Route::delete('/challenges/{challenge}/leave', [ChallengeController::class, 'leave'])->name('challenges.leave');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',                      [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}',               [UserManagementController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/toggle',      [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
        Route::patch('/users/{user}/makeadmin',   [UserManagementController::class, 'makeAdmin'])->name('users.makeadmin');
        Route::patch('/users/{user}/removeadmin', [UserManagementController::class, 'removeAdmin'])->name('users.removeadmin');
        Route::delete('/users/{user}',            [UserManagementController::class, 'destroy'])->name('users.destroy'); // ADDED
    });

});

require __DIR__.'/auth.php';