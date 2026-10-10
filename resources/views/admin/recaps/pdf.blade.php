<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi — Resvi</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #111111;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #111111;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 11px;
            color: #555555;
            margin-top: 3px;
        }

        .meta-box {
            background-color: #f8f9fa;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            font-size: 10.5px;
        }

        .meta-label {
            color: #555555;
            font-weight: 600;
            width: 130px;
        }

        .meta-value {
            color: #000000;
            font-weight: bold;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background-color: #222222;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #222222;
            text-align: center;
        }

        table.data-table td {
            padding: 7px 10px;
            border: 1px solid #d1d5db;
            font-size: 10.5px;
            color: #111111;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-box {
            margin-top: 16px;
            width: 100%;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 12px;
            font-size: 10px;
            color: #111111;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #666666;
            text-align: right;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <!-- Kop Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="header-title">Resvi — Laporan Eksekutif</div>
                <div class="header-subtitle">
                    Sistem Tata Kelola Fasilitas Kampus & Pelaporan Operasional Terpadu
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 12px; font-weight: bold; color: #000000;">
                    {{ $data['metric'] === 'occupancy' ? 'REKAPITULASI TINGKAT OKUPANSI' : 'REKAPITULASI FREKUENSI KERUSAKAN' }}
                </div>
                <div style="font-size: 10px; color: #555555;">
                    Pengelompokan: {{ $data['group_by'] === 'facility' ? 'Per Fasilitas' : 'Per Lokasi' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Meta Information -->
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Periode Kalender</td>
                <td class="meta-value">: {{ $data['period']['month_name'] }} {{ $data['period']['year'] }} ({{ $data['period']['days_in_month'] }} Hari)</td>
                <td class="meta-label">Total Entitas Data</td>
                <td class="meta-value">: {{ $data['summary']['total_items'] }} {{ $data['group_by'] === 'facility' ? 'Fasilitas' : 'Lokasi' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Jam Operasional Harian</td>
                <td class="meta-value">: 07.00 – 20.00 WIB (13 Jam / 26 Slot)</td>
                <td class="meta-label">
                    {{ $data['metric'] === 'occupancy' ? 'Total Jam Terpakai' : 'Total Insiden Perbaikan' }}
                </td>
                <td class="meta-value">
                    : {{ $data['metric'] === 'occupancy' ? number_format($data['summary']['total_completed_hours'], 2, ',', '.') . ' Jam' : $data['summary']['total_damage_incidents'] . ' Insiden' }}
                </td>
            </tr>
            <tr>
                <td class="meta-label">Waktu Dokumen Dibuat</td>
                <td class="meta-value">: {{ now()->translatedFormat('d F Y H:i:s') }} WIB</td>
                <td class="meta-label">
                    {{ $data['metric'] === 'occupancy' ? 'Rata-rata Okupansi' : 'Status Metrik' }}
                </td>
                <td class="meta-value">
                    : {{ $data['metric'] === 'occupancy' ? ($data['summary']['overall_occupancy_rate'] !== null ? number_format($data['summary']['overall_occupancy_rate'], 2, ',', '.') . '%' : 'N/A') : 'Terverifikasi Log' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            @if ($data['metric'] === 'occupancy')
                @if ($data['group_by'] === 'facility')
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th class="text-center">Nama Fasilitas</th>
                        <th class="text-center" style="width: 100px;">Tipe</th>
                        <th class="text-center">Lokasi / Gedung</th>
                        <th class="text-center" style="width: 110px;">Durasi Selesai</th>
                        <th class="text-center" style="width: 120px;">Kapasitas Aktif</th>
                        <th class="text-center" style="width: 110px;">Tingkat Okupansi</th>
                    </tr>
                @else
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th class="text-center">Lokasi / Gedung</th>
                        <th class="text-center" style="width: 110px;">Jumlah Fasilitas</th>
                        <th class="text-center" style="width: 130px;">Durasi Selesai</th>
                        <th class="text-center" style="width: 140px;">Kapasitas Aktif</th>
                        <th class="text-center" style="width: 130px;">Tingkat Okupansi</th>
                    </tr>
                @endif
            @else
                @if ($data['group_by'] === 'facility')
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th class="text-center">Nama Fasilitas</th>
                        <th class="text-center" style="width: 100px;">Tipe</th>
                        <th class="text-center">Lokasi / Gedung</th>
                        <th class="text-center" style="width: 140px;">Jumlah Insiden Perbaikan</th>
                        <th class="text-center" style="width: 140px;">Insiden Terakhir</th>
                    </tr>
                @else
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th class="text-center">Lokasi / Gedung</th>
                        <th class="text-center" style="width: 120px;">Jumlah Fasilitas</th>
                        <th class="text-center" style="width: 160px;">Jumlah Insiden Perbaikan</th>
                        <th class="text-center" style="width: 160px;">Insiden Terakhir</th>
                    </tr>
                @endif
            @endif
        </thead>
        <tbody>
            @forelse ($data['rows'] as $index => $row)
                @if ($data['metric'] === 'occupancy')
                    @if ($data['group_by'] === 'facility')
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #111111;">{{ $row['name'] }}</td>
                            <td>{{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}</td>
                            <td>{{ $row['location'] }}</td>
                            <td class="text-right">{{ number_format($row['used_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-right">{{ number_format($row['operational_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-center">
                                @if ($row['is_calculable'])
                                    {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                @else
                                    <span style="color: #666666; font-style: italic;">Tidak dapat dihitung</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #111111;">{{ $row['location'] }}</td>
                            <td class="text-center">{{ $row['facility_count'] }} Fasilitas</td>
                            <td class="text-right">{{ number_format($row['used_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-right">{{ number_format($row['operational_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-center">
                                @if ($row['is_calculable'])
                                    {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                @else
                                    <span style="color: #666666; font-style: italic;">Tidak dapat dihitung</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @else
                    @if ($data['group_by'] === 'facility')
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #111111;">{{ $row['name'] }}</td>
                            <td>{{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}</td>
                            <td>{{ $row['location'] }}</td>
                            <td class="text-center" style="font-weight: bold; color: #111111;">
                                {{ $row['incident_count'] }} Kali
                            </td>
                            <td class="text-center">
                                {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #111111;">{{ $row['location'] }}</td>
                            <td class="text-center">{{ $row['facility_count'] }} Fasilitas</td>
                            <td class="text-center" style="font-weight: bold; color: #111111;">
                                {{ $row['incident_count'] }} Kali
                            </td>
                            <td class="text-center">
                                {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endif
                @endif
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #666666;">
                        Tidak ada data yang ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Resvi &bull; Dokumen Resmi Tata Kelola Fasilitas
    </div>
</body>
</html>
