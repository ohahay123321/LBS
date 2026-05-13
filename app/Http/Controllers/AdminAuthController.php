<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use App\Rules\ReCaptcha;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    protected BrevoMailService $mailer;

    public function __construct(BrevoMailService $mailer)
    {
        $this->mailer = $mailer;
    }

    // ─── Login: Step 1 — verify credentials, send OTP ────────────────────────

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

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('admin')->user();
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

            $sent = $this->mailer->send(
                $user->email,
                $user->name ?? 'Admin',
                'Your Admin Login OTP - ' . config('app.name'),
                $this->otpEmailHtml($otp)
            );

            if (! $sent) {
                return back()->withErrors(['email' => 'Could not send OTP email. Please try again.']);
            }

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

        if (! $user || ! $user->login_otp || ! $user->login_otp_expires || now()->gt($user->login_otp_expires)) {
            session()->forget('otp:admin:user_id');
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'OTP expired. Please log in again.']);
        }

        if ($request->otp !== $user->login_otp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

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

        $sent = $this->mailer->send(
            $user->email,
            $user->name ?? 'Admin',
            config('app.name') . ' - Verify Your Email',
            $this->verifyEmailHtml($verifyLink)
        );

        $message = $sent
            ? 'Registration successful! Please check your email to verify your account.'
            : 'Registration successful! But email could not be sent. Please contact support.';

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

        $user = User::where('email', $request->email)->where('role', 'ADMIN')->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No admin account found with that email.']);
        }
        $token = bin2hex(random_bytes(32));
        $user->update(['reset_token' => $token, 'reset_expires' => now()->addHours(24)]);

        $resetLink = route('admin.reset', ['email' => $user->email, 'token' => $token]);

        $sent = $this->mailer->send(
            $user->email,
            $user->name ?? 'Admin',
            config('app.name') . ' - Reset Your Password',
            $this->resetEmailHtml($resetLink)
        );

        if (! $sent) {
            return back()->withErrors(['email' => 'Could not send reset email. Please try again.']);
        }

        return back()->with('success', 'Password reset link sent to your email.');
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

        return redirect()->route('admin.login')->with('success', 'Password reset successful! You can now login.');
    }

    // ─── Email Verification ───────────────────────────────────────────────────

    public function verify(Request $request)
    {
        $request->validate(['email' => 'required|email', 'token' => 'required|string']);

        $user = User::where('email', $request->email)
            ->where('verification_token', $request->token)
            ->first();

        if ($user && $user->email_verified) {
            return redirect()->route('admin.login')->with('success', 'Email already verified. You can now login.');
        }

        if ($user) {
            $user->update(['email_verified' => true, 'verification_token' => null]);
            return redirect()->route('admin.login')->with('success', 'Email verified! You can now login.');
        }

        return redirect()->route('admin.login')->with('error', 'Invalid or expired verification link.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.landing');
    }

    // ─── Email HTML Templates ─────────────────────────────────────────────────

    private function otpEmailHtml(string $otp): string
    {
        $appName = config('app.name');
        return <<<HTML
        <!DOCTYPE html><html><head><style>
        body{font-family:Arial,sans-serif;background:#f8fafc;margin:0;padding:40px 20px;}
        .container{max-width:480px;margin:0 auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}
        .header{background:#2563eb;padding:28px;text-align:center;}
        .header h1{color:#fff;font-size:20px;margin:0;}
        .body{padding:32px;}
        .body p{color:#475569;font-size:15px;line-height:1.6;margin:0 0 16px;}
        .otp-box{background:#f1f5f9;border:2px dashed #2563eb;border-radius:10px;text-align:center;padding:24px;margin:24px 0;}
        .otp-code{font-size:40px;font-weight:800;letter-spacing:12px;color:#1e293b;font-family:monospace;}
        .note{font-size:13px;color:#94a3b8;margin-top:8px;}
        .warning{background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:12px;font-size:13px;color:#854d0e;}
        .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px;text-align:center;font-size:12px;color:#94a3b8;}
        </style></head><body>
        <div class="container">
        <div class="header"><h1>{$appName} — Admin Login</h1></div>
        <div class="body">
        <p>Hello Admin,</p>
        <p>Use the OTP below to complete your login. It expires in <strong>10 minutes</strong>.</p>
        <div class="otp-box">
        <div class="otp-code">{$otp}</div>
        <div class="note">One-Time Password — valid for 10 minutes</div>
        </div>
        <div class="warning">If you did not attempt to log in, please ignore this email and change your password immediately.</div>
        </div>
        <div class="footer">&copy; {$appName}. Do not reply to this email.</div>
        </div>
        </body></html>
        HTML;
    }

    private function verifyEmailHtml(string $verifyLink): string
    {
        $appName = config('app.name');
        return <<<HTML
        <!DOCTYPE html><html><head><style>
        body{font-family:Arial,sans-serif;background:#f8fafc;margin:0;padding:40px 20px;}
        .container{max-width:480px;margin:0 auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}
        .header{background:#2563eb;padding:28px;text-align:center;}
        .header h1{color:#fff;font-size:20px;margin:0;}
        .body{padding:32px;}
        .body p{color:#475569;font-size:15px;line-height:1.6;margin:0 0 16px;}
        .btn{display:inline-block;background:#2563eb;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;}
        .link{font-size:13px;color:#94a3b8;word-break:break-all;margin-top:16px;}
        .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px;text-align:center;font-size:12px;color:#94a3b8;}
        </style></head><body>
        <div class="container">
        <div class="header"><h1>{$appName} — Verify Your Email</h1></div>
        <div class="body">
        <p>Hello Admin,</p>
        <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
        <p style="text-align:center;"><a href="{$verifyLink}" class="btn">Verify Email</a></p>
        <p class="link">Or copy this link: {$verifyLink}</p>
        <p style="color:#ef4444;font-size:13px;">This link expires in 24 hours.</p>
        </div>
        <div class="footer">&copy; {$appName}. Do not reply to this email.</div>
        </div>
        </body></html>
        HTML;
    }

    private function resetEmailHtml(string $resetLink): string
    {
        $appName = config('app.name');
        return <<<HTML
        <!DOCTYPE html><html><head><style>
        body{font-family:Arial,sans-serif;background:#f8fafc;margin:0;padding:40px 20px;}
        .container{max-width:480px;margin:0 auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}
        .header{background:#2563eb;padding:28px;text-align:center;}
        .header h1{color:#fff;font-size:20px;margin:0;}
        .body{padding:32px;}
        .body p{color:#475569;font-size:15px;line-height:1.6;margin:0 0 16px;}
        .btn{display:inline-block;background:#2563eb;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;}
        .link{font-size:13px;color:#94a3b8;word-break:break-all;margin-top:16px;}
        .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px;text-align:center;font-size:12px;color:#94a3b8;}
        </style></head><body>
        <div class="container">
        <div class="header"><h1>{$appName} — Reset Password</h1></div>
        <div class="body">
        <p>Hello Admin,</p>
        <p>We received a request to reset your password. Click the button below to proceed:</p>
        <p style="text-align:center;"><a href="{$resetLink}" class="btn">Reset Password</a></p>
        <p class="link">Or copy this link: {$resetLink}</p>
        <p style="color:#ef4444;font-size:13px;">This link expires in 24 hours. If you did not request this, ignore this email.</p>
        </div>
        <div class="footer">&copy; {$appName}. Do not reply to this email.</div>
        </div>
        </body></html>
        HTML;
    }
}
