<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication | LMS</title>
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
            max-width: 440px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
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
            margin-bottom: 6px;
        }
        .auth-subtitle {
            font-size: 14px;
            color: #64748b;
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
        .input-group { margin-bottom: 24px; }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 500;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 14px 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            transition: all 0.2s;
        }
        .input-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .input-group input::placeholder { color: #cbd5e1; font-size: 16px; letter-spacing: 2px; }
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
        .help-text {
            margin-top: 20px;
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .help-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .help-text a:hover { color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="/imagess.png" alt="Library System Logo">
        </div>
        <h2>Two-Factor Authentication</h2>
        <p class="auth-subtitle">Enter the 6-digit code from your authenticator app</p>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}" autocomplete="off">
            @csrf

            <div class="input-group">
                <input type="text" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*" autofocus>
            </div>

            <button type="submit" class="btn-auth">Verify</button>
        </form>

        <div class="help-text">
            Open your Google Authenticator app and enter the code shown.<br>
            <a href="{{ route('admin.login') }}">Back to Login</a>
        </div>
    </div>
</body>
</html>
