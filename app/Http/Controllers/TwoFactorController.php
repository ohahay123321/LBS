<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function showChallenge()
    {
        if (! session()->has('2fa:user:id') || ! session()->has('2fa:guard')) {
            return redirect()->route('admin.login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $userId = session('2fa:user:id');
        $guard  = session('2fa:guard');

        if (! $userId || ! $guard) {
            return redirect()->route('admin.login');
        }

        $user = User::find($userId);
        if (! $user || ! $user->google2fa_enabled || ! $user->google2fa_secret) {
            $loginRoute = $guard === 'student' ? 'student.login' : 'admin.login';
            return redirect()->route($loginRoute);
        }

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey(decrypt($user->google2fa_secret), $request->code)) {
            session()->forget(['2fa:user:id', '2fa:guard']);
            Auth::guard($guard)->login($user);

            $redirect = $guard === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');

            return $redirect->with('success', 'Authenticated successfully.');
        }

        return back()->withErrors(['code' => 'Invalid authentication code. Please try again.']);
    }

    public function showSetup()
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('student')->user();

        $google2fa = new Google2FA();

        if (! $user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->forceFill(['google2fa_secret' => encrypt($secret)])->save();
        } else {
            $secret = decrypt($user->google2fa_secret);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate inline QR code SVG using bacon/bacon-qr-code (already installed)
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeInline = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.two-factor-setup', compact('secret', 'qrCodeUrl', 'qrCodeInline'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::guard('admin')->user() ?? Auth::guard('student')->user();
        if (! $user || ! $user->google2fa_secret) {
            return back()->withErrors(['code' => 'No secret key found. Please refresh the page.']);
        }

        $secret = decrypt($user->google2fa_secret);
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($secret, $request->code)) {
            $user->forceFill(['google2fa_enabled' => true])->save();

            return redirect()->back()->with('success', 'Two-factor authentication has been enabled.');
        }

        return back()->withErrors(['code' => 'Invalid code. Please try again.']);
    }

    public function disable(Request $request)
    {
        $guard = Auth::guard('admin')->check() ? 'admin' : 'student';

        $request->validate(['password' => 'required|current_password:'.$guard]);

        $user = Auth::guard($guard)->user();
        $user->forceFill([
            'google2fa_secret' => null,
            'google2fa_enabled' => false,
        ])->save();

        return redirect()->back()->with('success', 'Two-factor authentication has been disabled.');
    }
}
