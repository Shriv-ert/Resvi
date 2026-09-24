<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffReportService
{
    /**
     * Get active queue: Baru, Diproses.
     * Ordered by: status group (Baru first, Diproses second), then created_at ASC (FIFO).
     */
    public function getActiveQueue(?string $search = null, ?string $type = null, ?string $status = null): Collection
    {
        $query = Report::query()
            ->with(['facility', 'reporter', 'processedBy'])
            ->whereIn('status', [
                ReportStatus::Baru,
                ReportStatus::Diproses,
            ]);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('reporter', fn ($rq) => $rq->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('facility', fn ($fq) => $fq->where('name', 'like', "%{$term}%"));
            });
        }

        if (! empty($type)) {
            $query->whereHas('facility', fn ($fq) => $fq->where('type', $type));
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query
            ->orderByRaw("FIELD(status, 'baru', 'diproses') ASC")
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get final reports history: Selesai, Ditolak.
     * Ordered by: created_at ASC (FIFO).
     */
    public function getHistory(?string $search = null, ?string $type = null, ?string $status = null): Collection
    {
        $query = Report::query()
            ->with(['facility', 'reporter', 'processedBy', 'resolvedBy', 'rejectedBy'])
            ->whereIn('status', [
                ReportStatus::Selesai,
                ReportStatus::Ditolak,
            ]);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('reporter', fn ($rq) => $rq->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('facility', fn ($fq) => $fq->where('name', 'like', "%{$term}%"));
            });
        }

        if (! empty($type)) {
            $query->whereHas('facility', fn ($fq) => $fq->where('type', $type));
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Start processing a new report (FR-PTG-07 & BR-PTG-04).
     */
    public function startProcess(Report $report, string $staffId): Report
    {
        return DB::transaction(function () use ($report, $staffId): Report {
            $lockedReport = Report::query()
                ->where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReport->status !== ReportStatus::Baru) {
                throw ValidationException::withMessages([
                    'start_process' => 'Hanya laporan berstatus Baru yang dapat diproses.',
                ]);
            }

            $lockedReport->update([
                'status' => ReportStatus::Diproses,
                'processed_by_id' => $staffId,
                'processed_at' => now(),
            ]);

            return $lockedReport->fresh(['facility', 'reporter', 'processedBy']);
        });
    }

    /**
     * Resolve a processing report (FR-PTG-08 & BR-PTG-04).
     */
    public function resolve(Report $report, string $resolutionNotes, string $staffId): Report
    {
        return DB::transaction(function () use ($report, $resolutionNotes, $staffId): Report {
            $lockedReport = Report::query()
                ->where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReport->status !== ReportStatus::Diproses) {
                throw ValidationException::withMessages([
                    'resolve' => 'Hanya laporan yang berstatus Diproses yang dapat diselesaikan.',
                ]);
            }

            $lockedReport->update([
                'status' => ReportStatus::Selesai,
                'resolution_notes' => trim($resolutionNotes),
                'resolved_by_id' => $staffId,
                'resolved_at' => now(),
            ]);

            return $lockedReport->fresh(['facility', 'reporter', 'resolvedBy']);
        });
    }

    /**
     * Reject a processing report (FR-PTG-09 & BR-PTG-04).
     */
    public function reject(Report $report, string $rejectionReason, string $staffId): Report
    {
        return DB::transaction(function () use ($report, $rejectionReason, $staffId): Report {
            $lockedReport = Report::query()
                ->where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReport->status !== ReportStatus::Diproses) {
                throw ValidationException::withMessages([
                    'reject' => 'Laporan harus diproses terlebih dahulu sebelum dapat ditolak.',
                ]);
            }

            $lockedReport->update([
                'status' => ReportStatus::Ditolak,
                'rejection_reason' => trim($rejectionReason),
                'rejected_by_id' => $staffId,
                'rejected_at' => now(),
            ]);

            return $lockedReport->fresh(['facility', 'reporter', 'rejectedBy']);
        });
    }
}
