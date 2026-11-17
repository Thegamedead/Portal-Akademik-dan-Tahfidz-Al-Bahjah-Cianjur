<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $label }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: "DejaVu Sans", sans-serif; font-size: 10px; color: #1A2422; }
.header { background: #1B5E4F; color: white; padding: 14px 16px; margin-bottom: 16px; }
.header h1 { font-size: 15px; font-weight: bold; margin-bottom: 3px; }
.header .meta { font-size: 8px; opacity: 0.8; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #1B5E4F; color: white; }
thead th { padding: 7px 10px; text-align: left; font-size: 9px; text-transform: uppercase; }
tbody tr:nth-child(even) { background: #f0faf7; }
tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; }
.alfa-kronis { background: #fdeaea !important; color: #9B3A3A; font-weight: bold; }
.footer { margin-top: 16px; font-size: 8px; color: #6b7280; text-align: right; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $label }}</h1>
    <div class="meta">Digenerate: {{ $generated }}</div>
</div>
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIS</th>
            <th>Kelompok</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alfa</th>
            <th>% Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr class="{{ ($item->total_alfa ?? 0) >= 3 ? 'alfa-kronis' : '' }}">
            <td>{{ $item->nama }}</td>
            <td>{{ $item->nis }}</td>
            <td>{{ $item->kelompok->nama ?? '-' }}</td>
            <td>{{ $item->total_hadir ?? 0 }}</td>
            <td>{{ $item->total_izin ?? 0 }}</td>
            <td>{{ $item->total_sakit ?? 0 }}</td>
            <td>{{ $item->total_alfa ?? 0 }}</td>
            <td>{{ number_format($item->persentase_kehadiran ?? 0, 1) }}%</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:16px;">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">Sistem Informasi Tahfidz SMP &bull; {{ $generated }}</div>
</body>
</html>
