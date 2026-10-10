<?php

namespace App\Http\Requests\Admin;

use App\Enums\FacilityType;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('type') === 'alat' && $this->input('capacity') === '') {
            $this->merge(['capacity' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(FacilityType::class)],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.max' => 'Nama fasilitas maksimal 255 karakter.',
            'type.required' => 'Tipe fasilitas wajib dipilih.',
            'type.enum' => 'Tipe fasilitas yang dipilih tidak valid.',
            'location.required' => 'Lokasi fasilitas wajib diisi.',
            'location.max' => 'Lokasi fasilitas maksimal 255 karakter.',
            'capacity.integer' => 'Kapasitas harus berupa angka bulat.',
            'capacity.min' => 'Kapasitas harus lebih besar dari 0.',
            'description.required' => 'Deskripsi fasilitas wajib diisi.',
            'description.max' => 'Deskripsi fasilitas maksimal 5000 karakter.',
            'photo.image' => 'Berkas foto harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 1 MB.',
        ];
    }
}
