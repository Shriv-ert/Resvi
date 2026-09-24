<?php

namespace App\Models;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'report_number',
    'reporter_id',
    'facility_id',
    'category',
    'description',
    'photo_path',
    'status',
    'resolution_notes',
    'rejection_reason',
    'processed_by_id',
    'processed_at',
    'resolved_by_id',
    'resolved_at',
    'rejected_by_id',
    'rejected_at',
])]
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory, HasUuids;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ReportCategory::class,
            'status' => ReportStatus::class,
            'processed_at' => 'datetime',
            'resolved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    /**
     * Accessor for facility_name (ensuring backward compatibility with blade views).
     */
    public function getFacilityNameAttribute(): ?string
    {
        return $this->facility?->name;
    }

    /**
     * Accessor for facility_location.
     */
    public function getFacilityLocationAttribute(): ?string
    {
        return $this->facility?->location;
    }

    /**
     * Accessor for facility_type.
     */
    public function getFacilityTypeAttribute(): ?string
    {
        return $this->facility?->type?->value;
    }

    /**
     * Accessor for photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }
}
