<?php

namespace App\Http\Requests\Staff;

use App\Enums\OperationalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOperationalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPetugas();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'operational_status' => ['required', Rule::enum(OperationalStatus::class)],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
            'report_id' => ['nullable', 'uuid', 'exists:reports,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'operational_status.required' => 'Status operasional baru wajib dipilih.',
            'operational_status.enum' => 'Status operasional tidak valid.',
            'reason.required' => 'Alasan perubahan status operasional wajib diisi.',
            'reason.min' => 'Alasan perubahan minimal 3 karakter.',
            'reason.max' => 'Alasan perubahan maksimal 1000 karakter.',
            'report_id.exists' => 'Laporan yang ditautkan tidak ditemukan.',
        ];
    }
}
