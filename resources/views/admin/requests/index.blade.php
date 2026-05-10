<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Requests | LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb; --primary-hover: #1d4ed8;
            --sidebar-bg: #0f172a; --sidebar-text: #94a3b8; --sidebar-active: #1e293b;
            --bg-main: #f1f5f9; --card-bg: #ffffff; --text-main: #1e293b; --text-muted: #64748b;
            --border: #e2e8f0; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --info: #3b82f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05); --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-main); display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--sidebar-bg); color: var(--sidebar-text); padding: 0; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; }
        .sidebar-profile { padding: 24px 20px; text-align: center; border-bottom: 1px solid #1e293b; }
        .sidebar-profile img { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; border: 2px solid var(--primary); }
        .sidebar-profile h3 { font-size: 16px; color: #ffffff; font-weight: 600; }
        .sidebar-heading { padding: 16px 20px 8px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #475569; letter-spacing: 0.05em; }
        .nav-btn { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: var(--sidebar-text); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent; cursor: pointer; background: none; border-top: none; border-right: none; border-bottom: none; }
        .nav-btn:hover { background: var(--sidebar-active); color: #ffffff; border-left-color: var(--primary); }
        .nav-btn.active { background: var(--sidebar-active); color: #ffffff; border-left-color: var(--primary); }
        .main-wrapper { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #ffffff; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 50; }
        .topbar-left { font-size: 20px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .content { padding: 32px; flex: 1; }
        .breadcrumb { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 16px 20px; background: #ffffff; border: 1px solid var(--border); border-radius: 8px; }
        .breadcrumb strong { font-size: 18px; }
        .breadcrumb span { font-size: 14px; color: var(--text-muted); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; background: #ffffff; border-radius: 8px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        th { background: #f8fafc; color: var(--text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--text-main); font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 14px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .status-PENDING { display: inline-block; padding: 4px 10px; background: #fef3c7; color: #92400e; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-APPROVED { display: inline-block; padding: 4px 10px; background: #d1fae5; color: #065f46; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-DENIED { display: inline-block; padding: 4px 10px; background: #fee2e2; color: #991b1b; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-RETURNED { display: inline-block; padding: 4px 10px; background: #dbeafe; color: #1e40af; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .btn-success { background: var(--success); color: #ffffff; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: var(--danger); color: #ffffff; }
        .btn-danger:hover { background: #dc2626; }
        @media (max-width: 1024px) { .sidebar { width: 80px; } .sidebar-profile h3, .nav-btn span { display: none; } .main-wrapper { margin-left: 80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-profile">
            <img src="{{ asset('imagess.png') }}" alt="Profile">
            <h3>Admin</h3>
        </div>
        <div class="sidebar-heading">Navigation</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="main-wrapper">
        <div class="topbar">
            <div class="topbar-left">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Library
            </div>
            <div style="font-size: 14px; color: var(--text-muted);">All Book Requests</div>
            <div class="topbar-right"></div>
        </div>

        <div class="content">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="breadcrumb"><strong>All Requests</strong><span>Home / All Requests</span></div>

            <table>
                <tr>
                    <th>Student</th>
                    <th>Book</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Processed By</th>
                    <th>Action Date</th>
                    <th>Return Date</th>
                </tr>
                @forelse($requests as $r)
                <tr>
                    <td>
                        <strong>{{ $r->student_name }}</strong><br>
                        <span style="color: var(--text-muted); font-size: 13px;">ID: {{ $r->student_id_num }}</span>
                        @if($r->user)
                        <br><span style="color: var(--text-muted); font-size: 12px;">{{ $r->user->email }}</span>
                        @endif
                    </td>
                    <td><strong>{{ $r->book->title ?? 'N/A' }}</strong></td>
                    <td><span class="status-{{ $r->status }}">{{ $r->status }}</span></td>
                    <td>{{ $r->req_date ? $r->req_date->format('M d, Y h:i A') : '-' }}</td>
                    <td>{{ $r->admin->name ?? '-' }}</td>
                    <td>{{ $r->action_date ? $r->action_date->format('M d, Y') : '-' }}</td>
                    <td>{{ $r->return_date ? $r->return_date->format('M d, Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; color: var(--text-muted); padding: 40px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <p style="margin-top: 8px;">No requests found.</p>
                    </td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
</body>
</html>
