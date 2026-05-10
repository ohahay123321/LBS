<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Portal | Library Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 32px;
            border: 3px solid #2563eb;
            box-shadow: 0 0 40px rgba(37, 99, 235, 0.3);
        }
        h1 {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            margin-bottom: 16px;
        }
        .subtitle {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 16px;
            max-width: 540px;
            line-height: 1.7;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            max-width: 640px;
            width: 100%;
            margin-bottom: 40px;
        }
        .feature {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 20px 16px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .feature:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            background: rgba(37, 99, 235, 0.12);
        }
        .feature h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .feature p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }
        .actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
            border: none;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
        }
        .btn-outline {
            background: transparent;
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-outline:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        .back-link {
            margin-top: 32px;
        }
        .back-link a {
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-link a:hover {
            color: #94a3b8;
        }
        .footer {
            text-align: center;
            padding: 24px;
            color: #475569;
            font-size: 12px;
        }
        @media (max-width: 600px) {
            h1 { font-size: 28px; }
            .features { grid-template-columns: 1fr; }
            .feature { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('imagess.png') }}" alt="Library System Logo" class="logo">
        <div class="badge">Admin Portal</div>
        <h1>Library Management System</h1>
        <p class="subtitle">
          
        </p>



        <div class="actions">
            <a href="{{ route('admin.login') }}" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                Sign In
            </a>
            <a href="{{ route('admin.register') }}" class="btn btn-outline">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                Create Account
            </a>
        </div>

    </div>


</body>
</html>