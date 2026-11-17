<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $label }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1A2422; }
        
        .header { background: #1B5E4F; color: white; padding: 14px 16px; margin-bottom: 16px; }
        .header h1 { font-size: 15px; font-weight: bold; margin-bottom: 3px; }
        .header .meta { font-size: 8px; opacity: 0.8; }
        
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1B5E4F; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr:nth-child(even) { background: #f0faf7; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; }
        
        .badge-lancar { color: #1B5E4F; font-weight: bold; }
        .badge-kurang { color: #C9A227; font-weight: bold; }
        .badge-tidak { color: #9B3A3A; font-weight: bold; }
        
        .footer { margin-top: 16px; font-size: 8px; color: #6b7280; text-align: right; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $label }}</h1>
        <div class="meta">Digenerate: {{ $generated }}</div>
    </div>

    @yield('content')

    <div class="footer">
        Sistem Informasi Tahfidz SMP &bull; {{ $generated }}
    </div>
</body>
</html>
