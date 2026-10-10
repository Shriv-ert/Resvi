<?php

namespace App\Http\Requests;

use App\Rules\HalfHourSlotRule;
use App\Rules\OperationalHoursRule;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'facility_id' => [
                'required',
                'uuid',
                Rule::exists('facilities', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
                new OperationalHoursRule(),
                new HalfHourSlotRule(),
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                new OperationalHoursRule(),
                new HalfHourSlotRule(),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('date')) {
                return;
            }

            if ($this->filled(['date', 'start_time'])) {
                try {
                    $now = CarbonImmutable::now();
                    $today = $now->toDateString();

                    if ($this->date === $today) {
                        $startDateTime = CarbonImmutable::createFromFormat(
                            'Y-m-d H:i',
                            "{$this->date} {$this->start_time}"
                        );

                        if ($startDateTime && $startDateTime->lessThanOrEqualTo($now)) {
                            $validator->errors()->add(
                                'start_time',
                                'Waktu mulai harus lebih besar dari waktu saat ini.'
                            );
                        }
                    }
                } catch (\Throwable) {
                    // Penanganan error format ditangani oleh rule validasi date_format
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facility_id.required' => 'Fasilitas wajib dipilih.',
            'facility_id.uuid' => 'ID fasilitas tidak valid.',
            'facility_id.exists' => 'Fasilitas tidak ditemukan atau sedang dinonaktifkan.',
            'date.required' => 'Tanggal penggunaan wajib diisi.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'date.after_or_equal' => 'Tanggal penggunaan tidak boleh di masa lampau.',
            'start_time.required' => 'Waktu mulai wajib ditentukan.',
            'start_time.date_format' => 'Format waktu mulai harus HH:mm.',
            'end_time.required' => 'Waktu selesai wajib ditentukan.',
            'end_time.date_format' => 'Format waktu selesai harus HH:mm.',
            'end_time.after' => 'Waktu selesai harus lebih besar daripada waktu mulai.',
        ];
    }
}
