<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Management System</title>
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
        .subtitle {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 48px;
            max-width: 500px;
            line-height: 1.6;
        }
        .portals {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .portal-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 36px 40px;
            width: 260px;
            text-decoration: none;
            color: #ffffff;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .portal-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #2563eb;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.2);
        }
        .portal-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }
        .portal-icon.admin { background: rgba(37, 99, 235, 0.15); }
        .portal-icon.student { background: rgba(16, 185, 129, 0.15); }
        .portal-card h2 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .portal-card p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            padding: 24px;
            color: #475569;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('imagess.png') }}" alt="Library System Logo" class="logo">
        <h1>Library Management System</h1>
        <p class="subtitle">A simple and efficient platform for managing library resources, book requests, and student records.</p>

        <div class="portals">
            <a href="{{ route('admin.landing') }}" class="portal-card">
                <div class="portal-icon admin">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h2>Admin Portal</h2>
                <p>Manage books, categories, requests, and oversee library operations.</p>
            </a>

            <a href="{{ route('student.landing') }}" class="portal-card">
                <div class="portal-icon student">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 3.34 3 6 3s6-1.34 6-3v-5"/></svg>
                </div>
                <h2>Student Portal</h2>
                <p>Browse books, borrow resources, and track your borrowing history.</p>
            </a>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Library Management System. All rights reserved.
    </div>
</body>
</html>
