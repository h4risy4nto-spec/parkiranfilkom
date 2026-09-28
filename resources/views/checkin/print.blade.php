<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Tiket - {{ $ticket->ticket_code }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 75mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            background: #fff;
            text-align: center;
        }
        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        .sub {
            font-size: 11px;
        }
        .qr-wrap {
            margin: 10px 0;
        }
        .plate {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
            border: 2px solid #000;
            padding: 4px;
            margin: 8px 0;
            display: inline-block;
        }
        .meta {
            text-align: left;
            font-size: 12px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 6px 0;
            margin: 8px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .footer {
            font-size: 10px;
            margin-top: 8px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; cursor: pointer;">[ CETAK TIKET ]</button>
        <button onclick="window.close()" style="padding: 6px 16px; margin-left: 8px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <div class="title">🅿️ PARKIRAN FILKOM UB</div>
        <div class="sub">Fakultas Ilmu Komputer Universitas Brawijaya</div>
        <div class="sub">Jl. Veteran No. 8, Malang</div>
    </div>

    <div class="qr-wrap">
        {!! $ticket->qr_code_svg !!}
    </div>

    <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px;">
        {{ $ticket->ticket_code }}
    </div>

    <div class="plate">
        {{ $ticket->plate_number }}
    </div>

    <div class="meta">
        <div class="meta-row">
            <span>Jenis:</span>
            <span><strong>{{ $ticket->vehicle_type }}</strong></span>
        </div>
        <div class="meta-row">
            <span>Area / Slot:</span>
            <span><strong>{{ $ticket->area->name ?? '-' }} / {{ $ticket->slot->slot_code ?? '-' }}</strong></span>
        </div>
        <div class="meta-row">
            <span>Masuk:</span>
            <span>{{ $ticket->entry_time->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="meta-row">
            <span>Petugas:</span>
            <span>{{ $ticket->operatorIn->name ?? 'Sistem' }}</span>
        </div>
    </div>

    <div class="footer">
        <div>* Simpan tiket untuk barcode keluar *</div>
        <div>Tiket hilang dikenakan denda sesuai regulasi</div>
        <div>Terima kasih atas kunjungan Anda</div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
