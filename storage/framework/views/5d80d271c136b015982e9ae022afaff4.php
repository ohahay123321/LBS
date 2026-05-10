<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | LMS</title>
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
        .input-group { margin-bottom: 18px; }
        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #1e293b;
            font-weight: 500;
            font-size: 14px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrapper .icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
        }
        .input-wrapper input {
            width: 100%;
            padding: 10px 12px 10px 42px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }
        .input-wrapper input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .input-wrapper input::placeholder { color: #94a3b8; }
        .forgot-link { text-align: right; margin-bottom: 20px; }
        .forgot-link a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-link a:hover { color: #1d4ed8; }
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }
        .btn-auth:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .auth-toggle {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
        }
        .auth-toggle a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .auth-toggle a:hover { color: #1d4ed8; }
        .notice {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="/imagess.png" alt="Library System Logo">
        </div>
        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Sign in to Admin Portal</p>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.login.submit')); ?>">
            <?php echo csrf_field(); ?>

            <div class="input-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </span>
                    <input type="email" name="email" placeholder="admin@example.com" required autofocus>
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="forgot-link">
                <a href="<?php echo e(route('admin.forgot')); ?>">Forgot your password?</a>
            </div>

            <div class="g-recaptcha" data-sitekey="<?php echo e(config('recaptcha.site_key')); ?>" style="margin-bottom: 12px;"></div>

            <button type="submit" class="btn-auth">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Sign In
            </button>
        </form>

        <p class="auth-toggle">
            Don't have an account?
            <a href="<?php echo e(route('admin.register')); ?>">Create Account</a>
        </p>

        <div class="notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Admin Login
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\LBS\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>