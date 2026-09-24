<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserReportService
{
    /**
     * Membuat laporan kerusakan fasilitas oleh Pengguna.
     */
    public function createReport(User $user, array $data, ?UploadedFile $photo = null): Report
    {
        $facility = Facility::where('id', $data['facility_id'])->first();

        if (! $facility || ! $facility->is_active) {
            throw ValidationException::withMessages([
                'facility_id' => ['Fasilitas yang dilaporkan tidak valid atau tidak aktif.'],
            ]);
        }

        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('reports', 'public');
        }

        // Generate Nomor Laporan Unik: LAP-YYYYMMDD-XXXX
        $datePrefix = date('Ymd');
        $countToday = Report::whereDate('created_at', today())->count();
        $uniqueCode = str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);
        $reportNumber = "LAP-{$datePrefix}-{$uniqueCode}";

        while (Report::where('report_number', $reportNumber)->exists()) {
            $uniqueCode = str_pad((string) (random_int(1000, 9999)), 4, '0', STR_PAD_LEFT);
            $reportNumber = "LAP-{$datePrefix}-{$uniqueCode}";
        }

        return Report::create([
            'report_number' => $reportNumber,
            'reporter_id' => $user->id,
            'facility_id' => $facility->id,
            'category' => $data['category'],
            'description' => $data['description'],
            'photo_path' => $photoPath,
            'status' => ReportStatus::Baru,
        ]);
    }
}
