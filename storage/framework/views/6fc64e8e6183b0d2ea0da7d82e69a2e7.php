<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification | Admin Portal | LMS</title>
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
        }
        .auth-logo img { display: block; margin: 0 auto 24px; width: 80px; }
        .icon-wrap {
            width: 64px; height: 64px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        h2 { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .auth-subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; line-height: 1.6; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; text-align: left; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }
        .otp-inputs input {
            width: 48px; height: 56px;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            outline: none;
        }
        .otp-inputs input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .otp-inputs input.filled { border-color: #2563eb; background: #eff6ff; }
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
        .btn-auth:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .timer { font-size: 13px; color: #94a3b8; margin-top: 16px; }
        .timer span { font-weight: 600; color: #2563eb; }
        .resend-link { font-size: 13px; color: #64748b; margin-top: 12px; }
        .resend-link a { color: #2563eb; text-decoration: none; font-weight: 500; }
        .resend-link a:hover { color: #1d4ed8; }
        .back-link { margin-top: 20px; }
        .back-link a { color: #94a3b8; font-size: 13px; text-decoration: none; }
        .back-link a:hover { color: #64748b; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="/imagess.png" alt="Library System Logo">
        </div>

        <div class="icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>

        <h2>Check Your Email</h2>
        <p class="auth-subtitle">
            We sent a 6-digit OTP to your registered email address.<br>
            It expires in <strong>10 minutes</strong>.
        </p>

        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($error); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.otp.verify')); ?>" id="otpForm">
            <?php echo csrf_field(); ?>
            
            <input type="hidden" name="otp" id="otpValue">

            <div class="otp-inputs">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            </div>

            <button type="submit" class="btn-auth">Verify OTP</button>
        </form>

        <div class="timer">
            Code expires in <span id="countdown">10:00</span>
        </div>

        <div class="resend-link">
            Didn't receive it? <a href="<?php echo e(route('admin.login')); ?>">Go back and try again</a>
        </div>

        <div class="back-link">
            <a href="<?php echo e(route('admin.login')); ?>">← Back to Login</a>
        </div>
    </div>

    <script>
        const digits = document.querySelectorAll('.otp-digit');
        const otpValue = document.getElementById('otpValue');
        const form = document.getElementById('otpForm');

        // Auto-focus first input
        digits[0].focus();

        digits.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Allow only digits
                input.value = input.value.replace(/[^0-9]/g, '');
                input.classList.toggle('filled', input.value !== '');

                if (input.value && index < digits.length - 1) {
                    digits[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    digits[index - 1].classList.remove('filled');
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                pasted.split('').forEach((char, i) => {
                    if (digits[i]) {
                        digits[i].value = char;
                        digits[i].classList.add('filled');
                    }
                });
                const next = Math.min(pasted.length, digits.length - 1);
                digits[next].focus();
            });
        });

        form.addEventListener('submit', (e) => {
            otpValue.value = Array.from(digits).map(d => d.value).join('');
        });

        // Countdown timer
        let seconds = 600;
        const countdown = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            const m = String(Math.floor(seconds / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            countdown.textContent = `${m}:${s}`;
            if (seconds <= 0) {
                clearInterval(timer);
                countdown.textContent = 'Expired';
                countdown.style.color = '#ef4444';
            }
        }, 1000);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\LBS\resources\views/admin/auth/otp.blade.php ENDPATH**/ ?>