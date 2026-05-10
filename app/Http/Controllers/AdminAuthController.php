<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeMail;
use App\Models\Log;
use App\Models\User;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function showRegister()
    {
        return view('admin.auth.register');
    }

    public function showForgot()
    {
        return view('admin.auth.forgot');
    }

    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $user = User::where('email', $request->email)->first();
        $token = bin2hex(random_bytes(32));
        $user->update([
            'reset_token' => $token,
            'reset_expires' => now()->addHours(24),
        ]);

        $resetLink = route('admin.reset', ['email' => $user->email, 'token' => $token]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($resetLink));

            return back()->with('success', 'Password reset link sent to your email.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Could not send reset email. Please try again.']);
        }
    }

    public function showReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_expires', '>', now())
            ->first();

        if (! $user) {
            return redirect()->route('admin.forgot')->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        return view('admin.auth.reset', ['email' => $request->email, 'token' => $request->token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_expires', '>', now())
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_token' => null,
            'reset_expires' => null,
        ]);

        return redirect()->route('admin.login')->with('success', 'Password reset successful! You can now login.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            if ($user->role !== 'ADMIN') {
                Auth::guard('admin')->logout();

                return back()->withErrors(['email' => 'Invalid credentials or not an admin.']);
            }

            if (! $user->email_verified) {
                Auth::guard('admin')->logout();

                return back()->withErrors(['email' => 'Email not verified. Please check your email.']);
            }

            Log::create(['description' => 'Admin logged in: '.$user->email]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not an admin.']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $verificationToken = bin2hex(random_bytes(32));

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'ADMIN',
            'verification_token' => $verificationToken,
        ]);

        $verifyLink = route('admin.verify', ['email' => $user->email, 'token' => $verificationToken]);

        // Send verification email
        try {
            Mail::to($user->email)->send(new WelcomeMail($verifyLink));
            $message = 'Registration successful! Please check your email to verify your account.';
        } catch (\Exception $e) {
            $message = 'Registration successful! But email could not be sent. Contact admin.';
        }

        return redirect()->route('admin.login')->with('success', $message);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('verification_token', $request->token)
            ->first();

        if ($user && $user->email_verified) {
            return redirect()->route('admin.login')->with('success', 'Email already verified. You can now login.');
        }

        if ($user) {
            $user->update([
                'email_verified' => true,
                'verification_token' => null,
            ]);

            return redirect()->route('admin.login')->with('success', 'Email verified! You can now login.');
        }

        return redirect()->route('admin.login')->with('error', 'Invalid or expired verification link.');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.landing');
    }
}
