<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Two-Factor Authentication | LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .auth-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .auth-logo img {
            display: block;
            margin: 0 auto 24px;
            width: 80px;
        }
        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 6px;
        }
        .auth-subtitle {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .step {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 700;
            margin-right: 10px;
        }
        .step-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .step p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }
        .qr-container {
            text-align: center;
            padding: 16px;
            background: #ffffff;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            margin: 12px 0;
        }
        .qr-container img {
            max-width: 200px;
            height: auto;
        }
        .secret-key {
            background: #1e293b;
            color: #10b981;
            padding: 10px 14px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
            margin: 12px 0;
            text-align: center;
        }
        .input-group { margin-bottom: 18px; }
        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #1e293b;
            font-weight: 500;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            text-align: center;
            letter-spacing: 4px;
            transition: all 0.2s;
        }
        .input-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn-auth {
            width: 100%;
            padding: 12px 24px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-auth:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-auth:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-danger {
            width: 100%;
            padding: 12px 24px;
            background: #ef4444;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        .back-link a:hover { color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="/imagess.png" alt="Library System Logo">
        </div>
        <h2>Setup 2FA</h2>
        <p class="auth-subtitle">Enhance your account security with Google Authenticator</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <div class="step">
            <div class="step-title"><span class="step-number">1</span> Install Google Authenticator</div>
            <p>Download the <strong>Google Authenticator</strong> app on your phone from the App Store or Google Play Store.</p>
        </div>

        <div class="step">
            <div class="step-title"><span class="step-number">2</span> Scan the QR Code</div>
            <div class="qr-container">
                <img src="data:image/svg+xml;base64,{{ $qrCodeInline }}" alt="QR Code" style="max-width:200px;">
            </div>
            <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 8px;">
                Can't scan? Enter this key manually:
            </p>
            <div class="secret-key">{{ $secret }}</div>
        </div>

        <div class="step">
            <div class="step-title"><span class="step-number">3</span> Verify the Code</div>
            <p style="margin-bottom: 16px;">Enter the 6-digit code from the app to confirm setup.</p>
            <form method="POST" action="{{ route('2fa.enable') }}" autocomplete="off">
                @csrf
                <div class="input-group">
                    <input type="text" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*" required autofocus>
                </div>
                <button type="submit" class="btn-auth">Enable 2FA</button>
            </form>
        </div>

        <div class="back-link">
            <a href="{{ url()->previous() }}">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
