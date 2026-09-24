<?php

namespace App\Http\Requests\User;

use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'facility_id' => [
                'required',
                'uuid',
                Rule::exists('facilities', 'id')->where('is_active', true),
            ],
            'date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
                'regex:/^(0[7-9]|1[0-9]|20):(00|30)$/',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                'regex:/^(0[7-9]|1[0-9]|20):(00|30)$/',
            ],
            'purpose' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $facility = Facility::find($this->input('facility_id'));
                if ($facility && $facility->operational_status->value === 'dalam_perbaikan') {
                    $validator->errors()->add('facility_id', 'Fasilitas ini sedang dalam perbaikan dan tidak dapat direservasi.');
                    return;
                }

                $date = $this->input('date');
                $startTime = $this->input('start_time');
                $endTime = $this->input('end_time');

                $today = now()->toDateString();
                if ($date < $today) {
                    $validator->errors()->add('date', 'Tanggal reservasi tidak boleh di masa lalu.');
                    return;
                }

                if ($date === $today) {
                    $nowTime = now()->format('H:i');
                    if ($startTime <= $nowTime) {
                        $validator->errors()->add('start_time', 'Waktu mulai reservasi harus lebih besar dari waktu saat ini.');
                        return;
                    }
                }

                if ($startTime < '07:00' || $startTime >= '20:00') {
                    $validator->errors()->add('start_time', 'Waktu mulai harus berada dalam jam operasional (07:00 - 20:00).');
                }

                if ($endTime <= '07:00' || $endTime > '20:00') {
                    $validator->errors()->add('end_time', 'Waktu selesai harus berada dalam jam operasional (07:00 - 20:00).');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facility_id.required' => 'Fasilitas wajib dipilih.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid atau tidak aktif.',
            'date.required' => 'Tanggal penggunaan wajib diisi.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'start_time.required' => 'Waktu mulai wajib dipilih.',
            'start_time.regex' => 'Waktu mulai harus berada di antara 07:00 - 20:00 dengan slot kelipatan 30 menit.',
            'end_time.required' => 'Waktu selesai wajib dipilih.',
            'end_time.regex' => 'Waktu selesai harus berada di antara 07:00 - 20:00 dengan slot kelipatan 30 menit.',
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'purpose.required' => 'Tujuan penggunaan fasilitas wajib diisi.',
            'purpose.min' => 'Tujuan penggunaan minimal berisi 5 karakter.',
        ];
    }
}
