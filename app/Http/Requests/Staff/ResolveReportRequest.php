<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class ResolveReportRequest extends FormRequest
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
            'resolution_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Catatan resolusi penanganan laporan wajib diisi.',
            'resolution_notes.min' => 'Catatan resolusi minimal 5 karakter.',
            'resolution_notes.max' => 'Catatan resolusi maksimal 2000 karakter.',
        ];
    }
}
