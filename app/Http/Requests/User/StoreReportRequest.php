<?php

namespace App\Http\Requests\User;

use App\Enums\ReportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
            'category' => [
                'required',
                Rule::enum(ReportCategory::class),
            ],
            'description' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facility_id.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid atau tidak aktif.',
            'category.required' => 'Kategori masalah wajib dipilih.',
            'category.enum' => 'Kategori masalah yang dipilih tidak valid.',
            'description.required' => 'Deskripsi masalah wajib diisi.',
            'description.min' => 'Deskripsi masalah minimal berisi 5 karakter.',
            'photo.image' => 'File lampiran harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WEBP.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
