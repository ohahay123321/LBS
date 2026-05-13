<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt | LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 40px 20px; }
        .receipt { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 640px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .receipt-header { background: #0f172a; color: #ffffff; padding: 32px; text-align: center; }
        .receipt-header h1 { font-size: 22px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .receipt-header p { font-size: 13px; color: #94a3b8; margin-top: 6px; }
        .receipt-body { padding: 32px; }
        .receipt-divider { border: none; border-top: 2px dashed #e2e8f0; margin: 24px 0; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; }
        .info-row .label { font-size: 13px; color: #64748b; font-weight: 500; min-width: 140px; }
        .info-row .value { font-size: 14px; color: #1e293b; font-weight: 600; text-align: right; flex: 1; }
        .section-title { font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #0f172a; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .status-badge.approved { background: #d1fae5; color: #065f46; }
        .status-badge.denied { background: #fee2e2; color: #991b1b; }
        .status-badge.returned { background: #dbeafe; color: #1e40af; }
        .receipt-footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .receipt-footer p { font-size: 12px; color: #94a3b8; }
        .btn-back { display: inline-block; margin-top: 16px; padding: 10px 24px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-back:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .btn-print { display: inline-block; margin-top: 16px; margin-left: 8px; padding: 10px 24px; background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-print:hover { background: #f8fafc; border-color: #94a3b8; }
        @media print {
            body { background: #fff; padding: 20px; }
            .receipt { box-shadow: none; border: 1px solid #ddd; }
            .btn-back, .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h1>Book Request Receipt</h1>
            <p>Library Management System</p>
        </div>

        <div class="receipt-body">
            <div class="section-title">Request #{{ $request->id }}</div>

            <div class="info-row">
                <span class="label">Status</span>
                <span class="value"><span class="status-badge {{ strtolower($request->status) }}">{{ $request->status }}</span></span>
            </div>

            <hr class="receipt-divider">

            <div class="section-title">Student Information</div>
            <div class="info-row">
                <span class="label">Full Name</span>
                <span class="value">{{ $request->student_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Student Number</span>
                <span class="value">{{ $request->student_id_num }}</span>
            </div>
            <div class="info-row">
                <span class="label">Email</span>
                <span class="value">{{ $request->user->email ?? 'N/A' }}</span>
            </div>

            <hr class="receipt-divider">

            <div class="section-title">Book Information</div>
            <div class="info-row">
                <span class="label">Book Title</span>
                <span class="value">{{ $request->book->title ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Author</span>
                <span class="value">{{ $request->book->author ?? 'N/A' }}</span>
            </div>

            <hr class="receipt-divider">

            <div class="section-title">Request Timeline</div>
            <div class="info-row">
                <span class="label">Requested On</span>
                <span class="value">{{ $request->req_date ? $request->req_date->format('M d, Y h:i A') : 'N/A' }}</span>
            </div>
            @if($request->action_date && $request->admin)
            <div class="info-row">
                <span class="label">Approved by</span>
                <span class="value">{{ $request->admin->name }}</span>
            </div>
            @endif
            @if($request->action_date)
            <div class="info-row">
                <span class="label">{{ $request->status == 'DENIED' ? 'Denied On' : 'Approved On' }}</span>
                <span class="value">{{ $request->action_date->format('M d, Y h:i A') }}</span>
            </div>
            @endif
            @if($request->return_date)
            <div class="info-row">
                <span class="label">Return By</span>
                <span class="value">{{ $request->return_date->format('M d, Y h:i A') }}</span>
            </div>
            @endif
            @if($request->status == 'RETURNED' && $request->fine > 0)
            <div class="info-row">
                <span class="label">Fine</span>
                <span class="value" style="color: #ef4444;">₱{{ number_format($request->fine, 2) }} {{ $request->fine_paid ? '(Paid)' : '(Unpaid)' }}</span>
            </div>
            @endif
        </div>

        <div class="receipt-footer">
            <a href="{{ route('student.dashboard') }}" class="btn-back">Back to Dashboard</a>
            <button onclick="window.print()" class="btn-print">Print Receipt</button>
        </div>
    </div>
</body>
</html>
