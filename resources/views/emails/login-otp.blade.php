<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login OTP</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f8fafc; margin: 0; padding: 40px 20px; color: #1e293b; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; }
        .header p { color: #bfdbfe; font-size: 14px; margin: 6px 0 0; }
        .body { padding: 36px 32px; }
        .body p { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 20px; }
        .otp-box { background: #f1f5f9; border: 2px dashed #2563eb; border-radius: 10px; text-align: center; padding: 24px; margin: 24px 0; }
        .otp-code { font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #1e293b; font-family: 'Courier New', monospace; }
        .otp-note { font-size: 13px; color: #94a3b8; margin-top: 10px; }
        .warning { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #854d0e; margin-top: 20px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Library Management System</h1>
            <p>Admin Login Verification</p>
        </div>
        <div class="body">
            <p>Hello Admin,</p>
            <p>You requested to log in to the Admin Portal. Use the OTP below to complete your login. It expires in <strong>10 minutes</strong>.</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-note">One-Time Password — valid for 10 minutes</div>
            </div>

            <div class="warning">
                If you did not attempt to log in, please ignore this email and consider changing your password immediately.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Library Management System. Do not reply to this email.
        </div>
    </div>
</body>
</html>
