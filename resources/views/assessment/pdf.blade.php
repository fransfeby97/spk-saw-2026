<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian {{ $employee->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #1e40af;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: normal;
            color: #666;
        }

        .info-section {
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f1f5f9;
        }

        .result-box {
            background: #f0f9ff;
            border: 2px solid #1e40af;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .result-box h4 {
            color: #1e40af;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
        }

        .result-row:last-child {
            border-bottom: none;
        }

        .result-name {
            font-weight: bold;
            color: #1e293b;
        }

        .result-score {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }

        .signature-section {
            margin-top: 20px;
        }

        .signature-row {
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 3px;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 3px;
            font-size: 11px;
        }

        .signature-title {
            color: #666;
            font-size: 10px;
        }

        .date-location {
            text-align: right;
            margin-bottom: 10px;
            color: #666;
            font-size: 10px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PENILAIAN KINERJA PEGAWAI</h1>
        <h2>Puskesmas Ukui</h2>
    </div>

    <div class="info-section">
        <table style="border: none; background: transparent;">
            <tr style="background: transparent;">
                <td style="border: none; text-align: left; width: 130px; font-weight: bold; color: #475569;">Nama
                    Pegawai</td>
                <td style="border: none; text-align: left;">: {{ $employee->name }}</td>
            </tr>
            <tr style="background: transparent;">
                <td style="border: none; text-align: left; width: 130px; font-weight: bold; color: #475569;">Jabatan
                </td>
                <td style="border: none; text-align: left;">: {{ $employee->position ?? '-' }}</td>
            </tr>
            <tr style="background: transparent;">
                <td style="border: none; text-align: left; width: 130px; font-weight: bold; color: #475569;">Periode
                    Penilaian</td>
                <td style="border: none; text-align: left;">: {{ $period->name }}</td>
            </tr>
        </table>
    </div>

    <h3 style="margin-bottom: 8px; color: #1e40af; font-size: 12px;">Nilai Kriteria</h3>
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Kriteria</th>
                <th>Bobot</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criteria as $c)
                <tr>
                    <td style="text-align: left;">{{ $c->name }}</td>
                    <td>{{ number_format($c->weight * 100, 0) }}%</td>
                    <td><strong>{{ isset($ratings[$c->id]) ? number_format($ratings[$c->id]->value, 2) : '-' }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="result-box">
        <h4>HASIL AKHIR PENILAIAN</h4>
        <table style="border: none; margin-bottom: 0;">
            <tr style="background: transparent;">
                <td style="border: none; text-align: left; font-weight: bold; color: #1e293b; font-size: 14px;">
                    {{ $employee->name }}
                </td>
                <td style="border: none; text-align: center; width: 50px;">:</td>
                <td style="border: none; text-align: left; font-size: 14px;">
                    <span style="color: #666;">{{ number_format($finalScore, 4) }}</span>
                    <span style="margin: 0 10px;">→</span>
                    <strong style="font-size: 20px; color: #1e40af;">{{ round($finalScore * 100) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <p class="date-location">Pekanbaru, {{ now()->translatedFormat('d F Y') }}</p>
        <div class="signature-row">
            <div class="signature-box">
                <p style="margin-bottom: 3px; font-size: 10px;">Mengetahui,</p>
                <p><strong>Kepala HRD</strong></p>
                <div class="signature-line"></div>
                <p class="signature-name">____________________</p>
                <p class="signature-title">NIP. ____________________</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Pendukung Keputusan Penilaian Kinerja Pegawai</p>
    </div>
</body>

</html>