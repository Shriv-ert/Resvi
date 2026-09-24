@extends('layouts.admin')

@section('title', 'Buat Akun Baru')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('admin.accounts.index') }}" class="hover:text-slate-900 transition">Kelola Akun</a>
        <span>/</span>
        <span class="text-slate-900">Buat Akun Baru</span>
    </nav>

    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Akun Baru</h1>
        <p class="mt-1 text-sm text-slate-600">
            Daftarkan akun Petugas atau Pengguna secara langsung. Kata sandi awal akan dibangkitkan secara acak oleh sistem.
        </p>
    </div>

    <!-- Security Info Banner -->
    <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4 text-sm text-sky-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="text-sky-700 font-bold text-base leading-none">ℹ</span>
            <div class="text-xs leading-5">
                <span class="font-bold">Keamanan Kata Sandi Awal:</span>
                Sistem akan membangkitkan kata sandi acak berkeamanan tinggi yang <strong>hanya ditampilkan satu kali</strong> kepada Admin sesaat setelah formulir ini disimpan. Pastikan Anda mencatat dan menyampaikannya kepada pemilik akun.
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6" id="createAccountForm">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: Budi Santoso"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-900">
                    Alamat Email Kampus / Pribadi <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="budi@kampus.ac.id"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selector -->
            <div>
                <label class="block text-sm font-semibold text-slate-900">
                    Peran Akun <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 grid grid-cols-2 gap-3">
                    <label class="relative flex cursor-pointer rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 transition focus-within:ring-2 focus-within:ring-sky-500">
                        <input
                            type="radio"
                            name="role"
                            value="pengguna"
                            class="sr-only role-radio"
                            {{ old('role', 'pengguna') === 'pengguna' ? 'checked' : '' }}
                        >
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-900">Pengguna</span>
                            <span class="text-xs text-slate-500 mt-0.5">Mahasiswa, Dosen, atau Staf (dapat mereservasi & melapor)</span>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 transition focus-within:ring-2 focus-within:ring-sky-500">
                        <input
                            type="radio"
                            name="role"
                            value="petugas"
                            class="sr-only role-radio"
                            {{ old('role') === 'petugas' ? 'checked' : '' }}
                        >
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-900">Petugas</span>
                            <span class="text-xs text-slate-500 mt-0.5">Staf operasional (memproses antrian reservasi & laporan)</span>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Field (Dynamic for Pengguna only) -->
            <div id="categoryWrapper" class="transition-all duration-200">
                <label for="user_category" class="block text-sm font-semibold text-slate-900">
                    Kategori Pengguna <span class="text-red-500">*</span>
                </label>
                <select
                    id="user_category"
                    name="user_category"
                    class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Pilih Kategori Civitas...</option>
                    <option value="mahasiswa" {{ old('user_category') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('user_category') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="staf" {{ old('user_category') === 'staf' ? 'selected' : '' }}>Staf</option>
                </select>
                @error('user_category')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a
                    href="{{ route('admin.accounts.index') }}"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-xl bg-sky-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                >
                    Simpan & Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Vanilla JS untuk toggle dinamis kategori dan styling pilihan radio role
    document.addEventListener('DOMContentLoaded', function () {
        const roleRadios = document.querySelectorAll('.role-radio');
        const categoryWrapper = document.getElementById('categoryWrapper');
        const categorySelect = document.getElementById('user_category');

        function updateRoleSelection() {
            roleRadios.forEach(radio => {
                const parentLabel = radio.closest('label');
                if (radio.checked) {
                    parentLabel.classList.add('border-sky-600', 'bg-sky-50/50', 'ring-2', 'ring-sky-600');
                    parentLabel.classList.remove('border-slate-200');
                } else {
                    parentLabel.classList.remove('border-sky-600', 'bg-sky-50/50', 'ring-2', 'ring-sky-600');
                    parentLabel.classList.add('border-slate-200');
                }
            });

            const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
            if (selectedRole === 'petugas') {
                categoryWrapper.style.display = 'none';
                categorySelect.disabled = true;
                categorySelect.value = '';
            } else {
                categoryWrapper.style.display = 'block';
                categorySelect.disabled = false;
            }
        }

        roleRadios.forEach(radio => {
            radio.addEventListener('change', updateRoleSelection);
        });

        // Initialize state on page load
        updateRoleSelection();
    });
</script>
@endpush
@endsection
