<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | LMS</title>
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
        .sidebar-profile h3 { font-size: 16px; color: #ffffff; font-weight: 600; margin-top: 8px; }
        .sidebar-profile p { font-size: 12px; color: var(--sidebar-text); }
        .nav-btn { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: var(--sidebar-text); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent; cursor: pointer; background: none; border-top: none; border-right: none; border-bottom: none; }
        .nav-btn:hover { background: var(--sidebar-active); color: #ffffff; border-left-color: var(--primary); }
        .nav-btn.logout { margin-top: auto; border-top: 1px solid #1e293b; color: #ef4444; width: 100%; text-align: left; }
        .nav-btn.logout:hover { background: rgba(239, 68, 68, 0.1); }
        .content { margin-left: 260px; flex: 1; padding: 32px; }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 24px; color: var(--text-main); }
        .search-bar { display: flex; gap: 12px; margin-bottom: 24px; }
        .search-bar input { flex: 1; padding: 10px 14px; background: #ffffff; border: 1px solid var(--border); border-radius: 8px; color: var(--text-main); font-size: 14px; transition: all 0.2s; font-family: 'Inter', sans-serif; }
        .search-bar input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .search-bar button { padding: 10px 24px; background: var(--primary); color: #ffffff; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s; }
        .search-bar button:hover { background: var(--primary-hover); }
        .filter-buttons { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; background: #ffffff; color: var(--text-muted); border: 1px solid var(--border); border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .filter-btn:hover, .filter-btn.active { background: var(--bg-main); border-color: var(--primary); color: var(--primary); }
        .filter-btn.borrowed:hover, .filter-btn.borrowed.active { border-color: var(--danger); color: var(--danger); background: #fee2e2; }
        .filter-btn.outofstock:hover, .filter-btn.outofstock.active { border-color: var(--warning); color: var(--warning); background: #fef3c7; }
        .section-title { font-size: 14px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .books-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .book-card { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: all 0.2s; box-shadow: var(--shadow-sm); }
        .book-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); border-color: var(--primary); }
        .book-card-image { position: relative; width: 100%; height: 240px; overflow: hidden; }
        .book-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease; }
        .book-card:hover .book-card-image img { transform: scale(1.05); }
        .status-AVAILABLE { position: absolute; top: 12px; left: 12px; padding: 4px 10px; background: #d1fae5; color: #065f46; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-BORROWED { position: absolute; top: 12px; left: 12px; padding: 4px 10px; background: #fee2e2; color: #991b1b; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .book-card-body { padding: 16px; }
        .book-card-title { font-size: 15px; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .book-card-author { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
        .book-card-stock { display: flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 12px; }
        .request-form { margin-top: 16px; display: flex; flex-direction: column; gap: 10px; }
        .request-form input[type="text"] { width: 100%; padding: 10px 12px; background: #ffffff; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-size: 14px; transition: all 0.2s; font-family: 'Inter', sans-serif; }
        .request-form input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .request-form button { width: 100%; padding: 10px 20px; background: var(--primary); color: #ffffff; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; }
        .request-form button:hover { background: var(--primary-hover); }
        table { width: 100%; border-collapse: separate; border-spacing: 0; background: #ffffff; border-radius: 8px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        th { background: #f8fafc; color: var(--text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--text-main); font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .status-APPROVED { display: inline-block; padding: 4px 10px; background: #d1fae5; color: #065f46; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-DENIED { display: inline-block; padding: 4px 10px; background: #fee2e2; color: #991b1b; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .status-PENDING { display: inline-block; padding: 4px 10px; background: #fef3c7; color: #92400e; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 14px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; display: flex; justify-content: center; align-items: center; z-index: 9999; animation: fadeOut 0.5s ease-out 0.5s forwards; }
        @keyframes fadeOut { to { opacity: 0; pointer-events: none; } }
        .loader { width: 48px; height: 48px; border: 4px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .notice-box { padding: 12px 16px; background: #fee2e2; color: var(--danger); border-radius: 6px; font-size: 13px; text-align: center; margin-top: 12px; }
        .notice-box.warning { background: #fef3c7; color: var(--warning); }
        @media (max-width: 1024px) { .sidebar { width: 80px; } .sidebar-profile h3, .sidebar-profile p, .nav-btn span { display: none; } .content { margin-left: 80px; } }
        @media (max-width: 768px) { .books-grid { grid-template-columns: 1fr; } .content { padding: 20px; } .filter-buttons { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loader"><div class="loader"></div></div>

    <div class="sidebar">
        <div class="sidebar-profile">
            <img src="{{ asset('imagess.png') }}" alt="Profile">
            <p>Welcome Student!</p>
            <h3>{{ explode('@', Auth::guard('student')->user()->email)[0] }}</h3>
        </div>
        <a href="{{ route('2fa.setup') }}" class="nav-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <span>Security</span>
        </a>
        <form method="POST" action="{{ route('student.logout') }}" style="display:contents;">
            @csrf
            <button type="submit" class="nav-btn logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <div class="content">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <h1 style="margin-bottom:0;">Library Books</h1>
            <div class="notif-wrapper" style="position:relative;">
                <button onclick="toggleNotif()" style="background:none; border:none; cursor:pointer; position:relative; padding:8px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span id="notif-badge" style="display:none; position:absolute; top:2px; right:2px; background:var(--danger); color:#fff; font-size:10px; font-weight:600; min-width:18px; height:18px; border-radius:9px; align-items:center; justify-content:center; border:2px solid #fff;"></span>
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
        </div>

        <form method="get" class="search-bar">
            <input type="text" name="search" placeholder="Search by title, author, or ISBN..." value="{{ $search }}">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <button type="submit">Search</button>
        </form>

        <div class="filter-buttons">
            <a href="?filter=available&search={{ $search }}&category={{ $category }}" class="filter-btn {{ $filter == 'available' ? 'active' : '' }}">Available Books</a>
            <a href="?filter=borrowed&search={{ $search }}&category={{ $category }}" class="filter-btn borrowed {{ $filter == 'borrowed' ? 'active' : '' }}">Borrowed Books</a>
            <a href="?filter=outofstock&search={{ $search }}&category={{ $category }}" class="filter-btn outofstock {{ $filter == 'outofstock' ? 'active' : '' }}">Out of Stock</a>
        </div>

        <div style="margin-bottom: 30px;">
            <div class="section-title">Categories</div>
            <div class="filter-buttons">
                <a href="?filter={{ $filter }}&search={{ $search }}" class="filter-btn {{ empty($category) ? 'active' : '' }}">All</a>
                @foreach($categories as $cat)
                <a href="?filter={{ $filter }}&search={{ $search }}&category={{ $cat->name }}" class="filter-btn {{ $category == $cat->name ? 'active' : '' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>

        <div class="books-grid">
            @forelse($books as $b)
            <div class="book-card">
                <div class="book-card-image">
                    <img src="{{ $b->image_url ?? asset('imagess.png') }}" alt="Book Cover">
                    <div class="status-{{ $b->status }}">{{ $b->status }}</div>
                </div>
                <div class="book-card-body">
                    <div class="book-card-title">{{ $b->title }}</div>
                    <div class="book-card-author">{{ $b->author ?? 'Unknown' }}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                        <span class="book-card-stock"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>Stock: {{ $b->stock ?? 1 }}</span>
                    </div>

                    @if($b->status == 'AVAILABLE' && ($b->stock ?? 1) > 0)
                    <form method="POST" action="{{ route('student.request') }}" class="request-form">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $b->id }}">
                        <input type="hidden" name="student_name" value="{{ Auth::guard('student')->user()->name }}">
                        <input type="hidden" name="student_id_num" value="{{ Auth::guard('student')->user()->student_number }}">
                        <button type="submit">Request Book</button>
                    </form>
                    @elseif($b->status == 'BORROWED')
                    <div class="notice-box">Currently Borrowed</div>
                    @elseif(($b->stock ?? 1) == 0)
                    <div class="notice-box warning">Out of Stock</div>
                    @endif
                </div>
            </div>
            @empty
            <p style="text-align:center; width:100%; color: var(--text-muted);">No books found.</p>
            @endforelse
        </div>

        <h1 style="margin-top: 48px;">My Request History</h1>
        <table>
            <tr><th>Book</th><th>Status</th><th>Requested On</th><th>Admin Response</th><th>Action</th></tr>
            @forelse($myRequests as $h)
            <tr>
                <td><strong>{{ $h->book->title ?? 'N/A' }}</strong></td>
                <td class="status-{{ $h->status }}">{{ $h->status }}</td>
                <td>{{ $h->req_date }}</td>
                <td>
                    @if($h->status == 'APPROVED')
                    <span style="color: var(--success); font-weight: 600;">Accepted on:</span> {{ $h->action_date }}<br>
                    <span style="color: var(--danger); font-weight: 600;">Return by:</span> {{ $h->return_date }}<br>
                    <span style="color: var(--text-muted); font-size: 13px;">Approved by: {{ $h->admin->name ?? 'Unknown' }}</span>
                    @elseif($h->status == 'DENIED')
                    <span style="color: var(--danger); font-weight: 600;">Denied on:</span> {{ $h->action_date }}<br>
                    <span style="color: var(--text-muted); font-size: 13px;">Denied by: {{ $h->admin->name ?? 'Unknown' }}</span>
                    @elseif($h->status == 'RETURNED')
                    <span style="color: var(--primary); font-weight: 600;">Returned on:</span> {{ $h->action_date }}<br>
                    <span style="color: var(--text-muted); font-size: 13px;">Processed by: {{ $h->admin->name ?? 'Unknown' }}</span>
                    @else
                    <span style="color: var(--warning);">Waiting for approval...</span>
                    @endif
                </td>
                <td>
                    @if(in_array($h->status, ['APPROVED', 'RETURNED', 'DENIED']))
                    <a href="{{ route('student.receipt', $h->id) }}" style="display:inline-block; padding:6px 14px; background:var(--primary); color:#fff; border-radius:6px; font-size:12px; font-weight:500; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.background='var(--primary-hover)'" onmouseout="this.style.background='var(--primary)'">View Receipt</a>
                    @else
                    <span style="color: var(--text-muted); font-size:13px;">---</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No requests yet.</td></tr>
            @endforelse
        </table>
    </div>

    <script>
        window.addEventListener('load', function() { setTimeout(function() { document.getElementById('loader').style.display = 'none'; }, 800); });

        let notifOpen = false;
        function toggleNotif() {
            notifOpen = !notifOpen;
            document.getElementById('notif-dropdown').style.display = notifOpen ? 'block' : 'none';
            if (notifOpen) fetchNotifs();
        }
        function fetchNotifs() {
            fetch('{{ url("student/notifications") }}')
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
                            let icon;
                            if (n.data.type === 'request_approved') {
                                icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>';
                            } else if (n.data.type === 'book_returned') {
                                icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
                            } else {
                                icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                            }
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
            fetch('{{ url("student/notifications") }}/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => { notifOpen = false; document.getElementById('notif-dropdown').style.display = 'none'; fetchNotifs(); });
        }
        function markAllRead() {
            fetch('{{ url("student/notifications/read-all") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
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
    </script>
</body>
</html>
