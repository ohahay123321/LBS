<?php

namespace App\Http\Controllers;

use App\Mail\LoginOtpMail;
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
    // ─── Login: Step 1 — verify credentials, send OTP ───────────────────────

    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            // Log out immediately — we require OTP before granting access
            Auth::guard('admin')->logout();

            if ($user->role !== 'ADMIN') {
                return back()->withErrors(['email' => 'Invalid credentials or not an admin.']);
            }

            if (! $user->email_verified) {
                return back()->withErrors(['email' => 'Email not verified. Please check your email.']);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'login_otp'         => $otp,
                'login_otp_expires' => now()->addMinutes(10),
            ]);

            try {
                Mail::to($user->email)->send(new LoginOtpMail($otp));
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Could not send OTP email. Please try again.']);
            }

            // Store user ID in session for the OTP step
            session()->put('otp:admin:user_id', $user->id);

            return redirect()->route('admin.otp');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not an admin.']);
    }

    // ─── Login: Step 2 — OTP verification ────────────────────────────────────

    public function showOtp()
    {
        if (! session()->has('otp:admin:user_id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = session('otp:admin:user_id');
        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $user = User::find($userId);

        if (
            ! $user ||
            ! $user->login_otp ||
            ! $user->login_otp_expires ||
            now()->gt($user->login_otp_expires)
        ) {
            session()->forget('otp:admin:user_id');
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'OTP expired. Please log in again.']);
        }

        if ($request->otp !== $user->login_otp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        // Clear OTP and log the user in
        $user->update(['login_otp' => null, 'login_otp_expires' => null]);
        session()->forget('otp:admin:user_id');

        Auth::guard('admin')->login($user);
        Log::create(['description' => 'Admin logged in: ' . $user->email]);

        return redirect()->route('admin.dashboard');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $verificationToken = bin2hex(random_bytes(32));

        $user = User::create([
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'role'               => 'ADMIN',
            'verification_token' => $verificationToken,
        ]);

        $verifyLink = route('admin.verify', ['email' => $user->email, 'token' => $verificationToken]);

        try {
            Mail::to($user->email)->send(new WelcomeMail($verifyLink));
            $message = 'Registration successful! Please check your email to verify your account.';
        } catch (\Exception $e) {
            $message = 'Registration successful! But email could not be sent. Contact admin.';
        }

        return redirect()->route('admin.login')->with('success', $message);
    }

    // ─── Forgot / Reset Password ──────────────────────────────────────────────

    public function showForgot()
    {
        return view('admin.auth.forgot');
    }

    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user  = User::where('email', $request->email)->first();
        $token = bin2hex(random_bytes(32));

        $user->update([
            'reset_token'   => $token,
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
        $request->validate(['email' => 'required|email', 'token' => 'required']);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_expires', '>', now())
            ->first();

        if (! $user) {
            return redirect()->route('admin.forgot')
                ->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        return view('admin.auth.reset', ['email' => $request->email, 'token' => $request->token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_expires', '>', now())
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        $user->update([
            'password'      => Hash::make($request->password),
            'reset_token'   => null,
            'reset_expires' => null,
        ]);

        return redirect()->route('admin.login')
            ->with('success', 'Password reset successful! You can now login.');
    }

    // ─── Email Verification ───────────────────────────────────────────────────

    public function verify(Request $request)
    {
        $request->validate(['email' => 'required|email', 'token' => 'required|string']);

        $user = User::where('email', $request->email)
            ->where('verification_token', $request->token)
            ->first();

        if ($user && $user->email_verified) {
            return redirect()->route('admin.login')
                ->with('success', 'Email already verified. You can now login.');
        }

        if ($user) {
            $user->update(['email_verified' => true, 'verification_token' => null]);
            return redirect()->route('admin.login')
                ->with('success', 'Email verified! You can now login.');
        }

        return redirect()->route('admin.login')
            ->with('error', 'Invalid or expired verification link.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.landing');
    }
}
