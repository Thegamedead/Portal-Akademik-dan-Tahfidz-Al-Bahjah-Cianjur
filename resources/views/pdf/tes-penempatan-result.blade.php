<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Tes Penempatan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f0f0f0; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Hasil Tes Penempatan Hafalan Quran</h2>
        <p>Sistem Informasi Tahfidz SMP</p>
    </div>

    <div class="section">
        <h3>Data Siswa</h3>
        <table class="table">
            <tr>
                <th width="30%">Nama Lengkap</th>
                <td>{{ $tes->siswa->nama }}</td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $tes->siswa->nis }}</td>
            </tr>
            <tr>
                <th>Tanggal Tes</th>
                <td>{{ $tes->tanggal_tes->tgl() }}</td>
            </tr>
            <tr>
                <th>Guru Penguji</th>
                <td>{{ $tes->guruPenguji->nama }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Hasil Tes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Juz</th>
                    <th>Surah</th>
                    <th>Ayat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tes->hasilTes as $hasil)
                <tr>
                    <td>{{ $hasil->juz->nama }}</td>
                    <td>{{ $hasil->surah->nama_surah }}</td>
                    <td>Ayat {{ $hasil->ayat_mulai }} - {{ $hasil->ayat_selesai }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Evaluasi Guru</h3>
        <table class="table">
            <tr>
                <th width="30%">Catatan Kualitas</th>
                <td>{{ $tes->catatan_kualitas }}</td>
            </tr>
            <tr>
                <th>Rekomendasi Tingkat</th>
                <td>{{ $tes->rekomendasi_tingkat?->value }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Baseline Kalkulasi Sistem</h3>
        <ul>
            <li>Total Ayat Dikuasai: {{ $baselineData['total_ayat'] ?? 0 }}</li>
            <li>Juz Dikuasai: {{ collect($baselineData['complete_juzs'] ?? [])->join(', ') ?: '-' }}</li>
        </ul>
    </div>
</body>
</html>
