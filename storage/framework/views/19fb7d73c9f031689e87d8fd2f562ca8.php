<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="<?php echo e(asset('js/chart.min.js')); ?>?v=2"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
        .sidebar-profile h3 { font-size: 16px; color: #ffffff; font-weight: 600; margin-top: 8px; }
        .sidebar-profile p { font-size: 12px; color: var(--sidebar-text); }
        .sidebar-heading { padding: 16px 20px 8px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #475569; letter-spacing: 0.05em; }
        .nav-btn { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: var(--sidebar-text); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent; cursor: pointer; background: none; border-top: none; border-right: none; border-bottom: none; }
        .nav-btn:hover { background: var(--sidebar-active); color: #ffffff; border-left-color: var(--primary); }
        .nav-btn.active { background: var(--sidebar-active); color: #ffffff; border-left-color: var(--primary); }
        .nav-btn.logout { margin-top: auto; border-top: 1px solid #1e293b; color: #ef4444; width: 100%; text-align: left; }
        .nav-btn.logout:hover { background: rgba(239, 68, 68, 0.1); }
        .main-wrapper { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #ffffff; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 50; }
        .topbar-left { font-size: 20px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .user-pill { display: flex; align-items: center; gap: 10px; padding: 6px 12px; background: var(--bg-main); border-radius: 999px; cursor: pointer; font-size: 14px; }
        .user-pill img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .content { padding: 32px; flex: 1; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .breadcrumb { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 16px 20px; background: #ffffff; border: 1px solid var(--border); border-radius: 8px; }
        .breadcrumb strong { font-size: 18px; color: var(--text-main); }
        .breadcrumb span { font-size: 14px; color: var(--text-muted); }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .dash-card { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s; box-shadow: var(--shadow-sm); }
        .dash-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); border-color: var(--primary); }
        .dash-card h2 { font-size: 28px; font-weight: 700; margin-bottom: 4px; color: var(--text-main); }
        .dash-card h3 { font-size: 16px; font-weight: 600; margin-bottom: 4px; color: var(--text-main); }
        .dash-card p { font-size: 13px; color: var(--text-muted); }
        .dash-card .card-icon { opacity: 0.6; }
        .card-primary { border-left: 4px solid var(--primary); } .card-primary h2 { color: var(--primary); }
        .card-danger { border-left: 4px solid var(--danger); } .card-danger h2 { color: var(--danger); }
        .card-success { border-left: 4px solid var(--success); } .card-success h2 { color: var(--success); }
        .card-warning { border-left: 4px solid var(--warning); } .card-warning h2 { color: var(--warning); }
        .card-info { border-left: 4px solid var(--info); } .card-info h2 { color: var(--info); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; background: #ffffff; border-radius: 8px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        th { background: #f8fafc; color: var(--text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--text-main); font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .btn-success { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--success); color: #ffffff; }
        .btn-success:hover { background: #059669; }
        .btn-danger { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--danger); color: #ffffff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-info { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--primary); color: #ffffff; }
        .btn-info:hover { background: var(--primary-hover); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-save { padding: 10px 24px; background: var(--primary); color: #ffffff; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-save:hover { background: var(--primary-hover); }
        .tab-btn { padding: 8px 16px; background: #ffffff; color: var(--text-main); border: 1px solid var(--border); border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; }
        .tab-btn:hover, .tab-btn.active { background: var(--bg-main); border-color: var(--primary); color: var(--primary); }
        .input-group { margin-bottom: 16px; }
        .input-group label { display: block; margin-bottom: 6px; color: var(--text-main); font-weight: 500; font-size: 13px; }
        .input-group input, .input-group select, .input-group textarea { width: 100%; padding: 10px 12px; background: #ffffff; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-size: 14px; transition: all 0.2s; font-family: 'Inter', sans-serif; }
        .input-group input:focus, .input-group select:focus, .input-group textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .input-group select option { background: #ffffff; color: var(--text-main); }
        .books-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .book-card { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: all 0.2s; box-shadow: var(--shadow-sm); }
        .book-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); border-color: var(--primary); }
        .book-card img { width: 100%; height: 240px; object-fit: cover; }
        .book-card-body { padding: 16px; }
        .book-card-title { font-size: 15px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .book-card-author { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
        .book-card-stock { display: flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 12px; }
        .profile-container { display: grid; grid-template-columns: 280px 1fr; gap: 32px; animation: fadeIn 0.3s ease-out; }
        .profile-left { text-align: center; }
        .profile-img-box { width: 180px; height: 180px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 4px solid #ffffff; box-shadow: var(--shadow); }
        .profile-img-box img { width: 100%; height: 100%; object-fit: cover; }
        .upload-form { display: flex; flex-direction: column; gap: 10px; align-items: center; }
        .profile-right { background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 14px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; display: flex; justify-content: center; align-items: center; z-index: 9999; animation: fadeOut 0.5s ease-out 0.5s forwards; }
        @keyframes fadeOut { to { opacity: 0; pointer-events: none; } }
        .loader { width: 48px; height: 48px; border: 4px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .section-title { margin-bottom: 20px; font-size: 18px; font-weight: 600; color: var(--text-main); }
        .add-form { background: #f8fafc; padding: 24px; margin-bottom: 24px; border-radius: 12px; border: 1px solid var(--border); }
        .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .chart-box { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; }
        .chart-box h3 { margin-bottom: 16px; }
        @media (max-width: 1024px) { .sidebar { width: 80px; } .sidebar-profile h3, .sidebar-profile p, .nav-btn span { display: none; } .main-wrapper { margin-left: 80px; } }
        @media (max-width: 768px) { .profile-container { grid-template-columns: 1fr; } .dashboard-grid { grid-template-columns: 1fr; } .chart-grid { grid-template-columns: 1fr; } }
        table.dataTable { border-collapse: separate !important; border-spacing: 0; background: #ffffff; border-radius: 8px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); width: 100% !important; }
        table.dataTable thead th { background: #f8fafc; color: var(--text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); border-right: none !important; }
        table.dataTable thead th.sorting:before, table.dataTable thead th.sorting:after { display: none !important; }
        table.dataTable tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--text-main); font-size: 14px; }
        table.dataTable tbody tr:last-child td { border-bottom: none; }
        table.dataTable tbody tr:hover td { background: #f8fafc; }
        table.dataTable thead .sorting { background-image: none !important; }
        .dt-container .dt-layout-row { margin-bottom: 16px; }
        .dt-container .dt-paging .dt-paging-button { padding: 4px 12px; border: 1px solid var(--border); border-radius: 6px; background: #ffffff; color: var(--text-main); font-size: 13px; cursor: pointer; margin: 0 2px; }
        .dt-container .dt-paging .dt-paging-button.current { background: var(--primary); color: #ffffff; border-color: var(--primary); }
        .dt-container .dt-paging .dt-paging-button:hover { background: var(--bg-main); }
        .dt-container .dt-length select { padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; background: #ffffff; font-size: 13px; }
        .dt-container .dt-search input { padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; background: #ffffff; }
        .dt-container .dt-info { font-size: 13px; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loader"><div class="loader"></div></div>

    <div class="sidebar">
        <div class="sidebar-profile">
            <img src="<?php echo e($admin->profile_image_url); ?>" alt="Profile">
            <p>Welcome Admin!</p>
            <h3><?php echo e($admin->name ?? explode('@', $admin->email)[0]); ?></h3>
        </div>
        <div class="sidebar-heading">General</div>
        <a href="#" onclick="showTab('dash'); return false;" class="nav-btn active">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Dashboard</span>
        </a>
        <a href="#" onclick="showTab('profile'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Profile</span>
        </a>
        <a href="#" onclick="showTab('manage_users'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Manage Users</span>
        </a>
        <a href="#" onclick="showTab('view_books'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            <span>Books List</span>
        </a>
        <a href="#" onclick="showTab('manage_books'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            <span>Manage Books</span>
        </a>
        <a href="#" onclick="showTab('issued_books'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Issued Books</span>
        </a>
        <a href="#" onclick="showTab('fines'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            <span>Fines</span>
        </a>
        <a href="#" onclick="showTab('req_books'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            <span>Requested Books</span>
        </a>
        <a href="#" onclick="showTab('system_logs'); return false;" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span>System Logs</span>
        </a>
        <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="nav-btn logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <div class="main-wrapper">
        <div class="topbar">
            <div class="topbar-left">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Library
            </div>
            <div style="font-size: 14px; color: var(--text-muted);">Librarian Control Panel</div>
            <div class="topbar-right">
                <div class="notif-wrapper" style="position:relative;">
                    <button onclick="toggleNotif()" style="background:none; border:none; cursor:pointer; position:relative; padding:8px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span id="notif-badge" style="display:none; position:absolute; top:2px; right:2px; background:var(--danger); color:#fff; font-size:10px; font-weight:600; min-width:18px; height:18px; border-radius:9px; display:none; align-items:center; justify-content:center; border:2px solid #fff;"></span>
                    </button>
                    <div id="notif-dropdown" style="display:none; position:absolute; top:100%; right:0; width:380px; background:#fff; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.15); border:1px solid var(--border); z-index:200; max-height:480px; overflow:hidden;">
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 18px; border-bottom:1px solid var(--border);">
                            <strong style="font-size:15px;">Notifications</strong>
                            <button onclick="markAllRead()" style="background:none; border:none; color:var(--primary); font-size:12px; cursor:pointer; font-weight:500;">Mark all read</button>
                        </div>
                        <div id="notif-list" style="max-height:400px; overflow-y:auto;">
                            <div style="padding:24px; text-align:center; color:var(--text-muted); font-size:14px;">No notifications</div>
                        </div>
                    </div>
                </div>
                <div class="user-pill" onclick="showTab('profile'); return false;">
                    <img src="<?php echo e($admin->profile_image_url); ?>" width="32" height="32" style="object-fit: cover;">
                    <span><?php echo e($admin->name ?? explode('@', $admin->email)[0]); ?></span>
                </div>
            </div>
        </div>

        <div class="content">
            <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
            <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <div id="dash" class="tab-content active">
                <div class="breadcrumb"><strong>Dashboard</strong><span>Home / Dashboard</span></div>
                <div class="dashboard-grid">
                    <div class="dash-card card-primary" onclick="showTab('manage_users')">
                        <div><h2><?php echo e($stats['members']); ?></h2><p>Members</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                    </div>
                    <div class="dash-card card-danger" onclick="showTab('issued_books')">
                        <div><h2><?php echo e($stats['issued']); ?></h2><p>Issued Books</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg></div>
                    </div>
                    <div class="dash-card card-success" onclick="showTab('view_books')">
                        <div><h2><?php echo e($stats['available']); ?></h2><p>Available Books</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></div>
                    </div>
                    <div class="dash-card card-warning" onclick="showTab('fines')">
                        <div><h2>₱<?php echo e(number_format($stats['total_fine'], 2)); ?></h2><p>Fines</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                    </div>
                    <div class="dash-card card-danger" onclick="showTab('manage_books')">
                        <div><h3>Manage Books</h3><p>Add, edit, or remove books</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></div>
                    </div>
                    <div class="dash-card card-primary" onclick="showTab('manage_users')">
                        <div><h3>Manage Users</h3><p>View and manage user accounts</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                    </div>
                    <div class="dash-card card-info" onclick="showTab('system_logs')">
                        <div><h3>System Logs</h3><p>View system activity logs</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                    </div>
                    <div class="dash-card card-warning" onclick="showTab('req_books')">
                        <div><h3>Requested Books</h3><p><?php echo e($stats['pending']); ?> Pending Requests</p></div>
                        <div class="card-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></div>
                    </div>
                </div>

                <div class="chart-grid">
                    <div class="chart-box">
                        <h3 class="section-title">Books by Status</h3>
                        <canvas id="booksStatusChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3 class="section-title">Book Requests (Last 6 Months)</h3>
                        <canvas id="requestsChart"></canvas>
                    </div>
                </div>

                <div class="chart-grid">
                    <div class="chart-box">
                        <h3 class="section-title">Fines by Month (PHP)</h3>
                        <canvas id="finesChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3 class="section-title">Books by Category</h3>
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="profile" class="tab-content">
                <div class="breadcrumb"><strong>Profile</strong><span>Home / Profile</span></div>
                <div class="profile-container">
                    <div class="profile-left">
                        <div class="profile-img-box"><img src="<?php echo e($admin->profile_image_url); ?>" alt="Profile"></div>
                        <form method="POST" action="<?php echo e(route('admin.profile.image')); ?>" enctype="multipart/form-data" class="upload-form">
                            <?php echo csrf_field(); ?>
                            <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                            <button type="submit" class="btn-info">Upload Image</button>
                        </form>
                    </div>
                    <div class="profile-right">
                        <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="input-group"><label>Name:</label><input type="text" name="name" value="<?php echo e($admin->name ?? ''); ?>" placeholder="Enter your name"></div>
                            <div class="input-group"><label>Email (Read Only):</label><input type="email" value="<?php echo e($admin->email); ?>" readonly></div>
                            <div class="input-group"><label>Phone No:</label><input type="text" name="phone" value="<?php echo e($admin->phone ?? ''); ?>" placeholder="Enter phone number"></div>
                        <div class="input-group"><label>Address:</label><textarea name="address" rows="4" placeholder="Enter full address"><?php echo e($admin->address ?? ''); ?></textarea></div>
                        <button type="submit" class="btn-save">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Update Profile
                        </button>
                    </form>
                    <hr style="margin: 30px 0; border: none; border-top: 1px solid var(--border);">
                    <h3 class="section-title">Change Password</h3>
                    <form method="POST" action="<?php echo e(route('admin.profile.password')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="input-group"><label>Current Password:</label><input type="password" name="current_password" required></div>
                        <div class="input-group"><label>New Password:</label><input type="password" name="password" required></div>
                        <div class="input-group"><label>Confirm Password:</label><input type="password" name="password_confirmation" required></div>
                        <button type="submit" class="btn-save">Change Password</button>
                    </form>
                </div>
                </div>
            </div>

            <div id="view_books" class="tab-content">
                <div class="breadcrumb"><strong>Books List</strong><span>Home / Books List</span></div>
                <h3 class="section-title">Library Catalog</h3>
                <div class="books-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="book-card">
                        <img src="<?php echo e($b->image_url ?? asset('imagess.png')); ?>" alt="Book Cover">
                        <div class="book-card-body">
                            <div class="book-card-title"><?php echo e($b->title); ?></div>
                            <div class="book-card-author"><?php echo e($b->author ?? 'Unknown'); ?></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                <span class="book-card-stock"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>Stock: <?php echo e($b->stock ?? 1); ?></span>
                                <span class="status-<?php echo e($b->status); ?>"><?php echo e($b->status); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p style="text-align:center; width:100%; color: var(--text-muted);">No books in the library.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="manage_books" class="tab-content">
                <div class="breadcrumb"><strong>Manage Books</strong><span>Home / Manage Books</span></div>
                <button onclick="document.getElementById('add_f').style.display='block'" class="tab-btn active" style="margin-bottom:15px;">+ Add New Book</button>
                <div id="add_f" style="display:none;" class="add-form">
                    <form method="POST" action="<?php echo e(route('admin.books.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div class="input-group" style="margin-bottom: 0;"><label>ISBN</label><input type="text" name="isbn" placeholder="Enter ISBN" required></div>
                            <div class="input-group" style="margin-bottom: 0;"><label>Title</label><input type="text" name="title" placeholder="Enter book title" required></div>
                            <div class="input-group" style="margin-bottom: 0;"><label>Author</label><input type="text" name="author" placeholder="Enter author name" required></div>
                            <div class="input-group" style="margin-bottom: 0;"><label>Stock</label><input type="number" name="stock" placeholder="Quantity" value="1" min="1" required></div>
                            <div class="input-group" style="margin-bottom: 0;"><label>Category</label><select name="category"><option value="" disabled selected>-- Select Category --</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                            <div class="input-group" style="margin-bottom: 0;"><label>Book Image</label><input type="file" name="image" accept="image/*" style="padding: 8px;"></div>
                        </div>
                        <button type="submit" class="btn-success">Save Book</button>
                    </form>
                </div>
                <button onclick="document.getElementById('add_cat').style.display='block'" class="tab-btn" style="margin-bottom:15px;">+ Add Category</button>
                <div id="add_cat" style="display:none;" class="add-form">
                    <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>" style="display: flex; gap: 12px; align-items: flex-end;">
                        <?php echo csrf_field(); ?>
                        <div class="input-group" style="margin-bottom: 0; flex: 1;"><label>New Category Name</label><input type="text" name="name" placeholder="Enter category name" required></div>
                        <button type="submit" class="btn-success" style="padding: 12px 24px;">Add Category</button>
                    </form>
                </div>
                <h3 class="section-title">Book Inventory</h3>
                <table id="books-table" class="display" style="width:100%">
                    <thead>
                        <tr><th>ISBN</th><th>Title</th><th>Author</th><th>Category</th><th>Stock</th><th>Status</th><th>Action</th></tr>
                    </thead>
                </table>
            </div>

            <div id="req_books" class="tab-content">
                <div class="breadcrumb"><strong>Requested Books</strong><span>Home / Requested Books</span></div>
                <h3 class="section-title">Pending Requests</h3>
                <table>
                    <tr><th>Student</th><th>Book</th><th>Requested On</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($row->student_name); ?></strong><br><span style="color: var(--text-muted); font-size: 13px;">ID: <?php echo e($row->student_id_num); ?></span></td>
                        <td><strong><?php echo e($row->book->title ?? 'N/A'); ?></strong></td>
                        <td><?php echo e($row->req_date); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('admin.requests.approve', $row->id)); ?>" style="display:inline;"><?php echo csrf_field(); ?><button type="submit" class="btn-success btn-sm" style="margin-right: 8px;">Approve</button></form>
                            <form method="POST" action="<?php echo e(route('admin.requests.deny', $row->id)); ?>" style="display:inline;"><?php echo csrf_field(); ?><button type="submit" class="btn-danger btn-sm">Deny</button></form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" style="text-align:center; color: var(--text-muted);">No pending requests.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <div id="manage_users" class="tab-content">
                <div class="breadcrumb"><strong>Manage Users</strong><span>Home / Manage Users</span></div>
                <h3 class="section-title">Administrators</h3>
                <table>
                    <tr><th>User ID</th><th>Email</th><th>Name</th><th>Verified</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($u->id); ?></td><td><?php echo e($u->email); ?></td><td><?php echo e($u->name ?? '-'); ?></td><td><?php echo e($u->email_verified ? 'Yes' : 'No'); ?></td>
                        <td>
                            <?php if($u->id != $admin->id): ?>
                            <form method="POST" action="<?php echo e(route('admin.users.destroy', $u->id)); ?>" style="display:inline;" onsubmit="return confirm('Delete this admin?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                            <?php else: ?>
                            <span style="color:var(--text-muted);">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No admins registered yet.</td></tr>
                    <?php endif; ?>
                </table>
                <h3 class="section-title" style="margin-top: 40px;">Students</h3>
                <table>
                    <tr><th>User ID</th><th>Email</th><th>Name</th><th>Verified</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($u->id); ?></td><td><?php echo e($u->email); ?></td><td><?php echo e($u->name ?? '-'); ?></td><td><?php echo e($u->email_verified ? 'Yes' : 'No'); ?></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('admin.users.destroy', $u->id)); ?>" style="display:inline;" onsubmit="return confirm('Delete this student?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No students registered yet.</td></tr>
                    <?php endif; ?>
                </table>
                <div style="margin-top: 16px;"><?php echo e($students->links()); ?></div>
            </div>

            <div id="system_logs" class="tab-content">
                <div class="breadcrumb"><strong>System Logs</strong><span>Home / System Logs</span></div>
                <h3 class="section-title">System History</h3>
                <table>
                    <tr><th>Time</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr><td><?php echo e($l->timestamp); ?></td><td><?php echo e($l->description); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="2" style="text-align:center; color: var(--text-muted); padding: 40px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <p style="margin-top: 8px;">No logs yet. Activity will appear here as admins perform actions.</p>
                    </td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <div id="issued_books" class="tab-content">
                <div class="breadcrumb"><strong>Issued Books</strong><span>Home / Issued Books</span></div>
                <h3 class="section-title">Currently Borrowed Books</h3>
                <table>
                    <tr><th>Student</th><th>Book</th><th>Issue Date</th><th>Return Due Date</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $issuedBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($row->student_name); ?></strong><br><span style="color: var(--text-muted); font-size: 13px;">ID: <?php echo e($row->student_id_num); ?></span></td>
                        <td><strong><?php echo e($row->book->title ?? 'N/A'); ?></strong></td>
                        <td><?php echo e($row->action_date); ?></td>
                        <td><strong style="color: var(--danger);"><?php echo e($row->return_date); ?></strong></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('admin.requests.return', $row->id)); ?>" style="display:inline;"><?php echo csrf_field(); ?><button type="submit" class="btn-success btn-sm">Mark Returned</button></form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No issued books.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <div id="fines" class="tab-content">
                <div class="breadcrumb"><strong>Fines Management</strong><span>Home / Fines</span></div>
                <h3 class="section-title">Outstanding Fines</h3>
                <div class="add-form" style="margin-bottom: 30px;">
                    <form method="POST" action="<?php echo e(route('admin.fine-rate')); ?>" style="display: flex; gap: 12px; align-items: flex-end;">
                        <?php echo csrf_field(); ?>
                        <div class="input-group" style="margin-bottom: 0; flex: 1;"><label>Fine Rate (PHP per day)</label><input type="number" name="fine_rate" value="<?php echo e($stats['fine_rate']); ?>" min="1" required></div>
                        <button type="submit" style="padding: 12px 24px; background: var(--warning); color: #ffffff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Update Rate</button>
                    </form>
                </div>
                <table>
                    <tr><th>Student</th><th>Book</th><th>Due Date</th><th>Days Late</th><th>Fine</th><th>Status</th><th>Action</th></tr>
                    <?php $__empty_1 = true; $__currentLoopData = $fines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($f->user->name ?? $f->student_name); ?></strong><br><span style="color: var(--text-muted); font-size: 13px;"><?php echo e($f->user->email ?? ''); ?></span></td>
                        <td><strong><?php echo e($f->book->title ?? 'N/A'); ?></strong></td>
                        <td><?php echo e($f->return_date); ?></td>
                        <td><span style="color: var(--danger);"><?php echo e($f->days_late ?? 0); ?> days</span></td>
                        <td><strong style="color: var(--warning);">₱<?php echo e(number_format($f->fine, 2)); ?></strong></td>
                        <td><?php echo $f->fine_paid ? '<span class="status-RETURNED">Paid</span>' : '<span class="status-BORROWED">Unpaid</span>'; ?></td>
                        <td><?php if(!$f->fine_paid): ?><form method="POST" action="<?php echo e(route('admin.requests.pay-fine', $f->id)); ?>" style="display:inline;"><?php echo csrf_field(); ?><button type="submit" class="btn-success btn-sm">Mark Paid</button></form><?php endif; ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No fines.</td></tr>
                    <?php endif; ?>
                </table>
                <div style="margin-top: 16px;"><?php echo e($fines->links()); ?></div>
            </div>
        </div>
    </div>

    <script>
        // Notifications
        let notifOpen = false;
        function toggleNotif() {
            notifOpen = !notifOpen;
            document.getElementById('notif-dropdown').style.display = notifOpen ? 'block' : 'none';
            if (notifOpen) fetchNotifs();
        }
        function fetchNotifs() {
            fetch('<?php echo e(url("admin/notifications")); ?>')
                .then(r => r.json())
                .then(d => {
                    const badge = document.getElementById('notif-badge');
                    if (d.unread_count > 0) {
                        badge.style.display = 'flex';
                        badge.textContent = d.unread_count > 99 ? '99+' : d.unread_count;
                    } else {
                        badge.style.display = 'none';
                    }
                    const list = document.getElementById('notif-list');
                    if (d.notifications.length === 0) {
                        list.innerHTML = '<div style="padding:24px; text-align:center; color:var(--text-muted); font-size:14px;">No notifications</div>';
                    } else {
                        list.innerHTML = d.notifications.map(n => {
                            const icon = n.data.type === 'new_request'
                                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>'
                                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>';
                            return '<div onclick="markRead(\'' + n.id + '\')" style="display:flex; gap:12px; padding:14px 18px; border-bottom:1px solid var(--border); cursor:pointer; transition:background 0.15s;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'\'">' +
                                '<div style="flex-shrink:0; margin-top:2px;">' + icon + '</div>' +
                                '<div style="flex:1; min-width:0;">' +
                                '<div style="font-size:13px; color:var(--text-main); line-height:1.4;">' + n.data.message + '</div>' +
                                '<div style="font-size:11px; color:var(--text-muted); margin-top:4px;">' + n.created_at + '</div>' +
                                '</div></div>';
                        }).join('');
                    }
                });
        }
        function markRead(id) {
            fetch('<?php echo e(url("admin/notifications")); ?>/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
                .then(() => { notifOpen = false; document.getElementById('notif-dropdown').style.display = 'none'; fetchNotifs(); });
        }
        function markAllRead() {
            fetch('<?php echo e(url("admin/notifications/read-all")); ?>', { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
                .then(() => { fetchNotifs(); });
        }
        document.addEventListener('click', function(e) {
            if (notifOpen && !e.target.closest('.notif-wrapper')) {
                notifOpen = false;
                document.getElementById('notif-dropdown').style.display = 'none';
            }
        });
        setInterval(fetchNotifs, 10000);
        setTimeout(fetchNotifs, 1000);

        // Edit Book Modal
        function editBook(id, title, author, category, stock) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_f').style.display = 'block';
        }
        function closeEdit() { document.getElementById('edit_f').style.display = 'none'; }

        // URL Hash Support
        window.addEventListener('hashchange', function() {
            var hash = window.location.hash.substring(1);
            if (hash) showTab(hash);
        });
        window.addEventListener('load', function() {
            setTimeout(function() { document.getElementById('loader').style.display = 'none'; }, 800);
            var hash = window.location.hash.substring(1);
            if (hash) { showTab(hash); }
            else {
                document.querySelectorAll('.tab-content').forEach(d => { d.style.display = 'none'; d.classList.remove('active'); });
                document.getElementById('dash').style.display = 'block';
                document.getElementById('dash').classList.add('active');
                document.querySelector('.nav-btn[onclick*="dash"]').classList.add('active');
            }
        });

        function showTab(id) {
            document.querySelectorAll('.tab-content').forEach(d => { d.style.display = 'none'; d.classList.remove('active'); });
            var target = document.getElementById(id);
            if (target) { target.style.display = 'block'; target.classList.add('active'); }
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            var activeBtn = document.querySelector('.nav-btn[onclick*="' + id + '"]');
            if (activeBtn) activeBtn.classList.add('active');
            window.location.hash = id;
            if (id === 'manage_books' && window.initBooksTable) {
                setTimeout(window.initBooksTable, 100);
            }
        }

        window.booksTable = null;
        window.initBooksTable = function() {
            if (window.booksTable) {
                window.booksTable.columns.adjust().draw();
                return;
            }
            if (!$.fn.DataTable) return;
            window.booksTable = $('#books-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.books.data')); ?>',
                    type: 'GET'
                },
                columns: [
                    { data: 'id' },
                    { data: 'title' },
                    { data: 'author' },
                    { data: 'category' },
                    { data: 'stock' },
                    {
                        data: 'status',
                        render: function(data) {
                            return '<strong class="status-' + data + '">' + data + '</strong>';
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    search: 'Search books:',
                    emptyTable: 'No books found.'
                }
            });
        };

        window.addEventListener('load', function() {
            setTimeout(function() { document.getElementById('loader').style.display = 'none'; }, 800);

            if (typeof Chart !== 'undefined') {
                new Chart(document.getElementById('booksStatusChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($booksByStatus['labels'], 15, 512) ?>,
                        datasets: [{ data: <?php echo json_encode($booksByStatus['data'], 15, 512) ?>, backgroundColor: ['#10b981', '#ef4444'], borderWidth: 0 }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });

                new Chart(document.getElementById('requestsChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($monthLabels, 15, 512) ?>,
                        datasets: [{ label: 'Requests', data: <?php echo json_encode($requestsByMonth, 15, 512) ?>, borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.4 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                });

                new Chart(document.getElementById('finesChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($monthLabels, 15, 512) ?>,
                        datasets: [{ label: 'Fines (PHP)', data: <?php echo json_encode($finesByMonth, 15, 512) ?>, backgroundColor: '#f59e0b', borderRadius: 6 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });

                new Chart(document.getElementById('categoriesChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($booksByCategoryLabels, 15, 512) ?>,
                        datasets: [{ label: 'Books', data: <?php echo json_encode($booksByCategoryData, 15, 512) ?>, backgroundColor: '#3b82f6', borderRadius: 6 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                });
            }
        });
        </script>

        <!-- Edit Book Modal -->
        <div id="edit_f" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:200; justify-content:center; align-items:center;">
            <div style="background:#fff; padding:32px; border-radius:12px; width:500px; max-width:90%;">
                <h3 style="margin-bottom:20px;">Edit Book</h3>
                <form method="POST" action="<?php echo e(route('admin.books.update')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="book_id" id="edit_id">
                    <div class="input-group"><label>Title</label><input type="text" name="title" id="edit_title" required></div>
                    <div class="input-group"><label>Author</label><input type="text" name="author" id="edit_author"></div>
                    <div class="input-group"><label>Category</label><select name="category" id="edit_category"><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div class="input-group"><label>Stock</label><input type="number" name="stock" id="edit_stock" min="1" required></div>
                    <div style="display:flex; gap:12px; margin-top:20px;">
                        <button type="submit" class="btn-success">Update Book</button>
                        <button type="button" onclick="closeEdit()" class="tab-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\LBS\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>