<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been deactivated.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => 'nullable|string|max:20',
            'gender'   => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        $data['role']   = 'user';
        $data['status'] = 'active';

        $user = User::create($data);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'gender'       => 'nullable|in:male,female,other',
            'date_of_birth'=> 'nullable|date|before:today',
        ]);

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profile updated.');
    }
}
