<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Login - {{ ucfirst($tipe) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 10px;
        }
        .print-btn {
            display: block;
            width: 220px;
            margin: 0 auto 12px;
            padding: 10px 16px;
            background-color: #1B5E4F;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .print-btn:hover {
            background-color: #144C3F;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5mm;
            max-width: 190mm;
            margin: 0 auto;
        }
        .card {
            background: white;
            border: 1.5px dashed #9CA3AF;
            border-radius: 4px;
            padding: 6px;
            page-break-inside: avoid;
            height: 54mm;
            display: flex;
            flex-direction: column;
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 5px;
            border-bottom: 1.5px solid #1B5E4F;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }
        .logo-container {
            flex-shrink: 0;
        }
        .logo-sekolah {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }
        .header-text {
            flex: 1;
            min-width: 0;
        }
        .card-header h2 {
            margin: 0;
            color: #1B5E4F;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            line-height: 1.1;
        }
        .card-header p {
            margin: 1px 0 0;
            font-size: 6.5px;
            color: #6B7280;
            font-weight: 500;
            line-height: 1.1;
        }
        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5px;
        }
        .info-row {
            display: flex;
            font-size: 7.5px;
            line-height: 1.2;
        }
        .info-label {
            width: 38px;
            font-weight: 600;
            color: #4B5563;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
            color: #111827;
            font-weight: 600;
            word-break: break-word;
        }
        .login-box {
            margin-top: 4px;
            background-color: #F0F9FF;
            border: 1px solid #BAE6FD;
            border-radius: 3px;
            padding: 4px 5px;
        }
        .login-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
            font-size: 7.5px;
        }
        .login-row:last-child {
            margin-bottom: 0;
        }
        .login-row span {
            color: #0369A1;
            font-weight: 600;
        }
        .login-box strong {
            color: #0C4A6E;
            font-size: 9px;
            letter-spacing: 0.3px;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer-note {
            text-align: center;
            font-size: 5.5px;
            color: #9CA3AF;
            margin-top: 3px;
            margin-bottom: 0;
            font-style: italic;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
            .card {
                border-color: #6B7280;
            }
            .grid {
                gap: 5mm;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Cetak Kartu Login</button>

    <div class="grid">
        @foreach($akuns as $akun)
            <div class="card">
                <div class="card-header">
                    @if($sekolah && $sekolah->logo)
                        <div class="logo-container">
                            <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" class="logo-sekolah">
                        </div>
                    @endif
                    <div class="header-text">
                        <h2>KARTU LOGIN SISTEM</h2>
                        <p>{{ $sekolah->nama_sekolah ?? 'Sistem Informasi Tahfidz' }}</p>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value">: {{ Str::limit($akun->nama, 26, '...') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">{{ $tipe === 'guru' ? 'NIY' : 'NIS' }}</div>
                        <div class="info-value">: {{ $tipe === 'guru' ? $akun->niy : $akun->nis }}</div>
                    </div>
                    @if($tipe === 'siswa')
                    <div class="info-row">
                        <div class="info-label">L/P</div>
                        <div class="info-value">: {{ $akun->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kelas</div>
                        <div class="info-value">: {{ $akun->kelas ? Str::afterLast($akun->kelas->nama_kelas, '-') : '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kelompok</div>
                        <div class="info-value">: {{ $akun->kelompok->nama_kelompok ?? 'Belum ditempatkan' }}</div>
                    </div>
                    @else
                    <div class="info-row">
                        <div class="info-label">L/P</div>
                        <div class="info-value">: {{ $akun->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jabatan</div>
                        <div class="info-value">: {{ Str::limit($akun->jabatan ?? '-', 28, '...') }}</div>
                    </div>
                    @endif
                </div>

                <div class="login-box">
                    <div class="login-row">
                        <span>Username</span>
                        <strong>{{ $tipe === 'guru' ? $akun->niy : $akun->nis }}</strong>
                    </div>
                    <div class="login-row">
                        <span>Password</span>
                        <strong>{{ $akun->password_text ?? 'N/A' }}</strong>
                    </div>
                </div>

                <p class="footer-note">Jaga kerahasiaan password • {{ now()->isoFormat('D MMM Y') }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>
