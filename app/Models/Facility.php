<?php

namespace App\Models;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'name',
    'type',
    'location',
    'capacity',
    'description',
    'is_active',
    'operational_status',
])]
class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => FacilityType::class,
            'operational_status' => OperationalStatus::class,
            'is_active' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FacilityPhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(FacilityPhoto::class)->where('is_primary', true);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(FacilityStatusLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if (empty($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $photo = $this->primaryPhoto ?? $this->photos?->first();
        if ($photo) {
            return $photo->url;
        }

        $type = $this->type instanceof \BackedEnum ? $this->type->value : $this->type;
        return match ($type) {
            'kelas' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
            'laboratorium' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop&q=80',
            'aula' => 'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?w=800&auto=format&fit=crop&q=80',
            'alat' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800&auto=format&fit=crop&q=80',
            'lapangan' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&auto=format&fit=crop&q=80',
            default => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&auto=format&fit=crop&q=80',
        };
    }
}
