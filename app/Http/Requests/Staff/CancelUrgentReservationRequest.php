<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CancelUrgentReservationRequest extends FormRequest
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
            'cancellation_reason' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Alasan pembatalan mendesak wajib diisi.',
            'cancellation_reason.min' => 'Alasan pembatalan mendesak minimal 3 karakter.',
            'cancellation_reason.max' => 'Alasan pembatalan mendesak maksimal 1000 karakter.',
        ];
    }
}
