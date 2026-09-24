<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CancelReservationRequest extends FormRequest
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
            'cancellation_reason' => [
                'required',
                'string',
                'min:3',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Alasan pembatalan reservasi wajib diisi.',
            'cancellation_reason.min' => 'Alasan pembatalan minimal 3 karakter.',
            'cancellation_reason.max' => 'Alasan pembatalan maksimal 1000 karakter.',
        ];
    }
}
