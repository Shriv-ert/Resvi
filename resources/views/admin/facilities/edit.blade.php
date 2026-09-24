@extends('layouts.admin')

@section('title', 'Edit Fasilitas: ' . $facility->name)

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-slate-900 transition">Kelola Fasilitas</a>
        <span>/</span>
        <a href="{{ route('admin.facilities.show', $facility) }}" class="hover:text-slate-900 transition">{{ $facility->name }}</a>
        <span>/</span>
        <span class="text-slate-900">Edit Data Master</span>
    </nav>

    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Ubah Data Master Fasilitas</h1>
        <p class="mt-1 text-sm text-slate-600">
            Perbarui spesifikasi nama, tipe, kapasitas, lokasi, atau foto fasilitas.
        </p>
    </div>

    <!-- Notice Callout -->
    <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4 text-sm text-sky-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="text-sky-700 font-bold text-base leading-none">ℹ</span>
            <div class="text-xs leading-5">
                <span class="font-bold">Ketentuan Perubahan:</span>
                Pembaruan data master <strong>tidak mengubah</strong> status operasional fasilitas. Fasilitas yang sedang dalam perbaikan akan tetap berstatus perbaikan.
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.facilities.update', $facility) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900">
                    Nama Fasilitas <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $facility->name) }}"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid: Type & Location -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-900">
                        Tipe Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="type"
                        name="type"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                        @foreach (\App\Enums\FacilityType::cases() as $typeCase)
                            <option value="{{ $typeCase->value }}" {{ old('type', $facility->type->value) === $typeCase->value ? 'selected' : '' }}>
                                {{ $typeCase->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-slate-900">
                        Lokasi / Gedung <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="{{ old('location', $facility->location) }}"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                    @error('location')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity -->
            <div>
                <label for="capacity" class="block text-sm font-semibold text-slate-900">
                    Kapasitas (Orang)
                </label>
                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="{{ old('capacity', $facility->capacity) }}"
                    min="1"
                    placeholder="Kosongkan jika tipe Alat"
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                <p id="capacityHelpText" class="mt-1.5 text-xs text-slate-500">
                    Boleh dikosongkan khusus untuk objek yang tidak memiliki kapasitas orang (seperti alat).
                </p>
                @error('capacity')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900">
                    Deskripsi Lengkap Fasilitas <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >{{ old('description', $facility->description) }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Upload with Preview -->
            <div>
                <label class="block text-sm font-semibold text-slate-900">
                    Ganti Foto Fasilitas
                </label>
                <div class="mt-2 flex items-center gap-4">
                    @php
                        $primaryPhoto = $facility->photos->firstWhere('is_primary', true) ?? $facility->photos->first();
                    @endphp
                    <div id="previewContainer" class="{{ $primaryPhoto ? '' : 'hidden' }}">
                        <img
                            id="imagePreview"
                            src="{{ $primaryPhoto ? asset('storage/' . $primaryPhoto->photo_path) : '#' }}"
                            alt="Foto Saat Ini"
                            class="h-20 w-20 rounded-xl object-cover border border-slate-200 shadow-sm"
                        >
                    </div>
                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2.5 file:text-xs file:font-semibold file:text-sky-700 hover:file:bg-sky-100 transition cursor-pointer"
                    >
                </div>
                <p class="mt-1.5 text-xs text-slate-500">Biarkan kosong jika tidak ingin mengganti foto saat ini.</p>
                @error('photo')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a
                    href="{{ route('admin.facilities.show', $facility) }}"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-xl bg-sky-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('photo');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');

        if (photoInput) {
            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
@endsection
