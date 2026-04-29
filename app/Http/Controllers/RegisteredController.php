<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'         => ['nullable', 'string', 'max:20'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'role'          => 'user',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}