<?php

namespace App\Services;

use App\Enums\FacilityStatusChangeType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\FacilityStatusLog;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class RecapService
{
    /**
     * Standard operating hours per day: 07:00 to 20:00 = 13.0 hours.
     */
    public const float DAILY_OPERATING_HOURS = 13.0;

    /**
     * Calculate and retrieve comprehensive recap data for the specified month, year, metric, and grouping.
     *
     * @return array{
     *     period: array{month: int, year: int, month_name: string, days_in_month: int, start_date: string, end_date: string},
     *     metric: string,
     *     group_by: string,
     *     summary: array{total_items: int, total_completed_hours: float, overall_occupancy_rate: ?float, total_damage_incidents: int},
     *     rows: Collection<int, array<string, mixed>>
     * }
     */
    public function getRecapData(int $month, int $year, string $metric = 'occupancy', string $groupBy = 'facility'): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $period = [
            'month' => $month,
            'year' => $year,
            'month_name' => $startDate->locale('id')->translatedFormat('F'),
            'days_in_month' => $daysInMonth,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ];

        $facilities = Facility::query()->orderBy('location')->orderBy('name')->get();

        if ($metric === 'occupancy') {
            return $this->buildOccupancyRecap($facilities, $period, $groupBy, $startDate, $endDate);
        }

        return $this->buildDamageFrequencyRecap($facilities, $period, $groupBy, $startDate, $endDate);
    }

    /**
     * Build occupancy recap per facility or per location.
     *
     * @param  Collection<int, Facility>  $facilities
     * @return array<string, mixed>
     */
    private function buildOccupancyRecap(
        Collection $facilities,
        array $period,
        string $groupBy,
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        // Query completed reservations duration per facility directly in MySQL
        $facilityUsageMap = Reservation::query()
            ->where('status', ReservationStatus::Selesai)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $period['end_date'])
            ->selectRaw('facility_id, SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time))) / 3600 as used_hours')
            ->groupBy('facility_id')
            ->pluck('used_hours', 'facility_id')
            ->map(fn ($hours) => round((float) $hours, 2))
            ->all();

        // Query status logs for active master state transitions
        $statusLogs = FacilityStatusLog::query()
            ->where('change_type', FacilityStatusChangeType::IsActive)
            ->orderBy('changed_at')
            ->get()
            ->groupBy('facility_id');

        $facilityRows = $facilities->map(function (Facility $facility) use ($facilityUsageMap, $statusLogs, $period, $startDate): array {
            $facilityId = (string) $facility->id;
            $usedHours = round($facilityUsageMap[$facilityId] ?? 0.0, 2);

            // Determine active days count in month
            $activeDays = $this->calculateActiveDaysInMonth(
                facility: $facility,
                logs: $statusLogs->get($facilityId, collect()),
                month: $period['month'],
                year: $period['year'],
                daysInMonth: $period['days_in_month'],
                startDate: $startDate
            );

            $operationalHours = round($activeDays * self::DAILY_OPERATING_HOURS, 2);
            $isCalculable = $operationalHours > 0;
            $occupancyRate = $isCalculable ? round(($usedHours / $operationalHours) * 100, 2) : null;

            return [
                'facility_id' => $facilityId,
                'name' => $facility->name,
                'type' => $facility->type,
                'location' => $facility->location,
                'is_active' => $facility->is_active,
                'operational_status' => $facility->operational_status,
                'active_days' => $activeDays,
                'operational_hours' => $operationalHours,
                'used_hours' => $usedHours,
                'is_calculable' => $isCalculable,
                'occupancy_rate' => $occupancyRate,
            ];
        });

        if ($groupBy === 'location') {
            $locationRows = $facilityRows->groupBy('location')->map(function (Collection $items, string $location): array {
                $facilityCount = $items->count();
                $totalOperationalHours = round($items->sum('operational_hours'), 2);
                $totalUsedHours = round($items->sum('used_hours'), 2);
                $isCalculable = $totalOperationalHours > 0;
                $occupancyRate = $isCalculable ? round(($totalUsedHours / $totalOperationalHours) * 100, 2) : null;

                return [
                    'location' => $location,
                    'facility_count' => $facilityCount,
                    'operational_hours' => $totalOperationalHours,
                    'used_hours' => $totalUsedHours,
                    'is_calculable' => $isCalculable,
                    'occupancy_rate' => $occupancyRate,
                ];
            })->values();

            $totalOperational = round($locationRows->sum('operational_hours'), 2);
            $totalUsed = round($locationRows->sum('used_hours'), 2);
            $overallOccupancy = $totalOperational > 0 ? round(($totalUsed / $totalOperational) * 100, 2) : null;

            return [
                'period' => $period,
                'metric' => 'occupancy',
                'group_by' => 'location',
                'summary' => [
                    'total_items' => $locationRows->count(),
                    'total_completed_hours' => $totalUsed,
                    'overall_occupancy_rate' => $overallOccupancy,
                    'total_damage_incidents' => 0,
                ],
                'rows' => $locationRows,
            ];
        }

        $totalOperational = round($facilityRows->sum('operational_hours'), 2);
        $totalUsed = round($facilityRows->sum('used_hours'), 2);
        $overallOccupancy = $totalOperational > 0 ? round(($totalUsed / $totalOperational) * 100, 2) : null;

        return [
            'period' => $period,
            'metric' => 'occupancy',
            'group_by' => 'facility',
            'summary' => [
                'total_items' => $facilityRows->count(),
                'total_completed_hours' => $totalUsed,
                'overall_occupancy_rate' => $overallOccupancy,
                'total_damage_incidents' => 0,
            ],
            'rows' => $facilityRows,
        ];
    }

    /**
     * Build damage frequency recap per facility or per location.
     *
     * @param  Collection<int, Facility>  $facilities
     * @return array<string, mixed>
     */
    private function buildDamageFrequencyRecap(
        Collection $facilities,
        array $period,
        string $groupBy,
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        // Query operational status transition logs directly aggregated in MySQL
        $damageAggr = FacilityStatusLog::query()
            ->where('change_type', FacilityStatusChangeType::OperationalStatus)
            ->where('old_value', OperationalStatus::DapatDigunakan->value)
            ->where('new_value', OperationalStatus::DalamPerbaikan->value)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->selectRaw('facility_id, COUNT(*) as incident_count, MAX(changed_at) as latest_incident_at')
            ->groupBy('facility_id')
            ->get()
            ->keyBy('facility_id');

        $facilityRows = $facilities->map(function (Facility $facility) use ($damageAggr): array {
            $facilityId = (string) $facility->id;
            $aggr = $damageAggr->get($facilityId);
            $incidentCount = $aggr ? (int) $aggr->incident_count : 0;
            $latestIncident = $aggr?->latest_incident_at;

            return [
                'facility_id' => $facilityId,
                'name' => $facility->name,
                'type' => $facility->type,
                'location' => $facility->location,
                'incident_count' => $incidentCount,
                'latest_incident_at' => $latestIncident ? Carbon::parse($latestIncident) : null,
            ];
        });

        if ($groupBy === 'location') {
            $locationRows = $facilityRows->groupBy('location')->map(function (Collection $items, string $location): array {
                $facilityCount = $items->count();
                $incidentCount = (int) $items->sum('incident_count');
                $latestIncident = $items->pluck('latest_incident_at')->filter()->max();

                return [
                    'location' => $location,
                    'facility_count' => $facilityCount,
                    'incident_count' => $incidentCount,
                    'latest_incident_at' => $latestIncident ? Carbon::parse($latestIncident) : null,
                ];
            })->values();

            return [
                'period' => $period,
                'metric' => 'damage_frequency',
                'group_by' => 'location',
                'summary' => [
                    'total_items' => $locationRows->count(),
                    'total_completed_hours' => 0.0,
                    'overall_occupancy_rate' => null,
                    'total_damage_incidents' => (int) $locationRows->sum('incident_count'),
                ],
                'rows' => $locationRows,
            ];
        }

        return [
            'period' => $period,
            'metric' => 'damage_frequency',
            'group_by' => 'facility',
            'summary' => [
                'total_items' => $facilityRows->count(),
                'total_completed_hours' => 0.0,
                'overall_occupancy_rate' => null,
                'total_damage_incidents' => (int) $facilityRows->sum('incident_count'),
            ],
            'rows' => $facilityRows,
        ];
    }

    /**
     * Calculate how many days a facility was active during the specified month.
     *
     * @param  Collection<int, FacilityStatusLog>  $logs
     */
    private function calculateActiveDaysInMonth(
        Facility $facility,
        Collection $logs,
        int $month,
        int $year,
        int $daysInMonth,
        Carbon $startDate,
    ): int {
        // If facility has no status logs, fall back to its created_at and current is_active status
        if ($logs->isEmpty()) {
            if (! $facility->is_active) {
                return 0;
            }

            // If created after this month, it had 0 active days
            if ($facility->created_at && $facility->created_at->isAfter($startDate->copy()->endOfMonth())) {
                return 0;
            }

            // If created during this month
            if ($facility->created_at && $facility->created_at->year === $year && $facility->created_at->month === $month) {
                return max(0, $daysInMonth - $facility->created_at->day + 1);
            }

            return $daysInMonth;
        }

        $firstLog = $logs->first();
        $birthDate = $firstLog ? Carbon::parse($firstLog->changed_at) : $facility->created_at;

        // Evaluate daily status for each day of the month at midday (12:00:00)
        $activeDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $samplePoint = Carbon::createFromDate($year, $month, $day)->setTime(12, 0, 0);

            // If facility was registered after this sample point, it did not exist yet
            if ($birthDate && $birthDate->isAfter($samplePoint)) {
                continue;
            }

            // Check latest log up to samplePoint
            $latestLog = $logs->filter(fn (FacilityStatusLog $l) => Carbon::parse($l->changed_at)->lte($samplePoint))->last();

            if ($latestLog) {
                if ($latestLog->new_value === '1' || $latestLog->new_value === 'true') {
                    $activeDays++;
                }
            } elseif ($facility->is_active) {
                $activeDays++;
            }
        }

        return $activeDays;
    }

    /**
     * Export recap data as streaming CSV with UTF-8 BOM.
     */
    public function exportCsv(array $data): StreamedResponse
    {
        $filename = sprintf(
            'rekap_%s_%s_%s_%d.csv',
            $data['metric'],
            $data['group_by'],
            strtolower($data['period']['month_name']),
            $data['period']['year']
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($data): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Metadata info
            fputcsv($handle, ['LAPORAN REKAPITULASI — RESVI']);
            fputcsv($handle, ['Periode', sprintf('%s %d', $data['period']['month_name'], $data['period']['year'])]);
            fputcsv($handle, ['Metrik', $data['metric'] === 'occupancy' ? 'Tingkat Okupansi' : 'Frekuensi Kerusakan']);
            fputcsv($handle, ['Pengelompokan', $data['group_by'] === 'facility' ? 'Per Fasilitas' : 'Per Lokasi']);
            fputcsv($handle, ['Waktu Ekspor', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []); // empty line

            if ($data['metric'] === 'occupancy') {
                if ($data['group_by'] === 'facility') {
                    fputcsv($handle, ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Durasi Terpakai (Jam)', 'Kapasitas Operasional (Jam)', 'Tingkat Okupansi']);
                    $no = 1;
                    foreach ($data['rows'] as $row) {
                        fputcsv($handle, [
                            $no++,
                            $row['name'],
                            $row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type'],
                            $row['location'],
                            number_format($row['used_hours'], 2, ',', '.'),
                            number_format($row['operational_hours'], 2, ',', '.'),
                            $row['is_calculable'] ? number_format($row['occupancy_rate'], 2, ',', '.').'%' : 'Tidak dapat dihitung',
                        ]);
                    }
                } else {
                    fputcsv($handle, ['No', 'Lokasi / Gedung', 'Jumlah Fasilitas', 'Durasi Terpakai (Jam)', 'Kapasitas Operasional (Jam)', 'Tingkat Okupansi']);
                    $no = 1;
                    foreach ($data['rows'] as $row) {
                        fputcsv($handle, [
                            $no++,
                            $row['location'],
                            $row['facility_count'],
                            number_format($row['used_hours'], 2, ',', '.'),
                            number_format($row['operational_hours'], 2, ',', '.'),
                            $row['is_calculable'] ? number_format($row['occupancy_rate'], 2, ',', '.').'%' : 'Tidak dapat dihitung',
                        ]);
                    }
                }
            } else {
                if ($data['group_by'] === 'facility') {
                    fputcsv($handle, ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Jumlah Insiden Perbaikan', 'Insiden Terakhir']);
                    $no = 1;
                    foreach ($data['rows'] as $row) {
                        fputcsv($handle, [
                            $no++,
                            $row['name'],
                            $row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type'],
                            $row['location'],
                            $row['incident_count'],
                            $row['latest_incident_at'] ? Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-',
                        ]);
                    }
                } else {
                    fputcsv($handle, ['No', 'Lokasi / Gedung', 'Jumlah Fasilitas', 'Jumlah Insiden Perbaikan', 'Insiden Terakhir']);
                    $no = 1;
                    foreach ($data['rows'] as $row) {
                        fputcsv($handle, [
                            $no++,
                            $row['location'],
                            $row['facility_count'],
                            $row['incident_count'],
                            $row['latest_incident_at'] ? Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-',
                        ]);
                    }
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export recap data as native Microsoft Excel (.xlsx) file using PhpSpreadsheet.
     */
    public function exportExcel(array $data): StreamedResponse
    {
        $filename = sprintf(
            'rekap_%s_%s_%s_%d.xlsx',
            $data['metric'],
            $data['group_by'],
            strtolower($data['period']['month_name']),
            $data['period']['year']
        );

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($data): void {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Rekapitulasi');

            // Header Kop
            $sheet->setCellValue('A1', 'RESVI — LAPORAN REKAPITULASI');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('000000');

            $sheet->setCellValue('A2', sprintf('Periode: %s %d', $data['period']['month_name'], $data['period']['year']));
            $sheet->setCellValue('A3', sprintf('Metrik: %s | Pengelompokan: %s', $data['metric'] === 'occupancy' ? 'Tingkat Okupansi' : 'Frekuensi Kerusakan', $data['group_by'] === 'facility' ? 'Per Fasilitas' : 'Per Lokasi'));
            $sheet->setCellValue('A4', sprintf('Waktu Ekspor: %s', now()->format('d/m/Y H:i:s')));
            $sheet->getStyle('A2:A4')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('555555');

            $startRow = 6;

            if ($data['metric'] === 'occupancy') {
                if ($data['group_by'] === 'facility') {
                    $tableHeaders = ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Durasi Terpakai (Jam)', 'Kapasitas Operasional (Jam)', 'Tingkat Okupansi'];
                } else {
                    $tableHeaders = ['No', 'Lokasi / Gedung', 'Jumlah Fasilitas', 'Durasi Terpakai (Jam)', 'Kapasitas Operasional (Jam)', 'Tingkat Okupansi'];
                }
            } else {
                if ($data['group_by'] === 'facility') {
                    $tableHeaders = ['No', 'Nama Fasilitas', 'Tipe', 'Lokasi', 'Jumlah Insiden Perbaikan', 'Insiden Terakhir'];
                } else {
                    $tableHeaders = ['No', 'Lokasi / Gedung', 'Jumlah Fasilitas', 'Jumlah Insiden Perbaikan', 'Insiden Terakhir'];
                }
            }

            // Write Table Header
            $colLetter = 'A';
            foreach ($tableHeaders as $headerText) {
                $sheet->setCellValue($colLetter.$startRow, $headerText);
                $colLetter++;
            }

            $lastCol = chr(ord('A') + count($tableHeaders) - 1);
            $headerRange = "A{$startRow}:{$lastCol}{$startRow}";
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('222222');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Write Rows
            $currentRow = $startRow + 1;
            $no = 1;

            foreach ($data['rows'] as $row) {
                if ($data['metric'] === 'occupancy') {
                    if ($data['group_by'] === 'facility') {
                        $sheet->setCellValue('A'.$currentRow, $no++);
                        $sheet->setCellValue('B'.$currentRow, $row['name']);
                        $sheet->setCellValue('C'.$currentRow, ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']));
                        $sheet->setCellValue('D'.$currentRow, $row['location']);
                        $sheet->setCellValue('E'.$currentRow, $row['used_hours']);
                        $sheet->setCellValue('F'.$currentRow, $row['operational_hours']);
                        $sheet->setCellValue('G'.$currentRow, $row['is_calculable'] ? ($row['occupancy_rate'] / 100) : 'Tidak dapat dihitung');
                    } else {
                        $sheet->setCellValue('A'.$currentRow, $no++);
                        $sheet->setCellValue('B'.$currentRow, $row['location']);
                        $sheet->setCellValue('C'.$currentRow, $row['facility_count']);
                        $sheet->setCellValue('D'.$currentRow, $row['used_hours']);
                        $sheet->setCellValue('E'.$currentRow, $row['operational_hours']);
                        $sheet->setCellValue('F'.$currentRow, $row['is_calculable'] ? ($row['occupancy_rate'] / 100) : 'Tidak dapat dihitung');
                    }
                } else {
                    if ($data['group_by'] === 'facility') {
                        $sheet->setCellValue('A'.$currentRow, $no++);
                        $sheet->setCellValue('B'.$currentRow, $row['name']);
                        $sheet->setCellValue('C'.$currentRow, ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']));
                        $sheet->setCellValue('D'.$currentRow, $row['location']);
                        $sheet->setCellValue('E'.$currentRow, $row['incident_count']);
                        $sheet->setCellValue('F'.$currentRow, $row['latest_incident_at'] ? Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-');
                    } else {
                        $sheet->setCellValue('A'.$currentRow, $no++);
                        $sheet->setCellValue('B'.$currentRow, $row['location']);
                        $sheet->setCellValue('C'.$currentRow, $row['facility_count']);
                        $sheet->setCellValue('D'.$currentRow, $row['incident_count']);
                        $sheet->setCellValue('E'.$currentRow, $row['latest_incident_at'] ? Carbon::parse($row['latest_incident_at'])->format('d/m/Y H:i') : '-');
                    }
                }
                $currentRow++;
            }

            $lastDataRow = $currentRow - 1;

            if ($lastDataRow >= $startRow + 1) {
                // Formatting borders
                $tableRange = "A{$startRow}:{$lastCol}{$lastDataRow}";
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

                // Data rows: explicit dark text, alternating white and subtle low-opacity gray background
                for ($r = $startRow + 1; $r <= $lastDataRow; $r++) {
                    $rowRange = "A{$r}:{$lastCol}{$r}";
                    $sheet->getStyle($rowRange)->getFont()->getColor()->setRGB('111111');

                    $bgColor = ($r % 2 === 0) ? 'F8F9FA' : 'FFFFFF';
                    $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
                }

                // Center align No column
                $sheet->getStyle('A'.($startRow + 1).":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Format numbers & percentages for occupancy (clean percentage, no highlight fill)
                if ($data['metric'] === 'occupancy') {
                    if ($data['group_by'] === 'facility') {
                        $sheet->getStyle('E'.($startRow + 1).":F{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle('G'.($startRow + 1).":G{$lastDataRow}")->getNumberFormat()->setFormatCode('0.00%');
                        $sheet->getStyle('G'.($startRow + 1).":G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } else {
                        $sheet->getStyle('D'.($startRow + 1).":E{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle('F'.($startRow + 1).":F{$lastDataRow}")->getNumberFormat()->setFormatCode('0.00%');
                        $sheet->getStyle('F'.($startRow + 1).":F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                } else {
                    if ($data['group_by'] === 'facility') {
                        $sheet->getStyle('E'.($startRow + 1).":F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } else {
                        $sheet->getStyle('C'.($startRow + 1).":E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            }

            // Auto-size all columns
            for ($col = 'A'; $col <= $lastCol; $col++) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }

    /**
     * Export recap data as official PDF document using Dompdf.
     */
    public function exportPdf(array $data): Response
    {
        $filename = sprintf(
            'rekap_%s_%s_%s_%d.pdf',
            $data['metric'],
            $data['group_by'],
            strtolower($data['period']['month_name']),
            $data['period']['year']
        );

        $pdf = Pdf::loadView('admin.recaps.pdf', [
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
