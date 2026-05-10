<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Exo 2', Arial, sans-serif; background: #0a0a1a; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .header { background: linear-gradient(135deg, #0a0a1a, #1a1a2e); padding: 30px; text-align: center; border-radius: 12px 12px 0 0; border-bottom: 2px solid #00f5ff; }
        h1 { color: #00f5ff; margin: 0; font-size: 24px; font-family: 'Orbitron', sans-serif; text-shadow: 0 0 20px rgba(0, 245, 255, 0.5); }
        .body { background: #1a1a2e; padding: 40px 30px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 15px rgba(0, 245, 255, 0.15); }
        h2 { color: #ffffff; margin-top: 0; font-family: 'Orbitron', sans-serif; }
        p { color: #adb5bd; line-height: 1.6; }
        .button { text-align: center; margin: 30px 0; }
        .button a { background: linear-gradient(135deg, #00f5ff, #00b8d4); color: #0a0a1a; padding: 14px 32px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600; font-family: 'Rajdhani', sans-serif; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 4px 15px rgba(0, 245, 255, 0.3); }
        .link { color: #6b7280; font-size: 14px; word-break: break-all; }
        .warning { color: #ff3d71; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Library Management System 2030</h1>
        </div>
        <div class="body">
            <h2>Welcome Admin!</h2>
            <p>Thank you for registering as an Admin for the Library Management System 2030.</p>
            <p>Please verify your email address by clicking the button below:</p>
            <div class="button">
                <a href="<?php echo e($verifyLink); ?>">Verify Email</a>
            </div>
            <p class="link">Or copy this link: <?php echo e($verifyLink); ?></p>
            <p class="warning"><strong>Verification link expires in 24 hours.</strong></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\LBS\resources\views/emails/verify.blade.php ENDPATH**/ ?>