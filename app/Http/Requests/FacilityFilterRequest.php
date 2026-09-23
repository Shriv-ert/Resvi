<?php

namespace App\Http\Requests;

use App\Enums\FacilityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FacilityFilterRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', new Enum(FacilityType::class)],
            'location' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'q.max' => 'Kata kunci pencarian tidak boleh lebih dari :max karakter.',
            'type.Illuminate\Validation\Rules\Enum' => 'Tipe fasilitas yang dipilih tidak valid.',
            'location.max' => 'Filter lokasi tidak boleh lebih dari :max karakter.',
            'capacity.integer' => 'Kapasitas minimal harus berupa bilangan bulat.',
            'capacity.min' => 'Kapasitas minimal tidak boleh kurang dari :min.',
            'capacity.max' => 'Kapasitas minimal tidak boleh lebih dari :max.',
        ];
    }
}
