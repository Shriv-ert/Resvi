<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
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
        $role = $this->input('role');
        $category = $this->input('user_category') ?? $this->input('category');

        $this->merge([
            'user_category' => $role === 'petugas' ? null : $category,
        ]);
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['petugas', 'pengguna'])],
            'user_category' => [
                'required_if:role,pengguna',
                'nullable',
                Rule::in(['mahasiswa', 'dosen', 'staf']),
            ],
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
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'role.required' => 'Peran akun wajib dipilih.',
            'role.in' => 'Peran yang dipilih hanya boleh Petugas atau Pengguna.',
            'user_category.required_if' => 'Kategori pengguna wajib dipilih.',
            'user_category.in' => 'Kategori pengguna harus salah satu dari: mahasiswa, dosen, atau staf.',
        ];
    }
}
