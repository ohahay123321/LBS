<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | LMS</title>
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
            max-width: 480px;
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
            text-transform: uppercase;
            letter-spacing: 1px;
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
        .password-requirements {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .password-requirements h4 {
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .password-requirements li {
            color: #64748b;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .password-requirements li::before {
            content: '✗';
            color: #ef4444;
            font-weight: bold;
        }
        .password-requirements li.valid::before {
            content: '✓';
            color: #10b981;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }
        .btn-auth:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .auth-toggle {
            text-align: center;
            margin-top: 25px;
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
        .gmail-notice {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .gmail-notice p {
            color: #94a3b8;
            font-size: 12px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="<?php echo e(asset('imagess.png')); ?>" alt="Library System Logo">
        </div>
        <h2>Create Student Account</h2>
        <p class="auth-subtitle">Library Management System</p>

        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('student.register.submit')); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="register">

            <div class="input-group">
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" name="name" placeholder="Full Name" maxlength="100">
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                    </span>
                    <input type="text" name="student_number" placeholder="Student Number" maxlength="50">
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </span>
                    <input type="email" name="email" placeholder="your.name@gmail.com" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" placeholder="Min 8 chars: A-Z, a-z, 0-9, symbols" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" name="password_confirmation" id="confirm_password" placeholder="Confirm your password" required>
                </div>
            </div>

            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li id="req-length">8+ characters</li>
                    <li id="req-uppercase">At least one uppercase letter (A-Z)</li>
                    <li id="req-lowercase">At least one lowercase letter (a-z)</li>
                    <li id="req-number">At least one number (0-9)</li>
                    <li id="req-symbol">At least one symbol (!@#$%^&*)</li>
                </ul>
            </div>

            <button type="submit" class="btn-auth">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Create Account
            </button>
        </form>

        <p class="auth-toggle">
            <a href="<?php echo e(route('student.login')); ?>">← Back to Login</a>
        </p>

        <div class="gmail-notice">
            <p>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Student Register
            </p>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');

        function validatePassword() {
            const val = password.value;
            document.getElementById('req-length').className = val.length >= 8 ? 'valid' : '';
            document.getElementById('req-uppercase').className = /[A-Z]/.test(val) ? 'valid' : '';
            document.getElementById('req-lowercase').className = /[a-z]/.test(val) ? 'valid' : '';
            document.getElementById('req-number').className = /[0-9]/.test(val) ? 'valid' : '';
            document.getElementById('req-symbol').className = /[^A-Za-z0-9]/.test(val) ? 'valid' : '';
        }

        password.addEventListener('input', validatePassword);
        confirm.addEventListener('input', validatePassword);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\LBS\resources\views/student/auth/register.blade.php ENDPATH**/ ?>