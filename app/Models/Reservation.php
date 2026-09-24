<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reservation_number',
    'user_id',
    'facility_id',
    'date',
    'start_time',
    'end_time',
    'purpose',
    'status',
    'rejection_reason',
    'cancellation_reason',
    'cancelled_by_id',
    'expiration_reason',
    'approved_by_id',
    'approved_at',
    'rejected_by_id',
    'rejected_at',
    'cancelled_at',
])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory, HasUuids;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'date' => 'date:Y-m-d',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function getFacilityNameAttribute(): ?string
    {
        return $this->facility?->name;
    }

    public function getFacilityLocationAttribute(): ?string
    {
        return $this->facility?->location;
    }

    public function getFacilityTypeAttribute(): ?string
    {
        return $this->facility?->type?->value;
    }

    public function getApplicantNameAttribute(): ?string
    {
        return $this->user?->name ?? 'Pemohon';
    }

    public function getApplicantEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getApplicantCategoryAttribute(): ?string
    {
        return $this->user?->user_category ? ucfirst($this->user->user_category->value) : 'Civitas';
    }

    public function getStartDateTimeAttribute(): \Carbon\Carbon
    {
        $dateStr = $this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d') : (string) $this->date;
        return \Carbon\Carbon::parse($dateStr . ' ' . $this->start_time);
    }

    public function getEndDateTimeAttribute(): \Carbon\Carbon
    {
        $dateStr = $this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d') : (string) $this->date;
        return \Carbon\Carbon::parse($dateStr . ' ' . $this->end_time);
    }

    /**
     * Evaluasi status otomatis berdasarkan waktu (BR-PGN-05, BR-PTG-02).
     */
    public function evaluateStatus(): self
    {
        $now = now();
        $dateStr = $this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d') : (string) $this->date;
        $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $this->end_time);

        if ($this->status === ReservationStatus::Menunggu && $now->gte($startDateTime)) {
            $this->status = ReservationStatus::Kedaluwarsa;
            $this->expiration_reason = 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.';
            $this->saveQuietly();
        } elseif ($this->status === ReservationStatus::Disetujui && $now->gte($startDateTime) && $now->lt($endDateTime)) {
            $this->status = ReservationStatus::Berlangsung;
            $this->saveQuietly();
        } elseif (($this->status === ReservationStatus::Berlangsung || $this->status === ReservationStatus::Disetujui) && $now->gte($endDateTime)) {
            $this->status = ReservationStatus::Selesai;
            $this->saveQuietly();
        }

        return $this;
    }
}
