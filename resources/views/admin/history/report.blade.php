<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Parkiran FILKOM UB</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
            color: #111;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 4px 0;
            font-size: 15px;
            font-weight: normal;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #444;
        }
        .meta-box {
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-size: 12px;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            width: 300px;
            margin-left: auto;
            border: 1px solid #333;
            padding: 8px;
            margin-bottom: 30px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .sign-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .sign-box {
            text-align: center;
            width: 200px;
        }
        .sign-space {
            height: 70px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 20px; font-weight: bold; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ CETAK / SIMPAN PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h2>UNIVERSITAS BRAWIJAYA</h2>
        <h3>FAKULTAS ILMU KOMPUTER</h3>
        <p>Gedung FILKOM UB, Jl. Veteran No. 8, Malang 65145 | Telp: (0341) 577911 | filkom.ub.ac.id</p>
        <p><strong>LAPORAN REKAPITULASI AKTIVITAS & TRANSAKSI PARKIR</strong></p>
    </div>

    <div class="meta-box">
        <div>
            <div><strong>Periode Laporan:</strong> {{ $startDate }} s/d {{ $endDate }}</div>
            <div><strong>Filter Jenis Kendaraan:</strong> {{ $type ?: 'Semua Kendaraan (Motor & Mobil)' }}</div>
        </div>
        <div style="text-align: right;">
            <div><strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i') }} WIB</div>
            <div><strong>Dicetak Oleh:</strong> {{ Auth::user()->name ?? 'Administrator' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kode Tiket</th>
                <th>Plat Nomor</th>
                <th>Jenis</th>
                <th>Area Parkir</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
                <th>Durasi</th>
                <th>Status Validasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $r)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $r->ticket_code }}</td>
                <td><strong>{{ $r->plate_number }}</strong></td>
                <td>{{ $r->vehicle_type }}</td>
                <td>{{ $r->area->name ?? '-' }}</td>
                <td>{{ $r->entry_time->format('d/m/Y H:i') }}</td>
                <td>{{ $r->exit_time ? $r->exit_time->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $r->formatted_duration }}</td>
                <td><span style="color: green; font-weight: bold;">Selesai (Bebas Biaya)</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">Tidak ada transaksi pada periode yang dipilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span>Total Kendaraan Keluar:</span>
            <strong>{{ $totalCount }} unit</strong>
        </div>
        <div class="summary-row">
            <span>Mobil:</span>
            <strong>{{ $totalMobil }} unit</strong>
        </div>
        <div class="summary-row">
            <span>Motor:</span>
            <strong>{{ $totalMotor }} unit</strong>
        </div>
        <div class="summary-row" style="border-top: 1px solid #333; padding-top: 4px; margin-top: 4px;">
            <span>Sistem Tarif:</span>
            <strong>Gratis (Fasilitas Kampus)</strong>
        </div>
    </div>

    <div class="sign-area">
        <div class="sign-box">
            <div>Mengetahui,</div>
            <div>Koordinator Pengelola Parkir</div>
            <div class="sign-space"></div>
            <div><strong>( ........................................ )</strong></div>
            <div>NIP. .....................................</div>
        </div>
        <div class="sign-box">
            <div>Malang, {{ date('d F Y') }}</div>
            <div>Petugas / Operator Sistem</div>
            <div class="sign-space"></div>
            <div><strong>{{ Auth::user()->name ?? 'Petugas Parkir' }}</strong></div>
            <div>Petugas Administrasi Parkir</div>
        </div>
    </div>
</body>
</html>
