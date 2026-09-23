<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi — PPK Room Reservation</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0369a1;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
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
            color: #64748b;
            font-weight: 600;
            width: 130px;
        }

        .meta-value {
            color: #0f172a;
            font-weight: bold;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background-color: #0369a1;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #0284c7;
            text-align: left;
        }

        table.data-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge-calculable {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .badge-uncalculable {
            display: inline-block;
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            font-style: italic;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .summary-box {
            margin-top: 16px;
            width: 100%;
        }

        .summary-card {
            border: 1px solid #cbd5e1;
            background-color: #f1f5f9;
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-right: 12px;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <!-- Kop Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="header-title">PPK Room Reservation — Laporan Eksekutif</div>
                <div class="header-subtitle">
                    Sistem Tata Kelola Fasilitas Kampus & Pelaporan Operasional Terpadu
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 12px; font-weight: bold; color: #0369a1;">
                    {{ $data['metric'] === 'occupancy' ? 'REKAPITULASI TINGKAT OKUPANSI' : 'REKAPITULASI FREKUENSI KERUSAKAN' }}
                </div>
                <div style="font-size: 10px; color: #64748b;">
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
                        <th>Nama Fasilitas</th>
                        <th style="width: 100px;">Tipe</th>
                        <th>Lokasi / Gedung</th>
                        <th class="text-right" style="width: 110px;">Durasi Selesai</th>
                        <th class="text-right" style="width: 120px;">Kapasitas Aktif</th>
                        <th class="text-center" style="width: 110px;">Tingkat Okupansi</th>
                    </tr>
                @else
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th>Lokasi / Gedung</th>
                        <th class="text-center" style="width: 110px;">Jumlah Fasilitas</th>
                        <th class="text-right" style="width: 130px;">Durasi Selesai</th>
                        <th class="text-right" style="width: 140px;">Kapasitas Aktif</th>
                        <th class="text-center" style="width: 130px;">Tingkat Okupansi</th>
                    </tr>
                @endif
            @else
                @if ($data['group_by'] === 'facility')
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th>Nama Fasilitas</th>
                        <th style="width: 100px;">Tipe</th>
                        <th>Lokasi / Gedung</th>
                        <th class="text-center" style="width: 140px;">Jumlah Insiden Perbaikan</th>
                        <th class="text-center" style="width: 140px;">Insiden Terakhir</th>
                    </tr>
                @else
                    <tr>
                        <th class="text-center" style="width: 35px;">No</th>
                        <th>Lokasi / Gedung</th>
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
                            <td style="font-weight: bold; color: #0f172a;">{{ $row['name'] }}</td>
                            <td>{{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}</td>
                            <td>{{ $row['location'] }}</td>
                            <td class="text-right">{{ number_format($row['used_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-right">{{ number_format($row['operational_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-center">
                                @if ($row['is_calculable'])
                                    <span class="badge-calculable">{{ number_format($row['occupancy_rate'], 2, ',', '.') }}%</span>
                                @else
                                    <span class="badge-uncalculable">Tidak dapat dihitung</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $row['location'] }}</td>
                            <td class="text-center">{{ $row['facility_count'] }} Fasilitas</td>
                            <td class="text-right">{{ number_format($row['used_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-right">{{ number_format($row['operational_hours'], 2, ',', '.') }} Jam</td>
                            <td class="text-center">
                                @if ($row['is_calculable'])
                                    <span class="badge-calculable">{{ number_format($row['occupancy_rate'], 2, ',', '.') }}%</span>
                                @else
                                    <span class="badge-uncalculable">Tidak dapat dihitung</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @else
                    @if ($data['group_by'] === 'facility')
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $row['name'] }}</td>
                            <td>{{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}</td>
                            <td>{{ $row['location'] }}</td>
                            <td class="text-center" style="font-weight: bold; color: {{ $row['incident_count'] > 0 ? '#b91c1c' : '#15803d' }};">
                                {{ $row['incident_count'] }} Kali
                            </td>
                            <td class="text-center">
                                {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $row['location'] }}</td>
                            <td class="text-center">{{ $row['facility_count'] }} Fasilitas</td>
                            <td class="text-center" style="font-weight: bold; color: {{ $row['incident_count'] > 0 ? '#b91c1c' : '#15803d' }};">
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
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data yang ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem PPK Room Reservation &bull; Dokumen Resmi Tata Kelola Fasilitas
    </div>
</body>
</html>
