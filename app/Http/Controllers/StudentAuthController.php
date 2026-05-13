<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        return view('student.auth.login');
    }

    public function showRegister()
    {
        return view('student.auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('student')->attempt($credentials)) {
            $user = Auth::guard('student')->user();

            if ($user->role !== 'USER') {
                Auth::guard('student')->logout();

                return back()->withErrors(['email' => 'Invalid credentials or not a student.']);
            }

            if ($user->google2fa_enabled) {
                Auth::guard('student')->logout();
                session()->put(['2fa:user:id' => $user->id, '2fa:guard' => 'student']);

                return redirect()->route('2fa.challenge');
            }

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not a student.']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^[^@]+@gmail\.com$/i'],
            'student_number' => 'nullable|string|max:50|unique:users,student_number',
            'name' => 'nullable|string|max:100',
            'password' => 'required|min:8|confirmed',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        User::create([
            'email' => $request->email,
            'student_number' => $request->student_number,
            'name' => $request->name ?: '',
            'password' => Hash::make($request->password),
            'role' => 'USER',
            'email_verified' => true,
        ]);

        return redirect()->route('student.login')->with('success', 'Registration successful! You can now login.');
    }

    public function logout()
    {
        Auth::guard('student')->logout();

        return redirect()->route('student.landing');
    }
}
